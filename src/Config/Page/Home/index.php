<?php

namespace Be\App\ShopFai\Config\Page\Home;

class index
{


    public int $middle = 1;

    public array $middleSections = [
        [
            'name' => 'App.ShopFai.Latest',
        ],
        [
            'name' => 'App.ShopFai.Hottest',
        ],
        [
            'name' => 'App.ShopFai.TopSales',
        ],
        [
            'name' => 'App.ShopFai.TopSearch',
        ],
    ];


}
