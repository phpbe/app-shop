<?php

namespace Be\App\Shop\Config\Page\Category;

class products
{


    public int $west = 25;
    public int $center = 75;
    public int $east = 0;

    public array $westSections = [
        [
            'name' => 'App.Shop.Category.MenuSide',
        ],
        [
            'name' => 'App.Shop.Product.HottestopNSide',
        ],
    ];

    public array $centerSections = [
        [
            'name' => 'Theme.System.PageTitle',
        ],
        [
            'name' => 'App.Shop.Category.Products',
        ],
    ];


}
