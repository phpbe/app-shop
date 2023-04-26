<?php

namespace Be\App\Shop\Section\Product\GuessYouLikeTopN;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    
    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $products = Be::getService('App.Shop.Product')->getGuessYouLikeTopNProducts($this->config->quantity);
        if (count($products) === 0) {
            return;
        }

        $defaultMoreLink = beUrl('Shop.Product.guessYouLike');
        echo Be::getService('App.Shop.Section')->makeProductsSection($this, 'app-shop-product-guess-you-like-top-n', $products, $defaultMoreLink);
    }

}

