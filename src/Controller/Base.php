<?php

namespace Be\App\ShopFai\Controller;

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

            $token = trim($request->cookie('shopfai_user_token', ''));
            if ($token === '') {
                try {
                    $user = (object)Be::getService('App.ShopFai.User')->newTokenUser($request->getIp());
                } catch (\Throwable $t) {}
            } else {
                try {
                    $user = (object)Be::getService('App.ShopFai.User')->tokenLogin($token,
                        $request->cookie('shopfai_user_token_auth', 0, 'int'),
                        $request->getIp()
                    );
                } catch (\Throwable $t) {}
            }

            if ($user !== null) {
                $response->cookie('shopfai_user_token', $user->token, time() + 180*86400, '/', $request->getDomain(), false, true);
                $response->cookie('shopfai_user_token_auth', $user->token_auth, time() + 180*86400, '/', $request->getDomain(), false, true);
                Be::setUser($user);
            }
        }

        if (!$request->isAjax()) {
            // 访客统计
            Be::getService('App.ShopFai.Statistic')->visit();
        }
    }

}

