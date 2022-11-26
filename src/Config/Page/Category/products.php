<?php

namespace Be\App\Shop\Config\Page\Category;

class products
{

    public int $west = 0;
    public int $center = 100;
    public int $east = 0;

    public array $centerSections = [
        [
            'name' => 'Theme.System.PageTitle',
        ],
        [
            'name' => 'App.Shop.CategoryProducts',
        ],
    ];


}
