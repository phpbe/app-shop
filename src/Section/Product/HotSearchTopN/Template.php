<?php

namespace Be\App\Shop\Section\Product\HotSearchTopN;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'west', 'center', 'east'];

    
    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $products = Be::getService('App.Shop.Product')->getHotSearchTopNProducts($this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $defaultMoreLink = beUrl('Shop.Product.hotSearch');
        echo Be::getService('App.Shop.Section')->makeProductsSection($this, 'app-shop-product-hot-search-top-n', $products, $defaultMoreLink);
    }

}

