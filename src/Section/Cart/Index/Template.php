<?php

namespace Be\App\Shop\Section\Cart\Index;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Cart.index'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        echo '<style type="text/css">';
        echo $this->getCssBackgroundColor('app-shop-cart-index');
        echo $this->getCssPadding('app-shop-cart-index');
        echo $this->getCssMargin('app-shop-cart-index');
        echo '</style>';

        echo '<div class="app-shop-cart-index">';
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }





        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
        echo '</div>';
    }


}

