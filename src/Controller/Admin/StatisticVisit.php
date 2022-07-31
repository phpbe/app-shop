<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 访问统计
 *
 * @BeMenuGroup("分析", icon="el-icon-s-marketing", ordering="6")
 * @BePermissionGroup("分析",  ordering="6")
 */
class StatisticVisit extends Auth
{

    /**
     * 访问统计
     *
     * @BeMenu("访问统计", icon="el-icon-discount", ordering="6.2")
     * @BePermission("访问统计", ordering="6.2")
     */
    public function dashboard()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $configStore = Be::getConfig('App.ShopFai.Store');
        $response->set('configStore', $configStore);

        $response->display();
    }


}
