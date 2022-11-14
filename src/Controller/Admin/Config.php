<?php

namespace Be\App\Shop\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Config extends Auth
{

    /**
     * @BeMenu("参数", icon="el-icon-setting", ordering="7.4")
     * @BePermission("参数", ordering="7.4")
     */
    public function dashboard()
    {
        Be::getAdminPlugin('Config')->setting(['appName' => 'Shop', 'title' => '参数'])->execute();
    }


}