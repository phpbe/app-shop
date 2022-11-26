<?php

namespace Be\App\Shop\Section\CategoryProducts;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Category.product'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $request = Be::getRequest();
        $response = Be::getResponse();

        $orderBy = $request->get('order_by', 'common');
        $orderByDir = $request->get('order_by_dir', 'desc');
        $response->set('orderBy', $orderBy);
        $response->set('orderByDir', $orderByDir);

        $pageSize = $this->config->pageSize;
        $page = $request->get('page', 1);

        $result = Be::getService('App.Shop.Product')->search('', [
            'categoryId' => $this->page->categoryId,
            'orderBy' => $orderBy,
            'orderByDir' => $orderByDir,
            'pageSize' => $pageSize,
            'page' => $page,
        ]);

        $paginationUrl = beUrl('Shop.Category.products', ['id' => $this->page->category->id]);
        echo Be::getService('App.Shop.Section')->makePaginationProductsSection($this, 'category-products', $result, $paginationUrl);
    }

}

