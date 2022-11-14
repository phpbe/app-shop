<?php

namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;
use Be\Task\TaskException;
use GeoIp2\Database\Reader;

/**
 * @BeTask("统计 - 访问队列")
 */
class StaticsVisit extends Task
{

    // 每 10 分钟执行一次
    protected $schedule = '*/10 * * * *';

    protected $timeout = 300;

    /**
     * @throws \Be\Runtime\RuntimeException
     * @throws TaskException
     */
    public function execute()
    {
        $redis = Be::getRedis();
        $es = Be::getEs();

        $configEs = Be::getConfig('App.Shop.Es');

        $path = Be::getRuntime()->getRootPath() . '/data/App/Shop/GeoLite2-Country.mmdb';
        if (!file_exists($path)) {
            $path = Be::getProperty('App.Shop')->getPath() . '/GeoLite2-Country.mmdb';
        }
        $reader = new Reader($path);

        $t0 = time();

        $serviceStatistic = Be::getService('App.Shop.Statistic');

        $batch = [];
        while (true) {
            $visit = $redis->rPop('Shop:Statistic:Visit');
            if ($visit === false) {
                break;
            }

            $visit = json_decode($visit, true);
            if ($visit === null) {
                continue;
            }

            $batch[] = [
                'index' => [
                    '_index' => $configEs->indexStatisticVisit
                ]
            ];

            $countryCode = '';
            try {
                $record = $reader->country($visit['ip']);
                $countryCode = $record->country->isoCode;
            } catch (\Throwable $t) {
            }

            $browser = $serviceStatistic->detectUserAgentBrowser($visit['user_agent']);
            $browserWithVersion = $serviceStatistic->detectUserAgentBrowser($visit['user_agent'], true);

            $os = $serviceStatistic->detectUserAgentOs($visit['user_agent']);
            $osWithVersion = $serviceStatistic->detectUserAgentOs($visit['user_agent'], true);

            $refererHost = '';
            if ($visit['referer']) {
                $parsedReferer = parse_url($visit['referer']);
                if ($parsedReferer) {
                    $refererHost = $parsedReferer['host'];
                }
            }

            $batch[] = [
                'user_token' => $visit['user_token'],
                'user_id' => $visit['user_id'],
                'product_id' => $visit['product_id'],
                'is_guest' => $visit['is_guest'],
                'ip' => $visit['ip'],
                'country_code' => $countryCode,
                'is_mobile' => $visit['is_mobile'],
                'url' => $visit['url'],
                'referer' => $refererHost,
                'browser' => $browser,
                'browser_with_version' => $browserWithVersion,
                'os' => $os,
                'os_with_version' => $osWithVersion,
                'create_time' => $visit['create_time'],
            ];

            if (count($batch) > 100) {
                $response = $es->bulk(['body' => $batch]);
                if ($response['errors'] > 0) {
                    $reason = '';
                    if (isset($response['items']) && count($response['items']) > 0) {
                        foreach ($response['items'] as $item) {
                            if (isset($item['index']['error']['reason'])) {
                                $reason = $item['index']['error']['reason'];
                                break;
                            }
                        }
                    }
                    throw new TaskException('访问队列写入ES出错：' . $reason);
                }

                $batch = [];
            }

            $t = time();
            if ($t - $t0 > $this->timeout) {
                break;
            }
        }


        if (count($batch) > 0) {
            $response = $es->bulk(['body' => $batch]);
            if ($response['errors'] > 0) {
                $reason = '';
                if (isset($response['items']) && count($response['items']) > 0) {
                    foreach ($response['items'] as $item) {
                        if (isset($item['index']['error']['reason'])) {
                            $reason = $item['index']['error']['reason'];
                            break;
                        }
                    }
                }
                throw new TaskException('访问队列写入ES出错：' . $reason);
            }
        }

    }

}
