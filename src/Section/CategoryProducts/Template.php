<?php

namespace Be\App\ShopFai\Section\CategoryProducts;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];


    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $paginationUrl = beUrl('ShopFai.Category.products', ['id' => $this->page->category->id]);
        echo Be::getService('App.ShopFai.Section')->makePaginationProductsSection($this, 'category-products', $this->page->result, $paginationUrl);
    }

}

