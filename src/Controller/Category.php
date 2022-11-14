<?php

namespace Be\App\Shop\Controller;

use Be\App\ControllerException;
use Be\Be;

class Category extends Base
{

    /**
     *
     * @BeMenu("分类", picker="return \Be\Be::getService('App.Shop.Admin.Category')->getCategoryMenuPicker()")
     * @BeRoute("\Be\Be::getService('App.Shop.Category')->getCategoryUrl($params)")
     */
    public function products()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $categoryId = $request->get('id');
        if (!$categoryId) {
            $response->end();
            return;
        }
        $response->set('categoryId', $categoryId);

        $category = Be::getService('App.Shop.Category')->getCategory($categoryId);
        $response->set('category', $category);

        $response->set('title', $category->seo_title);
        $response->set('metaDescription', $category->seo_description);
        $response->set('metaKeywords', $category->seo_keywords);
        $response->set('pageTitle', $category->name);

        $orderBy = $request->get('order_by', 'common');
        $orderByDir = $request->get('order_by_dir', 'desc');
        $response->set('orderBy', $orderBy);
        $response->set('orderByDir', $orderByDir);

        $page = $request->get('page', 1);

        $result = Be::getService('App.Shop.Product')->search('', [
            'categoryId' => $categoryId,
            'orderBy' => $orderBy,
            'orderByDir' => $orderByDir,
            'page' => $page,
        ]);

        $response->set('result', $result);

        $response->display();
    }



}
