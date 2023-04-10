<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("缓存")
 */
class Cache
{

    /**
     * @BeConfigItem("分类列表", driver="FormItemInputNumberInt")
     */
    public int $categories = 600;

    /**
     * @BeConfigItem("分类", driver="FormItemInputNumberInt")
     */
    public int $category = 600;

    /**
     * @BeConfigItem("商品详情", driver="FormItemInputNumberInt")
     */
    public int $product = 600;

    /**
     * @BeConfigItem("商品列表类", driver="FormItemInputNumberInt")
     */
    public int $products = 600;

    /**
     * @BeConfigItem("热门标签", driver="FormItemInputNumberInt")
     */
    public int $tag = 600;

    /**
     * @BeConfigItem("自定义页面", driver="FormItemInputNumberInt")
     */
    public int $page = 600;

    /**
     * @BeConfigItem("热搜关键词", driver="FormItemInputNumberInt")
     */
    public int $topKeywords = 600;

}

