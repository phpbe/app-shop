<?php

namespace Be\App\Shop\Section\Product\Detail\SimilarTopN;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Product.detail'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $product = $this->page->product;
        $products = Be::getService('App.Shop.Product')->getSimilarProducts($product->id, $product->name, $this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        echo Be::getService('App.Shop.Section')->makeProductsSection($this, 'app-shop-product-detail-similar-top-n', $products);
    }



}

