<?php

namespace Be\App\Shop\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("控制台")
 * @BePermissionGroup("控制台")
 */
class Es extends Auth
{

    /**
     * @BeMenu("ES搜索引擎", icon="el-icon-search", ordering="7.3")
     * @BePermission("ES搜索引擎 - 查看", ordering="7.3")
     */
    public function dashboard()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $configSystemEs = Be::getConfig('App.System.Es');
        if ($configSystemEs->enable === 0) {
            $response->error('ES尚未启用，此功能不可用！');
            return;
        }

        $configEs = Be::getConfig('App.Shop.Es');
        $response->set('configEs', $configEs);

        $indexes = Be::getService('App.Shop.Admin.Es')->getIndexes();
        $response->set('indexes', $indexes);

        $response->set('title', 'ES搜索引擎');
        $response->display();
    }

    /**
     * 创建索引
     *
     * @BePermission("ES搜索引擎 - 创建索引", ordering="7.31")
     */
    public function createIndex()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $formData = $request->json('formData');
        $indexName = $formData['name'] ?? '';
        try {
            Be::getService('App.Shop.Admin.Es')->createIndex($indexName, $formData);
            $response->success('创建成功！');
        } catch (\Throwable $t) {
            $response->error('创建失败：' . $t->getMessage());
        }
    }

    /**
     * 删除索引
     *
     * @BePermission("ES搜索引擎 - 删除索引", ordering="7.32")
     */
    public function deleteIndex()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $formData = $request->json('formData');
        $indexName = $formData['name'] ?? '';
        try {
            Be::getService('App.Shop.Admin.Es')->deleteIndex($indexName);
            $response->success('删除成功！');
        } catch (\Throwable $t) {
            $response->error('删除失败：' . $t->getMessage());
        }
    }

}