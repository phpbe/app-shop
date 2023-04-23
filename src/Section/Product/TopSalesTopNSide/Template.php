<?php

namespace Be\App\Shop\Section\Product\TopSalesTopNSide;

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

        $products = Be::getService('App.Shop.Product')->getTopSalesTopNProducts($this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $defaultMoreLink = beUrl('Shop.Product.topSales');
        echo Be::getService('App.Shop.Section')->makeSideProductsSection($this, 'app-shop-product-top-sales-top-n-side', $products, $defaultMoreLink);
    }

}

