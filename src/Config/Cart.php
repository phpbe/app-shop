<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("购物车")
 */
class Cart
{

    /**
     * @BeConfigItem("缓存失效时间（天）", description="购物车优先使用缓存（如Redis）存放，缓存不存在时从数据库中加载载到缓存", driver="FormItemInputNumberInt")
     */
    public int $cacheExpireDays = 30;

    /**
     * @BeConfigItem("最大条数", driver="FormItemInputNumberInt")
     */
    public int $maxItems = 100;

}

