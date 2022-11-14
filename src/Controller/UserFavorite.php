<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class UserFavorite extends Base
{

    /**
     * 我的收藏
     *
     * @BeMenu("用户 - 收藏夹")
     * @BeRoute("/favorites")
     */
    public function favorites()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = \Be\Be::getUser();
        if ($my->isGuest()) {
            $page = Be::getConfig('App.Shop.Page.UserFavorite.favorites');
            $page->west = 0;
            $response->set('_page', $page);
        }

        $products = Be::getService('App.Shop.UserFavorite')->getProducts();
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

        try {
            $productId = $request->post('product_id');
            Be::getService('App.Shop.User')->addFavorite($productId);
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

        try {
            $productId = $request->post('product_id');
            Be::getService('App.Shop.User')->deleteFavorite($productId);
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
