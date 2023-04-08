<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("ES搜索引擎")
 */
class Es
{

    /**
     * @BeConfigItem("是否启用ES搜索引擎",
     *     description="启用后，商品变更将同步到ES搜索引擎，检索相关的功能将由ES接管",
     *     driver="FormItemSwitch"
     * )
     */
    public int $enable = 0;

    /**
     * @BeConfigItem("商品索引", driver="FormItemInput")
     */
    public string $indexProduct = 'shop.product';

    /**
     * @BeConfigItem("商品访问记录索引", driver="FormItemInput")
     */
    public string $indexProductHistory = 'shop.product_history';

    /**
     * @BeConfigItem("商品搜索记录索引", driver="FormItemInput")
     */
    public string $indexProductSearchHistory = 'shop.product_search_history';

    /**
     * @BeConfigItem("订单", driver="FormItemInput")
     */
    public string $indexOrder = 'shop.order';

    /**
     * @BeConfigItem("访客统计", driver="FormItemInput")
     */
    public string $indexStatisticVisit = 'shop.statistic.visit';

    /**
     * @BeConfigItem("购物车统计", driver="FormItemInput")
     */
    public string $indexStatisticCart = 'shop.statistic.cart';

}
