<?php

namespace Be\App\Shop;


class Property extends \Be\App\Property
{

    protected string $label = '店熵商城';
    protected string $icon = 'bi-cart';
    protected string $description = '店熵商城';

    public function __construct() {
        parent::__construct(__FILE__);
    }

}
