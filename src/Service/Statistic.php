<?php

namespace Be\App\Shop\Service;

use Be\Be;

/**
 * 统计
 * Class Statistic
 *
 * @package Be\App\Shop\Service
 */
class Statistic
{

    /**
     * 访客
     *
     * @return void
     */
    public function visit()
    {
        $configSystemRedis = Be::getConfig('App.System.Redis');
        if ($configSystemRedis->enable === 0) {
            return;
        }

        $request = Be::getRequest();

        $productId = '';
        if ($request->getRoute() === 'Shop.Product.detail') {
            $productId = $request->get('id', '');
        }

        $my = Be::getUser();

        $now = date('Y-m-d H:i:s');
        $data = [
            'user_token' => $my->token,
            'user_id' => $my->id,
            'product_id' => $productId,
            'is_guest' => $my->isGuest(),
            'ip' => $request->getIp(),
            'is_mobile' => $request->isMobile(),
            'url' => $request->getUrl(),
            'referer' => $request->getReferer(),
            'user_agent' => $request->header('user-agent'),
            'accept' => $request->header('accept'),
            'accept_encoding' => $request->header('accept-encoding'),
            'accept_language' => $request->header('accept-language'),
            'create_time' => $now,
        ];

        $redis = Be::getRedis();
        $redis->lPush('Shop:Statistic:Visit', json_encode($data));
    }

    /**
     * 访客
     *
     * @return void
     */
    public function cart($productId, $productItemId)
    {
        $configSystemRedis = Be::getConfig('App.System.Redis');
        if ($configSystemRedis->enable === 0) {
            return;
        }

        $my = Be::getUser();

        $now = date('Y-m-d H:i:s');
        $data = [
            'user_token' => $my->token,
            'user_id' => $my->id,
            'product_id' => $productId,
            'product_item_id' => $productItemId,
            'create_time' => $now,
        ];

        $redis = Be::getRedis();
        $redis->lPush('Shop:Statistic:cart', json_encode($data));
    }

    public function detectUserAgentBrowser($userAgent, $withVersion = false)
    {
        $browser = '';
        foreach ([
                     'WeChat' => 'MicroMessenger',
                     'Chrome' => 'Chrome',
                     'Edge' => 'Edge|Edg',
                     'IE' => 'MSIE|IEMobile|MSIEMobile|Trident/[.0-9]+',
                     'Firefox' => 'Firefox',
                     'Opera' => 'Opera|OPR',
                     'Safari' => 'Safari',
                     'UC' => 'UC.*Browser|UCWEB',
                     'Netscape' => 'Netscape',
                 ] as $key => $val) {
            if (preg_match('#' . $val . '#is', $userAgent)) {
                $browser = $key;
                break;
            }
        }

        if ($browser) {
            if ($withVersion) {
                $versionRegs = [
                    'WeChat' => ['MicroMessenger/([\w._\+]+)'],
                    'Chrome' => ['Chrome/([\w._\+]+)', 'CriOS/([\w._\+]+)', 'CrMo/([\w._\+]+)'],
                    'Edge' => ['Edge/([\w._\+]+)', 'Edg/([\w._\+]+)'],
                    'IE' => ['IEMobile/([\w._\+]+);', 'IEMobile ([\w._\+]+)', 'MSIE ([\w._\+]+);', 'rv:([\w._\+]+)'],
                    'Firefox' => ['Firefox/([\w._\+]+)', 'FxiOS/([\w._\+]+)'],
                    'Opera' => [' OPR/([\w._\+]+)', 'Opera Mini/([\w._\+]+)', 'Version/([\w._\+]+)', 'Opera ([\w._\+]+)'],
                    'Safari' => ['Version/([\w._\+]+)', 'Safari/([\w._\+]+)'],
                    'UC' => ['UCWEB([\w._\+]+)', 'UC.*Browser/([\w._\+]+)'],
                    'Netscape' => ['Netscape/([\w._\+]+)'],
                ];

                $versionReg = $versionRegs[$browser];
                foreach ($versionReg as $val) {
                    preg_match('#' . $val . '#is', $userAgent, $match);
                    if (false === empty($match[1])) {
                        $version = $match[1];
                        $version = str_replace(array('_', ' ', '/'), '.', $version);
                        $browser .= ' ' . $version;
                        break;
                    }
                }
            }
        } else {
            $browser = 'Other';
        }

        return $browser;
    }


    public function detectUserAgentOs($userAgent, $withVersion = false)
    {
        $os = '';
        foreach ([
                     'Android' => 'Android',
                     'IOS' => '\biPhone.*Mobile|\biPod|\biPad|AppleCoreMedia',
                     'Windows' => 'Windows',
                     'Mac' => 'Mac OS',
                     'Ubuntu' => 'Ubuntu',
                     'Debian' => 'Debian',
                     'Linux' => 'Linux',
                 ] as $key => $val) {
            if (preg_match('#' . $val . '#is', $userAgent)) {
                $os = $key;
                break;
            }
        }

        if ($os) {
            if ($withVersion) {
                if ($os === 'Windows') {
                    $versionRegs = [
                        '11' => 'NT 11.0',
                        '10' => 'NT 10.0',
                        '8' => 'NT 6.2',
                        '7' => 'NT 6.1',
                        'Vista' => 'NT 6.0',
                        'XP' => 'NT 5.1',
                        '2000' => 'NT 5',
                    ];

                    foreach ($versionRegs as $key => $val) {
                        if (preg_match('#' . $val . '#is', $userAgent)) {
                            $version = $key;
                            $os .= ' ' . $version;
                            break;
                        }
                    }
                } else {
                    $versionRegs = [
                        'Android' => ['Android ([\w._\+]+)'],
                        'IOS' => [' \bi?OS\b ([\w._\+]+)[ ;]{1}'],
                        'Mac' => ['Mac OS X ([\w._\+]+)'],
                    ];
                    if (isset($versionRegs[$os])) {
                        $versionReg = $versionRegs[$os];
                        foreach ($versionReg as $val) {
                            preg_match('#' . $val . '#is', $userAgent, $match);
                            if (false === empty($match[1])) {
                                $version = $match[1];
                                $version = str_replace(array('_', ' ', '/'), '.', $version);
                                $os .= ' ' . $version;
                                break;
                            }
                        }
                    }

                }
            }
        } else {
            $os = 'Other';
        }

        return $os;
    }


}
