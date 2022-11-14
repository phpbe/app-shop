<?php

namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;
use Be\Task\TaskException;
use GeoIp2\Database\Reader;

/**
 * @BeTask("统计 - 购物车队列")
 */
class StaticsCart extends Task
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

        $t0 = time();

        $batch = [];
        while (true) {
            $cart = $redis->rPop('Shop:Statistic:cart');
            if ($cart === false) {
                break;
            }

            $cart = json_decode($cart, true);
            if ($cart === null) {
                continue;
            }

            $batch[] = [
                'index' => [
                    '_index' => $configEs->indexStatisticCart
                ]
            ];

            $batch[] = [
                'user_token' => $cart['user_token'],
                'user_id' => $cart['user_id'],
                'product_id' => $cart['product_id'],
                'product_item_id' => $cart['product_item_id'],
                'create_time' => $cart['create_time'],
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
                    throw new TaskException('购物车队列写入ES出错：' . $reason);
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
                throw new TaskException('购物车队列写入ES出错：' . $reason);
            }
        }

    }

}
