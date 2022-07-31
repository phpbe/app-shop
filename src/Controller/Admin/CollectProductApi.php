<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("商品")
 * @BePermissionGroup("商品")
 */
class CollectProductApi extends Auth
{

    /**
     * 采集接口
     *
     * @BeMenu("采集接口", icon="el-icon-aim", ordering="3.5")
     * @BePermission("采集接口", ordering="3.5")
     */
    public function config()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $config = Be::getService('App.ShopFai.Admin.CollectProductApi')->getConfig();
        $response->set('config', $config);
        $response->set('title', '采集接口');
        $response->display();
    }

    /**
     * 商品采集接口 切换启用状态
     *
     * @BePermission("切换启用状态", ordering="3.51")
     */
    public function toggleEnable()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $enable = Be::getService('App.ShopFai.Admin.CollectProductApi')->toggleEnable();
            $response->set('success', true);
            $response->set('message', '接口开关'.($enable ? '启用':'停用').'成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 商品采集接口 重设Token
     *
     * @BePermission("重设Token", ordering="3.52")
     */
    public function resetToken()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            Be::getService('App.ShopFai.Admin.CollectProductApi')->resetToken();
            $response->redirect(beAdminUrl('ShopFai.CollectProductApi.config'));
        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

}
