<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 物流运费
 *
 * @BeMenuGroup("控制台", icon="el-icon-monitor", ordering="7")
 * @BePermissionGroup("控制台",  ordering="7")
 */
class Shipping extends Auth
{

    /**
     * 物流运费
     *
     * @BeMenu("物流运费", icon="el-icon-discount", ordering="7.1")
     * @BePermission("物流运费", ordering="7.1")
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $response->set('title', '物流运费');

        $service = Be::getService('App.ShopFai.Admin.Shipping');
        $shippingList = $service->getShippingList();
        $response->set('shippingList', $shippingList);

        $configStore = Be::getConfig('App.ShopFai.Store');
        $response->set('configStore', $configStore);

        $response->display();
    }


    /**
     * 物流运费 添加
     *
     * @BePermission("物流运费-添加", ordering="7.11")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $serviceShipping = Be::getService('App.ShopFai.Admin.Shipping');

        if ($request->isAjax()) {
            try {
                $serviceShipping->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '添加区域方案成功！');
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('title', '添加区域方案');

            $response->set('shipping', false);

            $regionTree = $serviceShipping->getRegionTree();
            $response->set('regionTree', $regionTree);

            $configStore = Be::getConfig('App.ShopFai.Store');
            $response->set('configStore', $configStore);

            $response->display('App.ShopFai.Admin.Shipping.edit');
        }
    }

    /**
     * 物流运费 编辑
     *
     * @BePermission("物流运费-编辑", ordering="7.12")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $serviceShipping = Be::getService('App.ShopFai.Admin.Shipping');

        if ($request->isAjax()) {
            try {
                $serviceShipping->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑区域方案成功！');
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $response->set('title', '编辑区域方案');

            $shippingId = $request->get('id', '');
            $shipping = $serviceShipping->getShipping($shippingId);
            $response->set('shipping', $shipping);

            $regionTree = $serviceShipping->getRegionTree($shippingId);
            $response->set('regionTree', $regionTree);

            $configStore = Be::getConfig('App.ShopFai.Store');
            $response->set('configStore', $configStore);

            $response->display('App.ShopFai.Admin.Shipping.edit');
        }
    }

    /**
     * 物流运费 删除
     *
     * @BePermission("物流运费-删除", ordering="7.13")
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $serviceShipping = Be::getService('App.ShopFai.Admin.Shipping');
        try {
            $shippingId = $request->get('id', '');
            $serviceShipping->delete($shippingId);
            $response->set('success', true);
            $response->set('message', '删除区域方案成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }

    }

}
