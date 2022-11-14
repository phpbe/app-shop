<?php

namespace Be\App\Shop\Config\Page\Home;

class index
{


    public int $middle = 1;

    public array $middleSections = [
        [
            'name' => 'App.Shop.Latest',
        ],
        [
            'name' => 'App.Shop.Hottest',
        ],
        [
            'name' => 'App.Shop.TopSales',
        ],
        [
            'name' => 'App.Shop.TopSearch',
        ],
    ];


}
