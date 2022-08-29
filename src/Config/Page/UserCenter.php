<?php

namespace Be\App\ShopFai\Config\Page;

class UserCenter
{

    public int $west = 25;
    public int $center = 75;
    public int $east = 0;

    public array $westSections = [
        [
            'name' => 'App.ShopFai.UserCenterMenu',
        ],
    ];

    public array $centerSections = [
        [
            'name' => 'be-page-content',
        ],
    ];

}
