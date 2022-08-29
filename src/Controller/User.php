<?php

namespace Be\App\ShopFai\Controller;

use Be\App\ControllerException;
use Be\Be;

class User extends Base
{

    /**
     * 登录
     *
     * @BeRoute("/login");
     */
    public function login()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $return = $request->get('return', '');
        $response->set('return', $return);

        $response->display();
    }

    /**
     * 简单登录
     *
     * @BeRoute("/pop-login");
     */
    public function popLogin()
    {
        $response = Be::getResponse();
        $response->display();
    }

    /**
     * 密码登录
     *
     * @BeRoute("/login-check");
     */
    public function loginCheck()
    {
        $my = Be::getUser();
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $email = $request->post('email');
            if (!$email) {
                throw new ControllerException('Please enter your email!');
            }

            $password = $request->post('password');
            if (!$password) {
                throw new ControllerException('Please enter your password!');
            }

            $return = $request->post('return');
            if ($return) {
                $return = base64_decode($return);
            } else {
                $return = beUrl('ShopFai.UserCenter.dashboard');
            }

            $user = Be::getService('App.ShopFai.User')->login($email, $password, $request->getIp(), $my->token);
            if ($user->avatar === '') {
                $user->avatar = Be::getProperty('App.ShopFai')->getWwwUrl() . '/images/user/avatar/default.png';
            }

            $response->cookie('shopfai_user_token', $user->token, time() + 180*86400, '/', $request->getDomain(), false, true);
            $response->cookie('shopfai_user_token_auth', 1, time() + 180*86400, '/', $request->getDomain(), false, true);
            Be::setUser($user);

            $response->set('success', true);
            $response->set('message', '登录成功！');
            $response->set('redirectUrl', $return);
            $response->set('user', $user);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 退出
     *
     * @BeRoute("/logout");
     */
    public function logout()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        Be::setUser(null);
        $response->cookie('shopfai_user_token_auth', '', time() - 1, '/', $request->getDomain(), false, true);
        $response->redirect(beUrl('ShopFai.User.login'));
    }

    /**
     * 注册
     *
     * @BeRoute("/register");
     */
    public function register()
    {
        $response = Be::getResponse();
        $response->display();
    }

    /**
     * 密码登录
     *
     * @BeRoute("/register-save");
     */
    public function registerSave()
    {
        $my = Be::getUser();
        $userId = $my->id;

        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $data = $request->post();
            $data['ip'] = $request->getIp();
            $data['token'] = $my->token;

            $user = Be::getService('App.ShopFai.User')->register($userId, $data);

            $response->cookie('shopfai_user_token', $user->token, time() + 180*86400, '/', $request->getDomain(), false, true);
            $response->cookie('shopfai_user_token_auth', 1, time() + 180*86400, '/', $request->getDomain(), false, true);
            Be::setUser($user);

            $response->set('success', true);
            $response->set('message', 'Create account success!');
            $response->set('redirectUrl', beUrl('ShopFai.User.login'));
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 忘记密码
     *
     * @BeRoute("/forget-password");
     */
    public function forgetPassword()
    {
        $response = Be::getResponse();
        $response->display('App.ShopFai.User.login');
    }

}
