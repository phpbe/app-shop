<?php

namespace Be\App\Shop\Section\Category\HottestTopNSide;

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

        if ($this->route !== 'Shop.Category.products') {
            return;
        }

        $products = Be::getService('App.Shop.Product')->getCategoryHottestTopNProducts($this->page->category->id, $this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        echo Be::getService('App.Shop.Section')->makeSideProductsSection($this, 'app-shop-category-hottest-top-n-side', $products);
    }
}

