<?php

namespace Be\App\Shop\Controller;

use Be\Be;

/**
 * 优惠券
 *
 * Class PromotionCoupon
 * @package Be\App\Shop\Controller
 */
class PromotionCoupon extends Base
{

    /**
     * 校验优惠券否可用
     *
     * @BeRoute("/promotion/coupon/check")
     */
    public function check()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            Be::getService('App.Shop.PromotionCoupon')->check($request->post());
            $response->set('success', true);
            $response->set('message', 'Your discount code is available!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }


}
