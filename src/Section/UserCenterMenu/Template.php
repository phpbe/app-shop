<?php

namespace Be\App\Shop\Section\UserCenterMenu;

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
        echo 'height: 2.5rem;';
        echo 'line-height: 2.5rem;';
        echo '}';

        echo '.user-center-menu li a {';
        echo 'color: #888;';
        echo 'transition: all 0.3s;';
        echo 'display: block;';
        echo 'text-indent: .5rem;';
        echo '}';

        echo '.user-center-menu li a:hover,';
        echo '.user-center-menu li.active a {';
        echo 'background-color: #efefef;';
        echo '}';

        echo '</style>';

        echo '<div class="user-center-menu">';

        if ($this->config->title !== '') {
            echo $this->page->tag0('be-section-title', true);
            echo $this->config->title;
            echo $this->page->tag1('be-section-title', true);
        }

        echo $this->page->tag0('be-section-content', true);

        $route = Be::getRequest()->getRoute();

        $menu = Be::getMenu('UserCenter');
        $menuTree = $menu->getTree();
        foreach ($menuTree as $item) {

            echo '<li';
            if ($item->route === $route) {
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

        echo '<li>';
        echo '<a href="' . beUrl('Shop.User.logout') . '">Sign Out</a>';
        echo '</li>';

        echo '<ul>';

        echo $this->page->tag1('be-section-content', true);

        echo '</div>';

    }

}

