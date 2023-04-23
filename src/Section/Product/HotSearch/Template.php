<?php

namespace Be\App\Shop\Section\Product\HotSearch;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $request = Be::getRequest();
        $response = Be::getResponse();

        $page = $request->get('page', 1);
        $params = [
            'page' => $page,
        ];

        if ($this->config->pageSize > 0) {
            $params['pageSize'] = $this->config->pageSize;
        }

        $result = Be::getService('App.Shop.Product')->getHotSearchProducts($params);

        echo Be::getService('App.Shop.Section')->makePagedProductsSection($this, 'app-shop-product-hot-search', $result);
    }

}

