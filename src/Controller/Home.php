<?php

namespace Be\App\ShopFai\Controller;


use Be\Be;

class Home extends Base
{


    /**
     * 首页
     *
     * @BeMenu("首页")
     * @BeRoute("/shopfai/home/")
     */
    public function index()
    {
        $response = Be::getResponse();
        $response->display();
    }

}

