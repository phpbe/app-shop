<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 销售统计
 *
 * @BeMenuGroup("分析", icon="el-icon-s-marketing", ordering="6")
 * @BePermissionGroup("分析",  ordering="6")
 */
class StatisticSales extends Auth
{

    /**
     * 销售统计
     *
     * @BeMenu("销售统计", icon="el-icon-discount", ordering="6.1")
     * @BePermission("销售统计", ordering="6.1")
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
