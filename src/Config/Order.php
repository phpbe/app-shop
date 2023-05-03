<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("订单")
 */
class Order
{

    /**
     * @BeConfigItem("订单编号前缀",
     *     driver="FormItemInput")
     */
    public string $snPrefix = 'SO';

    /**
     * @BeConfigItem("付款超时时间（秒）",
     *     driver="FormItemInputNumberInt",
     *     ui="return [':min' => 600];")
     */
    public int $paymentExpireTime = 86400;


}
