<?php

namespace Be\App\Shop\Section\Product\GuessYouLike;

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

        $request = Be::getRequest();
        $response = Be::getResponse();

        $page = $request->get('page', 1);
        if ($page > $this->config->maxPages) {
            $page = $this->config->maxPages;
        }
        $params = [
            'pageSize' => $this->config->pageSize,
            'page' => $page,
        ];

        $result = Be::getService('App.Shop.Product')->getGuessYouLikeProducts($params);

        echo Be::getService('App.Shop.Section')->makePagedProductsSection($this, 'app-shop-product-guess-you-like', $result);
    }

}

