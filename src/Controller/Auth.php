<?php

namespace Be\App\Shop\Controller;

use Be\App\ControllerException;
use Be\Be;

class Auth extends Base
{

    public function __construct()
    {
        parent::__construct();

        $my = Be::getUser();
        if ($my->isGuest()) {
            $request = Be::getRequest();

            $redirect = null;
            if ($request->isAjax()) {
                $redirectUrl = beUrl('Shop.User.login');
                $redirect = [
                    'url' => $redirectUrl
                ];
            } else {
                $return = $request->getUrl();
                $redirectUrl = beUrl('Shop.User.login', ['return' => base64_encode($return)]);
                $redirect = [
                    'url' => $redirectUrl,
                    'message' => 'Redirect to <a href="{url}">Login page</a> after {timeout} seconds.',
                    'timeout' => 3,
                ];
            }

            throw new ControllerException('Login timeout!', 0, $redirect);
        }
    }

}

