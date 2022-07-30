<?php

namespace Be\App\ShopFai;


class Property extends \Be\App\Property
{

    protected string $label = '店熵';
    protected string $icon = 'el-icon-shopping-cart-full';
    protected string $description = '店熵系统';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}
