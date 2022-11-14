<?php

namespace Be\App\Shop\Config\Page\User;

class popLogin
{

    public int $header = 0;
    public int $footer = 0;
    public int $middle = 1;
    public int $west = 0;
    public int $center = 0;
    public int $east = 0;

    public array $middleSections = [
        [
            'name' => 'Theme.System.PageContent',
        ],
    ];
}

