<?php

namespace Be\App\ShopFai\Section\UserCenterMenu;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['west', 'east'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        echo '<style type="text/css">';
        echo $this->getCssBackgroundColor('user-center-menu');
        echo $this->getCssPadding('user-center-menu');
        echo $this->getCssMargin('user-center-menu');

        echo '.user-center-menu ul {';
        echo '}';

        echo '.user-center-menu li {';
        echo 'height: 40px;';
        echo 'line-height: 40px;';
        echo '}';

        echo '.user-center-menu li a {';
        echo 'color: #888;';
        echo 'font-size: 14px;';
        echo 'transition: all 0.3s;';
        echo 'display: block;';
        echo 'margin: 0 15px;';
        echo 'ext-indent: 9px;';
        echo '}';

        echo '.user-center-menu li a:hover,';
        echo '.user-center-menu li.active a {';
        echo 'background-color: #efefef;';
        echo '}';

        echo '</style>';

        echo '<div class="user-center-menu">';

        if ($this->config->title !== '') {
            echo $this->pageTemplate->tag0('be-section-title', true);
            echo $this->config->title;
            echo $this->pageTemplate->tag1('be-section-title', true);
        }

        echo $this->pageTemplate->tag0('be-section-content', true);

        $route = Be::getRequest()->getRoute();

        $menu = Be::getMenu('UserCenter');
        $menuTree = $menu->getTree();
        foreach ($menuTree as $item) {

            echo '<li';
            if ($item->route == $route) {
                echo ' class="active"';
            }
            echo '>';

            $url = 'javascript:void(0);';
            if ($item->route) {
                if ($item->params) {
                    $url = beUrl($item->route, $item->params);
                } else {
                    $url = beUrl($item->route);
                }
            } else {
                if ($item->url) {
                    if ($item->url === '/') {
                        $url = beUrl();
                    } else {
                        $url = $item->url;
                    }
                }
            }
            echo '<a href="' . $url . '"';
            if ($item->target === '_blank') {
                echo ' target="_blank"';
            }
            echo '>' . $item->label . '</a>';

            echo '</li>';
        }

        echo '<ul>';

        echo $this->pageTemplate->tag1('be-section-content', true);

        echo '</div>';
        
        


    }
}

