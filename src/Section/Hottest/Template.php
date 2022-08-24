<?php

namespace Be\App\ShopFai\Section\Hottest;

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

        $products = Be::getService('App.ShopFai.Product')->getHottestProducts($this->config->quantity);
        if (count($products) === 0) {
            $products = Be::getService('App.ShopFai.Product')->getSampleProducts($this->config->quantity);
        }

        $moreLink = beUrl('ShopFai.Product.hottest');
        echo Be::getService('App.ShopFai.Section')->makeProductSection($this, 'hottest', $products, $moreLink);
    }

}

