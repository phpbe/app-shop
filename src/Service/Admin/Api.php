<?php

namespace Be\App\Shop\Service\Admin;

use Be\Be;
use Be\Config\ConfigHelper;
use Be\Util\Crypt\Random;

/**
 * Api接口
 */
class Api
{

    /**
     * 获取Api接口配置
     *
     * @return object
     */
    public function getConfig(): object
    {
        $configApi = Be::getConfig('App.Shop.Api');

        if ($configApi->token === '') {
            $configApi->token = Random::simple(32);

            ConfigHelper::update('App.Shop.Api', $configApi);

            if (Be::getRuntime()->isSwooleMode()) {
                Be::getRuntime()->reload();
            }
        }

        return $configApi;
    }

    /**
     * Api接口 - 切换启用状态
     *
     * @return int
     */
    public function toggleEnable(): int
    {
        $configApi = Be::getConfig('App.Shop.Api');

        $configApi->enable = 1 - (int)$configApi->enable;

        ConfigHelper::update('App.Shop.Api', $configApi);

        if (Be::getRuntime()->isSwooleMode()) {
            Be::getRuntime()->reload();
        }

        return $configApi->enable;
    }

    /**
     * Api接口 - 重置Token
     *
     * @return string
     */
    public function resetToken(): string
    {
        $configApi = Be::getConfig('App.Shop.Api');

        $configApi->token = Random::simple(32);

        ConfigHelper::update('App.Shop.Api', $configApi);

        if (Be::getRuntime()->isSwooleMode()) {
            Be::getRuntime()->reload();
        }

        return $configApi->token;
    }

}
