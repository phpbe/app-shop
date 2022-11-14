<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class Base
{

    public function __construct()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        if (!isset($my->token) || $my->token === '') {
            $user = null;

            $token = trim($request->cookie('shop_user_token', ''));
            if ($token === '') {
                try {
                    $user = (object)Be::getService('App.Shop.User')->newTokenUser($request->getIp());
                } catch (\Throwable $t) {}
            } else {
                try {
                    $user = (object)Be::getService('App.Shop.User')->tokenLogin($token,
                        $request->cookie('shop_user_token_auth', 0, 'int'),
                        $request->getIp()
                    );
                } catch (\Throwable $t) {}
            }

            if ($user !== null) {
                $response->cookie('shop_user_token', $user->token, time() + 180*86400, '/', $request->getDomain(), false, true);
                $response->cookie('shop_user_token_auth', $user->token_auth, time() + 180*86400, '/', $request->getDomain(), false, true);
                Be::setUser($user);
            }
        }

        if (!$request->isAjax()) {
            // 访客统计
            Be::getService('App.Shop.Statistic')->visit();
        }
    }

}

