<?php

namespace Be\App\Shop\Controller;

use Be\Be;

/**
 * 优惠
 *
 * Class Promotion
 * @package Be\App\Shop\Controller
 */
class Promotion extends Base
{

    /**
     * 结算
     *
     * @BeRoute("/promotion/get-discount-amount")
     */
    public function getDiscountAmount()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $productId = $request->post('product_id');
        $productItemId = $request->post('product_item_id');
        $quantity = $request->post('quantity');

        if (!is_array($productId)) {
            $productId = [$productId];
        }

        if (!is_array($productItemId)) {
            $productItemId = [$productItemId];
        }

        if (!is_array($quantity)) {
            $quantity = [$quantity];
        }

        try {
            $products = Be::getService('App.Shop.Cart')->formatProducts($productId, $productItemId, $quantity, true);

            $cart = $request->post();
            $cart['products'] = $products;
            $discountAmount = Be::getService('App.Shop.Promotion')->getDiscountAmount($cart);

            $response->set('success', true);
            $response->set('message', 'Get promotion discount success!');
            $response->set('discountAmount', $discountAmount);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

}
