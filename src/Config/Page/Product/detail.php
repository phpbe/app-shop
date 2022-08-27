<?php

namespace Be\App\ShopFai\Config\Page\Product;

class detail
{

    public int $west = 0;
    public int $center = 100;
    public int $east = 0;

    public array $centerSections = [
        [
            'name' => 'App.ShopFai.ProductDetail',
        ],
    ];

    public string $spacingMobile = '0';
    public string $spacingTablet = '0';
    public string $spacingDesktop = '0';

}
