<?php

namespace Be\App\ShopFai\Controller;

use Be\Be;

class PaymentCod extends Base
{

    /**
     *
     * @BeRoute("/payment/cod")
     */
    public function pay()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $orderId = $request->get('order_id');
        $response->redirect(beUrl('ShopFai.Order.detail', ['order_id' => $orderId]));
    }


}

