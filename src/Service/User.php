<?php

namespace Be\App\ShopFai\Service;

use Be\App\ServiceException;
use Be\Util\Crypt\Random;
use Be\Util\Validator;
use Be\Be;

class User
{

    /**
     * 输入密码登录
     *
     * @param string $email 邮箱
     * @param string $password 密码
     * @param string $ip IP地址
     * @param string $token 用户TOKEN
     * @return object
     * @throws \Throwable
     */
    public function login(string $email, string $password, string $ip = '', string $token = ''): object
    {
        $email = trim($email);
        if (!$email) {
            throw new ServiceException('Please enter your email!');
        }

        $password = trim($password);
        if (!$password) {
            throw new ServiceException('Please enter your password!');
        }

        $now = date('Y-m-d H:i:s');
        $db = Be::getDb();
        $db->beginTransaction();
        try {
            $tupleUser = Be::getTuple('shopfai_user');

            try {
                $tupleUser->loadBy([
                    'email' => $email
                ]);
            } catch (\Throwable $t) {
                throw new ServiceException('Your account (' . $email . ') does not exist!');
            }

            if ($tupleUser->password === $this->encryptPassword($password, $tupleUser->salt)) {
                if ($tupleUser->is_delete === 1) {
                    throw new ServiceException('Your account (' . $email . ') is unavailable!');
                } elseif ($tupleUser->is_enable === 0) {
                    throw new ServiceException('Your account (' . $email . ') is blocked!');
                } else {
                    $tupleUser->last_login_time = $tupleUser->this_login_time;
                    $tupleUser->this_login_time = $now;
                    $tupleUser->last_login_ip = $tupleUser->this_login_ip;
                    $tupleUser->this_login_ip = $ip;
                    $tupleUser->update_time = $now;
                    $tupleUser->update();
                }
            } else {
                throw new ServiceException('Password error!');
            }

            $tupleUserToken = Be::getTuple('shopfai_user_token');
            try {
                $tupleUserToken->loadBy([
                    'token' => $token
                ]);
            } catch (\Throwable $t) {
            }

            $tokenUserId  = null;
            if ($tupleUserToken->isLoaded()) {
                // 绑定 token 与用户
                $tupleUserToken->user_id = $tupleUser->id;
                $tupleUserToken->update_time = $now;
                $tupleUserToken->update();

                $tokenUserId = '-' . $tupleUserToken->id;
            } else {
                // token 无效时，创建一个新的 token
                $token = null;
                $exist = null;
                do {
                    $token = Random::simple(32);
                    $sql = 'SELECT COUNT(*) FROM shopfai_user_token WHERE token=?';
                    $exist = $db->getValue($sql, [$token]) > 0;
                } while ($exist);

                $tupleUserToken = Be::getTuple('shopfai_user_token');
                $tupleUserToken->user_id = $tupleUser->id;
                $tupleUserToken->token = $token;
                $tupleUserToken->last_login_time = $now;
                $tupleUserToken->this_login_time = $now;
                $tupleUserToken->last_login_ip = $ip;
                $tupleUserToken->this_login_ip = $ip;
                $tupleUserToken->create_time = $now;
                $tupleUserToken->update_time = $now;
                $tupleUserToken->insert();
            }

            $db->commit();

            if ($tokenUserId !== null) {
                $this->importTmpCart($tokenUserId, $tupleUser->id);
            }

            return (object)[
                'id' => $tupleUser->id,
                'email' => $tupleUser->email,
                'token' => $token,
                'token_auth' => 1,
                'first_name' => $tupleUser->first_name,
                'last_name' => $tupleUser->last_name,
                'avatar' => $tupleUser->avatar,
                'last_login_time' => $tupleUser->last_login_time,
                'last_login_ip' => $tupleUser->last_login_ip,
            ];

        } catch (\Throwable $t) {
            $db->rollback();
            throw $t;
        }
    }


    private function importTmpCart(string $tmpUserId, string $userId): bool
    {
        $redis = Be::getRedis();
        $tmpData = $redis->hGetAll('ShopFai:Cart:' . $tmpUserId);
        $data = $redis->hGetAll('ShopFai:Cart:' . $userId);

        if ($tmpData) {
            foreach ($tmpData as $k => $v) {
                if (isset($data[$k])) {
                    $data[$k] = $data[$k] + $v;
                } else {
                    $data[$k] = $v;
                }
            }

            foreach ($data as $k => $v) {
                $redis->hset('ShopFai:Cart:' . $userId, $k, $v);
            }
        }
        $redis->del('ShopFai:Cart:' . $tmpUserId);
        return true;
    }

    /**
     * 用 token 登录
     *
     * @param string $token Token
     * @param int $tokenAuth 是否启用了Token登录
     * @param string $ip IP地址
     * @return object
     * @throws \Throwable
     */
    public function tokenLogin(string $token, int $tokenAuth = 0, string $ip = ''): object
    {
        $db = Be::getDb();
        $now = date('Y-m-d H:i:s');

        $db->beginTransaction();
        try {
            $tupleUserToken = Be::getTuple('shopfai_user_token');
            try {
                $tupleUserToken->loadBy([
                    'token' => $token
                ]);
            } catch (\Throwable $t) {
                throw new ServiceException('Token (' . $token . ') is unavailable!');
            }

            $tupleUserToken->last_login_time = $tupleUserToken->this_login_time;
            $tupleUserToken->this_login_time = $now;
            $tupleUserToken->last_login_ip = $tupleUserToken->this_login_ip;
            $tupleUserToken->this_login_ip = $ip;
            $tupleUserToken->update_time = $now;
            $tupleUserToken->update();

            // 当 token 未绑定用户时 或 不使用 token 登录时，返回 token 用户
            if ($tupleUserToken->user_id === '' || $tokenAuth == 0) {
                $db->commit();
                return (object)[
                    'id' => '-' . $tupleUserToken->id,
                    'email' => '',
                    'token' => $token,
                    'token_auth' => $tokenAuth,
                    'first_name' => 'Guest',
                    'last_name' => '',
                    'avatar' => '',
                    'last_login_time' => $now,
                    'last_login_ip' => $ip,
                ];
            } else {
                $tupleUser = Be::getTuple('shopfai_user');
                $tupleUser->load($tupleUserToken->user_id);

                if ($tupleUser->is_delete === 1) {
                    throw new ServiceException('Token (' . $token . ') is unavailable!');
                } elseif ($tupleUser->is_enable === 0) {
                    throw new ServiceException('The account of token (' . $token . ')  is blocked!');
                } else {
                    $tupleUser->last_login_time = $tupleUser->this_login_time;
                    $tupleUser->this_login_time = date('Y-m-d H:i:s');
                    $tupleUser->last_login_ip = $tupleUser->this_login_ip;
                    $tupleUser->this_login_ip = $ip;
                    $tupleUser->update_time = $now;
                    $tupleUser->update();
                }

                $db->commit();
                return (object)[
                    'id' => $tupleUser->id,
                    'email' => $tupleUser->email,
                    'token' => $token,
                    'token_auth' => $tokenAuth,
                    'first_name' => $tupleUser->first_name,
                    'last_name' => $tupleUser->last_name,
                    'avatar' => $tupleUser->avatar,
                    'last_login_time' => $tupleUser->last_login_time,
                    'last_login_ip' => $tupleUser->last_login_ip,
                ];
            }
        } catch (\Throwable $t) {
            $db->rollback();
        }

        // token 登录失败，创建 token 用户
        return $this->newTokenUser($ip);
    }


    /**
     * 创建 token 用户
     *
     * @param string $ip IP地址
     * @return object
     * @throws \Throwable
     */
    public function newTokenUser(string $ip = ''): object
    {
        $db = Be::getDb();
        $now = date('Y-m-d H:i:s');

        $token = null;
        $exist = null;
        do {
            $token = Random::simple(32);
            $sql = 'SELECT COUNT(*) FROM shopfai_user_token WHERE token=?';
            $exist = $db->getValue($sql, [$token]) > 0;
        } while ($exist);

        $tupleUserToken = Be::getTuple('shopfai_user_token');
        $tupleUserToken->user_id = '';
        $tupleUserToken->token = $token;
        $tupleUserToken->last_login_time = $now;
        $tupleUserToken->this_login_time = $now;
        $tupleUserToken->last_login_ip = $ip;
        $tupleUserToken->this_login_ip = $ip;
        $tupleUserToken->create_time = $now;
        $tupleUserToken->update_time = $now;
        $tupleUserToken->insert();

        return (object)[
            'id' => '-' . $tupleUserToken->id,
            'email' => '',
            'token' => $token,
            'token_auth' => 1,
            'first_name' => 'Guest',
            'last_name' => '',
            'avatar' => '',
            'last_login_time' => $now,
            'last_login_ip' => $ip,
        ];
    }

    /**
     * 用户注册
     * @param string $tmpUserId
     * @param array $data
     * @return object
     */
    public function register(string $tmpUserId, array $data = []): object
    {
        $first_name = $data['first_name'] ?? '';
        $last_name = $data['last_name'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $ip = $data['ip'] ?? '';
        $token = $data['token'] ?? '';

        if (!$first_name) {
            throw new ServiceException('Please enter first name!');
        }
        if (!$last_name) {
            throw new ServiceException('Please enter last name!');
        }
        if (!$email) {
            throw new ServiceException('Please enter email!');
        }
        if (!Validator::isEmail($email)) {
            throw new ServiceException('The email (' . $email . ') is incorrect!');
        }
        if (!$password) {
            throw new ServiceException('Please enter password!');
        }

        $db = Be::getDb();
        $sql = 'SELECT COUNT(*) FROM shopfai_user WHERE email=?';
        if ($db->getValue($sql, [$email]) > 0) {
            throw new ServiceException('Email ' . $email . ' is unavailable!');
        }

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tupleUser = Be::getTuple('shopfai_user');
            $tupleUser->email = $email;
            $tupleUser->first_name = $first_name;
            $tupleUser->last_name = $last_name;
            $tupleUser->salt = Random::complex(32);
            $tupleUser->password = $this->encryptPassword($password, $tupleUser->salt);
            $tupleUser->last_login_time = $now;
            $tupleUser->this_login_time = $now;
            $tupleUser->last_login_ip = '0.0.0.0';
            $tupleUser->this_login_ip = $ip;
            $tupleUser->create_time = $now;
            $tupleUser->update_time = $now;
            $tupleUser->insert();

            $sql = 'SELECT COUNT(*) FROM shopfai_order WHERE user_id=\'\' AND email=?';
            if ($db->getValue($sql, [$email]) > 0) {
                // 将未登录前创建的订单与用户关联
                $sql = 'UPDATE shopfai_order SET user_id =? WHERE user_id=\'\' AND email=?';
                $db->query($sql, [$tupleUser->id, $email]);
            }

            $configStore = Be::getConfig('App.ShopFai.Store');

            $rootUrl = Be::getRequest()->getRootUrl();
            $mailSubject = 'Welcome to ' . $configStore->name;
            $mailBody = '<div style="width:700px; margin:10px auto;">';
            $mailBody .= '<div style="font-family:Arial; padding:15px 0; line-height:150%; min-height:100px; _height:100px; color:#333; font-size:12px;">';
            $mailBody .= 'Dear <strong>' . $first_name . ' ' . $last_name . '</strong>:<br /><br />';
            $mailBody .= 'This is an automated email from <a href="' . $rootUrl . '" target="_blank">' . $configStore->name . '</a>. ';
            $mailBody .= 'Please do not reply to this email.<br /><br />';
            $mailBody .= 'Thanks for choosing ' . $configStore->name . '.<br /><br />';
            $mailBody .= 'You have been a member of our company and welcome to join us to enjoy the convenient and safe shopping experience. Your account information is as follows:<br />';
            $mailBody .= '-------------------------------------------------------------------------------------------<br />';
            $mailBody .= '<div style="height:24px; line-height:24px; clear:both;">';
            $mailBody .= '    <div style="float:left; width:92px;">Your Username</div>';
            $mailBody .= '    <div style="float:left; width:400px;">: ' . $first_name . ' ' . $last_name . '</div>';
            $mailBody .= '</div>';
            $mailBody .= '<div style="height:24px; line-height:24px; clear:both;">';
            $mailBody .= '    <div style="float:left; width:92px;">Your E-mail</div>';
            $mailBody .= '    <div style="float:left; width:400px;">: ' . $email . '/div>';
            $mailBody .= '</div>';
            $mailBody .= '<div style="height:24px; line-height:24px; clear:both;">';
            $mailBody .= '    <div style="float:left; width:92px;">Your Password</div>';
            $mailBody .= '    <div style="float:left; width:400px;">: ********</div>';
            $mailBody .= '</div><br /><br />';
            $mailBody .= 'Please click the following link or copy and paste the link into your address bar of your web browser to shopping:<br />';
            $mailBody .= '<a href = "' . $rootUrl . '" target = "_blank"><strong > ' . $rootUrl . '</strong></a><br /><br />';
            $mailBody .= 'Yours sincerely,<br /><br />';
            $mailBody .= $configStore->name . ' Customer Care Team';
            $mailBody .= '</div>';
            $mailBody .= '<div style = "padding:20px 0; line-height:180%; font-family:Arial; font-size:12px; color:#000; border-top:1px solid #ccc; border-bottom:1px solid #ccc;">';
            $mailBody .= 'You have received this email because you are a registered member of the ' . $configStore->name . ' website .<br />';
            $mailBody .= 'for further information, log in to your account at: <a href = "' . $rootUrl . '" target = "_blank" style = "font-family:Arial; font-size:12px; color:#1E5494; text-decoration:underline;" >' . $rootUrl . '</a> and submit your request or use live chat.';
            $mailBody .= '</div>';
            $mailBody .= '</div>';

            Be::getService('App.System.MailQueue')->send($email, $mailSubject, $mailBody);

            $tupleUserToken = Be::getTuple('shopfai_user_token');
            if ($token) {
                try {
                    $tupleUserToken->loadBy([
                        'token' => $token
                    ]);
                } catch (\Throwable $t) {
                }
            }

            $tokenUserId  = null;
            if ($tupleUserToken->isLoaded()) {
                // 绑定 token 与用户
                $tupleUserToken->user_id = $tupleUser->id;
                $tupleUserToken->update_time = $now;
                $tupleUserToken->update();

                $tokenUserId = '-' . $tupleUserToken->id;
            } else {
                // token 无效时，创建一个新的 token
                $token = null;
                $exist = null;
                do {
                    $token = Random::simple(32);
                    $sql = 'SELECT COUNT(*) FROM shopfai_user_token WHERE token=?';
                    $exist = $db->getValue($sql, [$token]) > 0;
                } while ($exist);

                $tupleUserToken = Be::getTuple('shopfai_user_token');
                $tupleUserToken->user_id = $tupleUser->id;
                $tupleUserToken->token = $token;
                $tupleUserToken->last_login_time = $now;
                $tupleUserToken->this_login_time = $now;
                $tupleUserToken->last_login_ip = $ip;
                $tupleUserToken->this_login_ip = $ip;
                $tupleUserToken->create_time = $now;
                $tupleUserToken->update_time = $now;
                $tupleUserToken->insert();
            }

            $db->commit();

            if ($tokenUserId !== null) {
                $this->importTmpCart($tokenUserId, $tupleUser->id);
            }

            return (object)[
                'id' => $tupleUser->id,
                'email' => $tupleUser->email,
                'token' => $token,
                'token_auth' => 1,
                'first_name' => $tupleUser->first_name,
                'last_name' => $tupleUser->last_name,
                'avatar' => $tupleUser->avatar,
                'last_login_time' => $tupleUser->last_login_time,
                'last_login_ip' => $tupleUser->last_login_ip,
            ];

        } catch (\Throwable $t) {
            $db->rollback();

            Be::getLog()->error($t);
            throw new ServiceException('Create account exception');
        }
    }

    /**
     * 密码 Hash
     *
     * @param string $password 密码
     * @param string $salt 盐值
     * @return string
     */
    public function encryptPassword(string $password, string $salt): string
    {
        return sha1(sha1($password) . $salt);
    }


    /**
     * 获取用户资料
     *
     * @return object
     * @throws ServiceException
     */
    public function getUser(): object
    {
        $my = Be::getUser();
        $tupleUser = Be::getTuple('shopfai_user');
        try {
            $tupleUser->load($my->id);
        } catch (\Throwable $t) {
            throw new ServiceException('User (#' . $my->id . ') does not exist!');
        }

        return $tupleUser->toObject();
    }

    /**
     * 修改密码
     *
     * @param array $profile 用户信息
     * @throws \Throwable
     */
    public function updateProfile(array $profile)
    {
        $my = Be::getUser();
        $tupleUser = Be::getTuple('shopfai_user');
        try {
            $tupleUser->load($my->id);
        } catch (\Throwable $t) {
            throw new ServiceException('User (#' . $my->id . ') does not exist!');
        }

        if (!isset($profile['first_name'])) {
            throw new ServiceException('Please entry first name!');
        }
        $tupleUser->first_name = $profile['first_name'];

        if (!isset($profile['last_name'])) {
            throw new ServiceException('Please entry last name!');
        }
        $tupleUser->last_name = $profile['last_name'];

        $tupleUser->update_time = date('Y-m-d H:i:s');
        $tupleUser->update();
    }

    /**
     * 修改密码
     *
     * @param string $password 密码
     * @param string $email 邮箱
     * @throws \Throwable
     */
    public function changeEmail(string $password, string $email)
    {
        if (!$password) {
            throw new ServiceException('Please entry password!');
        }

        if (!$email) {
            throw new ServiceException('Please entry email!');
        }

        if (!Validator::isEmail($email)) {
            throw new ServiceException('The email address you entered is incorrect!');
        }

        $my = Be::getUser();
        $tupleUser = Be::getTuple('shopfai_user');
        try {
            $tupleUser->load($my->id);
        } catch (\Throwable $t) {
            throw new ServiceException('User (#' . $my->id . ') does not exist!');
        }

        if ($tupleUser->password !== $this->encryptPassword($password, $tupleUser->salt)) {
            throw new ServiceException('Existing password is wrong!');
        }

        $sql = 'SELECT COUNT(*) FROM shopfai_user WHERE id!=? AND email=?';
        if (Be::getDb()->getValue($sql, [$my->id, $email]) > 0) {
            throw new ServiceException('Sorry, this email address has already been used!');
        }

        $tupleUser->email = $email;

        $tupleUser->update_time = date('Y-m-d H:i:s');
        $tupleUser->update();
    }

    /**
     * 修改密码
     *
     * @param string $password
     * @param string $newPassword
     * @throws \Throwable
     */
    public function changePassword(string $password, string $newPassword)
    {
        if (!$password) {
            throw new ServiceException('Please entry password!');
        }

        if (!$newPassword) {
            throw new ServiceException('Please entry new password!');
        }

        $my = Be::getUser();
        $tupleUser = Be::getTuple('shopfai_user');
        try {
            $tupleUser->load($my->id);
        } catch (\Throwable $t) {
            throw new ServiceException('User (#' . $my->id . ') does not exist!');
        }

        if ($tupleUser->password !== $this->encryptPassword($password, $tupleUser->salt)) {
            throw new ServiceException('Existing password is wrong!');
        }

        $tupleUser->salt = Random::complex(32);
        $tupleUser->password = $this->encryptPassword($newPassword, $tupleUser->salt);
        $tupleUser->update_time = date('Y-m-d H:i:s');
        $tupleUser->update();
    }


}
