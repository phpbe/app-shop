<?php

namespace Be\App\Shop\Section\Hottest;

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

        $products = Be::getService('App.Shop.Product')->getHottestProducts($this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $moreLink = beUrl('Shop.Product.hottest');
        echo Be::getService('App.Shop.Section')->makeProductsSection($this, 'hottest', $products, $moreLink);
    }

}

