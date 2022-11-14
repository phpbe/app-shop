<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("PayPal支付")
 */
class PaymentPaypal
{

    /**
     * @BeConfigItem("弹窗支付", description="开启后，使用Paypal支付时，页面不跳转，在弹出的窗口中进行PayPal支付", driver="FormItemSwitch")
     */
    public int $pop = 0;


}
