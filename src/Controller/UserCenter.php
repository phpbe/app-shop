<?php

namespace Be\App\ShopFai\Controller;

use Be\Be;

class UserCenter extends Auth
{

    /**
     * 用户中心
     *
     * @BeRoute("/dashbaord");
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
     * @BeRoute("/setting");
     */
    public function setting()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        $profile = Be::getService('App.ShopFai.User')->getUser($userId);
        $response->set('profile', $profile);

        $response->display();
    }

    /**
     * 修改资料
     *
     * @BeRoute("/update-profile");
     */
    public function updateProfile()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        try {
            Be::getService('App.ShopFai.User')->updateProfile($userId, $request->post());
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
     * @BeRoute("/change-email");
     */
    public function changeEmail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        try {
            $password = $request->post('password');
            $email = $request->post('email');
            Be::getService('App.ShopFai.User')->changeEmail($userId, $password, $email);
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
     * @BeRoute("/change-password");
     */
    public function changePassword()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        try {
            $password = $request->post('password');
            $newPassword = $request->post('new_password');
            Be::getService('App.ShopFai.User')->changePassword($userId, $password, $newPassword);
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
