<?php
namespace Be\App\ShopFai\Config;

/**
 * @BeConfig("订单")
 */
class Order
{

    /**
     * @BeConfigItem("订单编号前缀",
     *     driver="FormItemInput")
     */
    public $snPrefix = 'SO';

    /**
     * @BeConfigItem("付款超时时间（秒）",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 600];")
     */
    public $paymentExpireTime = 86400;


}
