<?php

namespace Be\App\Shop\Section\Category\HotSearchTopNSide;

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

        $request = Be::getRequest();
        if ($request->getRoute() !== 'Shop.Category.products') {
            return;
        }

        $products = Be::getService('App.Shop.Product')->getCategoryHotSearchTopNProducts($this->page->category->id, $this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        echo Be::getService('App.Shop.Section')->makeProductsSection($this, 'app-shop-category-hot-search-top-n-side', $products);
    }
}

