<?php

namespace Be\App\Shop\Section\Product\HottestTopNSide;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['west', 'east'];
    
    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $products = Be::getService('App.Shop.Product')->getHottestTopNProducts($this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $defaultMoreLink = beUrl('Shop.Product.hottest');
        echo Be::getService('App.Shop.Section')->makeSideProductsSection($this, 'app-shop-product-hottest-top-n-side', $products, $defaultMoreLink);
    }

}

