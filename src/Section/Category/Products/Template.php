<?php

namespace Be\App\Shop\Section\Category\Products;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Category.products'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $request = Be::getRequest();

        $orderBy = $request->get('order_by', 'common');
        $orderByDir = $request->get('order_by_dir', 'desc');
        $page = $request->get('page', 1);
        if ($page > $this->config->maxPages) {
            $page = $this->config->maxPages;
        }
        
        $result = Be::getService('App.Shop.Product')->search('', [
            'categoryId' => $this->page->categoryId,
            'orderBy' => $orderBy,
            'orderByDir' => $orderByDir,
            'pageSize' => $this->config->pageSize,
            'page' => $page,
        ]);

        $paginationUrl = beUrl('Shop.Category.products', ['id' => $this->page->category->id]);
        echo Be::getService('App.Shop.Section')->makePagedProductsSection($this, 'category-products', $result, $paginationUrl);
    }

}

