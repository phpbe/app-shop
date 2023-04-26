<?php

namespace Be\App\Shop\Section\Category\LatestTopNSide;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['west', 'east'];

    public array $routes = ['Shop.Category.products'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $products = Be::getService('App.Shop.Product')->getCategoryLatestTopNProducts($this->page->category->id, $this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $defaultMoreLink = beUrl('Shop.Product.latest');
        echo Be::getService('App.Shop.Section')->makeSideProductsSection($this, 'app-shop-category-latest-top-n-side', $products, $defaultMoreLink);
    }
}

