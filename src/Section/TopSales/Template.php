<?php

namespace Be\App\ShopFai\Section\TopSales;

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

        $products = Be::getService('App.ShopFai.Product')->getTopSalesProducts($this->config->quantity);
        if (count($products) === 0) {
            $products = Be::getService('App.ShopFai.Product')->getSampleProducts($this->config->quantity);
        }

        $moreLink = beUrl('ShopFai.Product.topSales');
        echo Be::getService('App.ShopFai.Section')->makeProductsSection($this, 'top-sales', $products, $moreLink);
    }

}

