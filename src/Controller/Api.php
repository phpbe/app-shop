<?php

namespace Be\App\Shop\Controller;

use Be\App\ControllerException;
use Be\Be;

/**
 * 接口
 */
class Api
{

    public function __contruct()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $serviceApi = Be::getService('App.Shop.Admin.Api');
        $apiConfig = $serviceApi->getConfig();

        if ($apiConfig->enable === 0) {
            $response->error('Api 接口未启用！');
            $response->end();
        }

        $token = $request->header('token', '');
        if ($apiConfig->token !== $token) {
            $response->error('Token 无效！');
            $response->end();
        }
    }

    /**
     * 创建商品
     *
     * @BeRoute("/api/product/create")
     */
    public function productCreate()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

    }

    /**
     * 编辑商品
     *
     * @BeRoute("/api/product/edit")
     */
    public function productEdit()
    {

    }

    /**
     * 商品上架
     *
     * @BeRoute("/api/product/enable")
     */
    public function productEnable()
    {

    }

    /**
     * 商品下架
     *
     * @BeRoute("/api/product/disable")
     */
    public function productDisable()
    {

    }

    /**
     * 删除商品
     *
     * @BeRoute("/api/product/delete")
     */
    public function productDelete()
    {

    }

    /**
     * 商品列表
     *
     * @BeRoute("/api/products")
     */
    public function products()
    {

    }

    /**
     * 创建用户
     *
     * @BeRoute("/api/user/create")
     */
    public function userCreate()
    {

    }

    /**
     * 编辑用户
     *
     * @BeRoute("/api/user/edit")
     */
    public function userEdit()
    {

    }

    /**
     * 用户列表
     *
     * @BeRoute("/api/users")
     */
    public function users()
    {

    }



    /**
     * 订单列表
     *
     * @BeRoute("/api/orders")
     */
    public function orders()
    {

    }

}
