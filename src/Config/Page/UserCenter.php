<?php

namespace Be\App\Shop\Config\Page;

class UserCenter
{

    public int $west = 25;
    public int $center = 75;
    public int $east = 0;

    public array $westSections = [
        [
            'name' => 'App.Shop.UserCenterMenu',
        ],
    ];

    public array $centerSections = [
        [
            'name' => 'Theme.System.PageContent',
        ],
    ];

}
