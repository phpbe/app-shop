<?php

namespace Be\App\Shop\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 访问统计
 *
 * @BeMenuGroup("分析")
 * @BePermissionGroup("分析")
 */
class StatisticVisit extends Auth
{

    /**
     * 访问统计
     *
     * @BeMenu("访问统计", icon="el-icon-thumb", ordering="6.2")
     * @BePermission("访问统计", ordering="6.2")
     */
    public function dashboard()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $configStore = Be::getConfig('App.Shop.Store');
        $response->set('configStore', $configStore);

        $response->display();
    }


}
