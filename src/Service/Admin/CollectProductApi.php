<?php

namespace Be\App\Shop\Service\Admin;

use Be\Be;
use Be\Config\ConfigHelper;
use Be\Util\Crypt\Random;

/**
 * 商品采集接口
 */
class CollectProductApi
{

    /**
     * 获取商品采集接口配置
     *
     * @return object
     */
    public function getConfig(): object
    {
        $configCollectProductApi = Be::getConfig('App.Shop.CollectProductApi');

        if ($configCollectProductApi->token === '') {
            $configCollectProductApi->token = Random::simple(32);

            ConfigHelper::update('App.Shop.CollectProductApi', $configCollectProductApi);

            if (Be::getRuntime()->isSwooleMode()) {
                Be::getRuntime()->reload();
            }
        }

        return $configCollectProductApi;
    }

    /**
     * 商品采集接口 - 切换启用状态
     *
     * @return int
     */
    public function toggleEnable(): int
    {
        $configCollectProductApi = Be::getConfig('App.Shop.CollectProductApi');

        $configCollectProductApi->enable = 1 - (int)$configCollectProductApi->enable;

        ConfigHelper::update('App.Shop.CollectProductApi', $configCollectProductApi);

        if (Be::getRuntime()->isSwooleMode()) {
            Be::getRuntime()->reload();
        }

        return $configCollectProductApi->enable;
    }

    /**
     * 商品采集接口 - 重置Token
     *
     * @return string
     */
    public function resetToken(): string
    {
        $configCollectProductApi = Be::getConfig('App.Shop.CollectProductApi');

        $configCollectProductApi->token = Random::simple(32);

        ConfigHelper::update('App.Shop.CollectProductApi', $configCollectProductApi);

        if (Be::getRuntime()->isSwooleMode()) {
            Be::getRuntime()->reload();
        }

        return $configCollectProductApi->token;
    }

}
