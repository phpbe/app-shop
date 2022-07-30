<?php
namespace Be\App\ShopFai\Config;

/**
 * @BeConfig("ES搜索引擎")
 */
class Es
{

    /**
     * @BeConfigItem("商品索引", driver="FormItemInput")
     */
    public string $indexProduct = 'shopfai.product';

    /**
     * @BeConfigItem("商品访问记录索引", driver="FormItemInput")
     */
    public string $indexProductHistory = 'shopfai.product_history';

    /**
     * @BeConfigItem("商品搜索记录索引", driver="FormItemInput")
     */
    public string $indexProductSearchHistory = 'shopfai.product_search_history';

    /**
     * @BeConfigItem("订单", driver="FormItemInput")
     */
    public string $indexOrder = 'shopfai.order';

    /**
     * @BeConfigItem("访客统计", driver="FormItemInput")
     */
    public string $indexStatisticVisit = 'shopfai.statistic.visit';

    /**
     * @BeConfigItem("购物车统计", driver="FormItemInput")
     */
    public string $indexStatisticCart = 'shopfai.statistic.cart';

}
