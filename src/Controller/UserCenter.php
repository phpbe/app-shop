<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class UserCenter extends Auth
{

    /**
     * 用户中心
     *
     * @BeMenu("用户 - 控制台")
     * @BeRoute("/dashbaord")
     */
    public function dashboard()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $response->display();
    }

    /**
     * 账号设置
     *
     * @BeMenu("用户 - 账号设置")
     * @BeRoute("/setting")
     */
    public function setting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $profile = Be::getService('App.Shop.User')->getUser();
        $response->set('profile', $profile);

        $response->display();
    }

    /**
     * 修改资料
     *
     * @BeRoute("/update-profile")
     */
    public function updateProfile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            Be::getService('App.Shop.User')->updateProfile($request->post());
            $response->set('success', true);
            $response->set('message', 'Update your profile success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 修改邮箱
     *
     * @BeRoute("/change-email")
     */
    public function changeEmail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $password = $request->post('password');
            $email = $request->post('email');
            Be::getService('App.Shop.User')->changeEmail($password, $email);
            $response->set('success', true);
            $response->set('message', 'Change your email success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 修改密码
     *
     * @BeRoute("/change-password")
     */
    public function changePassword()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $password = $request->post('password');
            $newPassword = $request->post('new_password');
            Be::getService('App.Shop.User')->changePassword($password, $newPassword);
            $response->set('success', true);
            $response->set('message', 'Change your password success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }


}
