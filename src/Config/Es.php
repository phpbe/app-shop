<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("ES搜索引擎")
 */
class Es
{

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
