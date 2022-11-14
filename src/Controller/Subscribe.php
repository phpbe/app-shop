<?php

namespace Be\App\Shop\Controller;

use Be\Be;

/**
 * 订阅
 */
class Subscribe extends Base
{

    /**
     * 订阅
     *
     * @BeRoute("/subscribe")
     */
    public function index()
    {
        $my = Be::getUser();
        $userId = $my->id;

        $request = Be::getRequest();
        $response = Be::getResponse();

        $response->display();
    }

    /**
     * 订阅保存
     *
     * @BeRoute("/subscribe/save")
     */
    public function save()
    {
        $my = Be::getUser();
        $userId = $my->id;

        $request = Be::getRequest();
        $response = Be::getResponse();

    }


}

