<?php

namespace Be\App\Shop\Service\Admin;

use Be\Be;
use Be\Config\ConfigHelper;

class Store
{

    /**
     * 店铺时间转系统时间
     *
     * @return string
     */
    public function storeTime2SystemTime($storeTime, $format = 'Y-m-d H:i:s'): string
    {
        $configStore = Be::getConfig('App.Shop.Store');
        $configSystem = Be::getConfig('App.System.System');
        $systemTime = new \DateTime($storeTime, new \DateTimeZone($configStore->timezone));
        $systemTime->setTimezone(new \DateTimeZone($configSystem->timezone));
        return $systemTime->format($format);
    }

    /**
     * 系统时间转店铺时间
     *
     * @return string
     */
    public function systemTime2StoreTime($systemTime, $format = 'Y-m-d H:i:s'): string
    {
        $configStore = Be::getConfig('App.Shop.Store');
        $configSystem = Be::getConfig('App.System.System');
        $storeTime = new \DateTime($systemTime, new \DateTimeZone($configSystem->timezone));
        $storeTime->setTimezone(new \DateTimeZone($configStore->timezone));
        return $storeTime->format($format);
    }

    /**
     * 完成某项店铺基础设置
     * 当 $configStore->setUp === 7 时，表示三项基础设置均已设置
     *
     * @param int $key 1：设置商品, 2：设置物流, 4：设置收款
     * @return void
     */
    public function setUp(int $key)
    {
        $configStore = Be::getConfig('App.Shop.Store');
        if (($configStore->setUp & $key) === 0) {
            $configStore->setUp = $configStore->setUp | $key;
            ConfigHelper::update('App.Shop.Store', $configStore);

            if (Be::getRuntime()->isSwooleMode()) {
                Be::getRuntime()->reload();
            }
        }
    }
}