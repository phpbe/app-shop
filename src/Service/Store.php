<?php

namespace Be\App\ShopFai\Service;

use Be\App\ServiceException;
use Be\Be;

class Store
{


    /**
     * 店铺时间转系统时间
     *
     * @return string
     */
    public function storeTime2SystemTime($storeTime, $format = 'Y-m-d H:i:s'): string
    {
        $configStore = Be::getConfig('App.ShopFai.Store');
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
        $configStore = Be::getConfig('App.ShopFai.Store');
        $configSystem = Be::getConfig('App.System.System');
        $storeTime = new \DateTime($systemTime, new \DateTimeZone($configSystem->timezone));
        $storeTime->setTimezone(new \DateTimeZone($configStore->timezone));
        return $storeTime->format($format);
    }

}