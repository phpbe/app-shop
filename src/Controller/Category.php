<?php

namespace Be\App\ShopFai\Controller;

use Be\App\ControllerException;
use Be\Be;

class Category extends Base
{

    /**
     *
     * @BeRoute("\Be\Be::getService('App.ShopFai.Category')->getCategoryUrl($params)")
     */
    public function products()
    {
        $my = Be::getUser();
        $userId = $my->id;

        $request = Be::getRequest();
        $response = Be::getResponse();

        $categoryId = $request->get('id');
        if (!$categoryId) {
            $response->end();
            return;
        }
        $response->set('categoryId', $categoryId);

        $category = Be::getService('App.ShopFai.Category')->getCategory($categoryId);
        $response->set('category', $category);

        $response->set('title', $category->seo_title);
        $response->set('meta_keywords', $category->seo_keywords);
        $response->set('meta_description', $category->seo_description);

        $orderBy = $request->get('order_by', 'common');
        $orderByDir = $request->get('order_by_dir', 'desc');
        $response->set('orderBy', $orderBy);
        $response->set('orderByDir', $orderByDir);

        $page = $request->get('page', 1);

        $result = Be::getService('App.ShopFai.Product')->filter([
            'categoryId' => $categoryId,
            'orderBy' => $orderBy,
            'orderByDir' => $orderByDir,
            'page' => $page,
        ]);

        $response->set('result', $result);

        $response->display();
    }



}
