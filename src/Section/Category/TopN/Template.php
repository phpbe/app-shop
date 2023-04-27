<?php

namespace Be\App\Shop\Section\Category\TopN;

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

        $categories = Be::getService('App.Shop.Category')->getCategories($this->config->quantity);
        if (count($categories) === 0) {
            return;
        }

        $this->css();

        echo '<div class="app-shop-category-top-n">';
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }

        if ($this->config->title !== '') {
            echo $this->page->tag0('be-section-title');
            echo $this->config->title;
            echo $this->page->tag1('be-section-title');
        }

        $isMobile = \Be\Be::getRequest()->isMobile();

        echo $this->page->tag0('be-section-content');
        echo '<div class="app-shop-category-top-n-items">';
        foreach ($categories as $category) {

            if ($category->image === '') {
                $category->image = Be::getProperty('App.Shop')->getWwwUrl() . '/images/category/no-image.webp';
            }

            echo '<div class="app-shop-category-top-n-item">';

            echo '<div class="be-ta-center app-shop-category-top-n-item-image">';
            echo '<a href="' . beUrl('Shop.Category.products', ['id' => $category->id]) . '"';
            if (!$isMobile) {
                echo ' target="_blank"';
            }
            echo '>';

            echo '<img src="' . $category->image . '" alt="' . htmlspecialchars($category->name) . '">';

            echo '</a>';
            echo '</div>';


            echo '<div class="be-mt-100 be-ta-center">';
            echo '<a class="be-d-block be-t-ellipsis" href="' . beUrl('Shop.Category.products', ['id' => $category->id]) . '"';
            if (!$isMobile) {
                echo ' target="_blank"';
            }
            echo '>';
            echo $category->name;
            echo '</a>';
            echo '</div>';


            echo '</div>';
        }
        echo '</div>';
        echo $this->page->tag1('be-section-content');

        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
        echo '</div>';
    }

    private function css()
    {
        echo '<style type="text/css">';
        echo $this->getCssBackgroundColor('app-shop-category-top-n');
        echo $this->getCssPadding('app-shop-category-top-n');
        echo $this->getCssMargin('app-shop-category-top-n');

        echo '#' . $this->id . '{';
        echo '}';

        echo '#' . $this->id . ' .app-shop-category-top-n {';
        echo '}';


        echo '#' . $this->id . ' .app-shop-category-top-n-title {';
        echo 'margin-bottom: 2rem;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-category-top-n-title h3 {';
        echo 'border-bottom: 2px solid #eee;';
        echo 'text-align: center;';
        echo 'position: relative;';
        echo 'padding-bottom: 1rem;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-category-top-n-title h3:before {';
        echo 'position: absolute;';
        echo 'content: "";';
        echo 'left: 50%;';
        echo 'bottom: -2px;';
        echo 'width: 100px;';
        echo 'height: 2px;';
        echo 'margin-left: -50px;';
        echo 'background-color: var(--major-color);';
        echo '}';

        $itemWidthMobile = '100%';
        $itemWidthTablet = '50%';
        $itemWidthDesktop = '33.333333333333%';
        $itemWidthDesktopXl = '';
        $itemWidthDesktopXxl = '';
        $itemWidthDesktopX3l = '';
        $cols = 4;
        if (isset($section->config->cols)) {
            $cols = $section->config->cols;
        }
        if ($cols >= 4) {
            $itemWidthDesktopXl = '25%';
        }
        if ($cols >= 5) {
            $itemWidthDesktopXxl = '20%';
        }
        if ($cols >= 6) {
            $itemWidthDesktopX3l = '16.666666666666%';
        }
        echo $section->getCssSpacing('app-shop-category-top-n-items', 'app-shop-category-top-n-item', $itemWidthMobile, $itemWidthTablet, $itemWidthDesktop, $itemWidthDesktopXl, $itemWidthDesktopXxl, $itemWidthDesktopX3l);

        echo '</style>';
    }


}

