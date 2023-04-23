<?php

namespace Be\App\Shop\Section\Product\Detail\Description;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Product.detail'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        echo '<style type="text/css">';
        echo $this->getCssBackgroundColor('app-shop-product-detail-description');
        echo $this->getCssPadding('app-shop-product-detail-description');
        echo $this->getCssMargin('app-shop-product-detail-description');
        echo '</style>';

        echo '<div class="app-shop-product-detail-description">';
        if ($this->position === 'middle') {
            echo '<div class="be-container">';
        }

        echo '<h4 class="be-h4 be-lh-200">' . $this->config->title . '</h4>';
        echo '<div class="be-mt-200">';
        echo $this->page->product->description;
        echo '</div>';

        if ($this->position === 'middle') {
            echo '</div>';
        }
        echo '</div>';
    }


}

