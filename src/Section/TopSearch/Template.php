<?php

namespace Be\App\Shop\Section\TopSearch;

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

        $products = Be::getService('App.Shop.Product')->getTopSearchProducts($this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $moreLink = beUrl('Shop.Product.topSearch');
        echo Be::getService('App.Shop.Section')->makeProductsSection($this, 'top-search', $products, $moreLink);
    }

}

