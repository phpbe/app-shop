<?php

namespace Be\App\Shop\Controller;


use Be\Be;

class Home extends Base
{


    /**
     * 首页
     *
     * @BeMenu("首页")
     * @BeRoute("/shop/home/")
     */
    public function index()
    {
        $response = Be::getResponse();
        $response->display();
    }

}

