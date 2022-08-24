<?php

namespace Be\App\ShopFai\Controller;

use Be\Be;

class UserFavorite extends Base
{

    /**
     * 我的收藏
     *
     * @BeRoute("/favorites")
     */
    public function favorites()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        $products = Be::getService('App.ShopFai.UserFavorite')->getProducts($userId);
        $response->set('products', $products);
        $response->display();
    }

    /**
     * @BeRoute("/add-favorite")
     */
    public function addFavorite()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;
        try {
            $productId = $request->post('product_id');
            Be::getService('App.ShopFai.User')->addFavorite($userId, $productId);
            $response->set('success', true);
            $response->set('message', '收藏商品成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * @BeRoute("/delete-favorite")
     */
    public function deleteFavorite()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        try {
            $productId = $request->post('product_id');
            Be::getService('App.ShopFai.User')->deleteFavorite($userId, $productId);
            $response->set('success', true);
            $response->set('message', '删除收藏商品成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

}
