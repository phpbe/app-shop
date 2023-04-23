<?php

namespace Be\App\Shop\Config\Page\Product;

class detail
{

    public int $west = 0;
    public int $center = 100;
    public int $east = 0;

    public array $centerSections = [
        [
            'name' => 'App.Shop.Product.Detail.Main',
        ],
        [
            'name' => 'App.Shop.Product.Detail.SimilarTopN',
        ],
        [
            'name' => 'App.Shop.Product.Detail.Description',
        ],
        [
            'name' => 'App.Shop.Product.Detail.Reviews',
        ],
        [
            'name' => 'App.Shop.Product.GuessYouLikeTopN',
        ],
    ];

}
