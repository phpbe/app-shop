<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;


/**
 * @BeMenuGroup("店熵", icon="el-icon-document-copy", ordering="1")
 * @BePermissionGroup("店熵", icon="el-icon-document-copy", ordering="1")
 */
class Home extends Auth
{

    /**
     * 首页
     *
     * @BeMenu("首页", icon="el-icon-s-home", ordering="1.1")
     * @BePermission("首页", ordering="1.1")
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $configStore = Be::getConfig('App.ShopFai.Store');

        if ($configStore->setUp === 7) {
            $serviceStatisticSales = Be::getService('App.ShopFai.Admin.StatisticSales');
            $todaySalesPaidSum = $serviceStatisticSales->getPaidSum(['dateRangeType' => 'today']);
            $response->set('todaySalesPaidSum', $todaySalesPaidSum);

            $todaySalesPaidCount = $serviceStatisticSales->getPaidCount(['dateRangeType' => 'today']);
            $response->set('todaySalesPaidCount', $todaySalesPaidCount);

            $todaySalesPaidNotShippedCount = $serviceStatisticSales->getPaidNotShippedCount(['dateRangeType' => 'today']);
            $response->set('todaySalesPaidNotShippedCount', $todaySalesPaidNotShippedCount);

            $serviceStatisticVisit = Be::getService('App.ShopFai.Admin.StatisticVisit');
            $todayVisitUniqueUserCount = $serviceStatisticVisit->getUniqueUserCount(['dateRangeType' => 'today']);
            $response->set('todayVisitUniqueUserCount', $todayVisitUniqueUserCount);

            $response->display();
        } else {

            $configTheme = Be::getConfig('App.System.Theme');
            $themeProperty = Be::getProperty('Theme.' . $configTheme->default);
            $response->set('themeProperty', $themeProperty);

            $response->set('configStore', $configStore);

            $response->display('App.ShopFai.Admin.Home.setUp');
        }
    }

}

