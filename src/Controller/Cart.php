<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class Cart extends Base
{

    /**
     * 购物车
     *
     * @BeRoute("/cart")
     */
    public function index()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $products = Be::getService('App.Shop.Cart')->getProducts();
        $response->set('products', $products);

        $productTotalQuantity = 0;
        $productTotalAmount = '0.00';
        foreach ($products as $product) {
            $productTotalQuantity += $product->quantity;
            $productTotalAmount = bcadd($productTotalAmount, $product->amount, 2);
        }
        $response->set('productTotalQuantity', $productTotalQuantity);
        $response->set('productTotalAmount', $productTotalAmount);
        $response->set('shippingFee', '0.00');

        $cart = [];
        $cart['products'] = $products;
        $discountAmount = Be::getService('App.Shop.Promotion')->getDiscountAmount($cart);
        $response->set('discountAmount', $discountAmount);

        $totalAmount = bcsub($productTotalAmount, $discountAmount, 2);
        $response->set('totalAmount', $totalAmount);

        $response->set('hideHeaderCart', 1);

        $response->set('title', 'Shopping Cart');
        $response->display();
    }

    /**
     * 结算
     *
     * @BeRoute("/checkout")
     */
    public function checkout()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $from = $request->get('from', 'cart');
        $response->set('from', $from);

        $cart = $request->post();
        $products = Be::getService('App.Shop.Cart')->formatProducts($request->post(), true);
        $response->set('products', $products);

        $cart['products'] = $products;

        $productTotalQuantity = 0;
        $productTotalAmount = '0.00';
        foreach ($products as $product) {
            $productTotalQuantity += $product->quantity;
            $productTotalAmount = bcadd($productTotalAmount, $product->amount, 2);
        }
        $response->set('productTotalQuantity', $productTotalQuantity);
        $response->set('productTotalAmount', $productTotalAmount);
        $response->set('shippingFee', '0.00');

        $discountAmount = Be::getService('App.Shop.Promotion')->getDiscountAmount($cart);
        $response->set('discountAmount', $discountAmount);

        $totalAmount = bcsub($productTotalAmount, $discountAmount, 2);
        $response->set('totalAmount', $totalAmount);

        $response->set('hideHeaderCart', 1);

        $response->set('title', 'Checkout');
        $response->display();
    }

    /**
     * Ajax 提交订单
     *
     * @BeRoute("/checkout-save")
     */
    public function checkoutSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $order = Be::getService('App.Shop.Cart')->checkout($request->post());
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
            return;
        }

        $response->set('success', true);
        $response->set('message', 'Check out success!');
        $redirectUrl = beUrl('Shop.Payment.pay', ['order_id' => $order->id]);
        $response->set('redirectUrl', $redirectUrl);
        $response->json();
    }

    /**
     * 结算
     *
     * @BeRoute("/cart/add")
     */
    public function add()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $productId = $request->post('product_id');
        $productItemId = $request->post('product_item_id');
        $quantity = $request->post('quantity', 1);

        Be::getService('App.Shop.Cart')->add($productId, $productItemId, $quantity);
        $response->set('success', true);
        $response->set('message', 'Added to cart!');
        $response->json();

        Be::getService('App.Shop.Statistic')->cart($productId, $productItemId);
    }

    /**
     * 结算
     *
     * @BeRoute("/cart/change")
     */
    public function change()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $productId = $request->post('product_id');
        $productItemId = $request->post('product_item_id');
        $quantity = $request->post('quantity', 1);

        Be::getService('App.Shop.Cart')->change($productId, $productItemId, $quantity);
        $response->set('success', true);
        $response->set('message', 'Cart item quantity is changed!');
        $response->json();
    }

    /**
     * 结算
     *
     * @BeRoute("/cart/remove")
     */
    public function remove()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $productId = $request->post('product_id');
        $productItemId = $request->post('product_item_id');

        Be::getService('App.Shop.Cart')->remove($productId, $productItemId);
        $response->set('success', true);
        $response->set('message', 'Cart item is deleted!');
        $response->json();
    }

    /**
     * Ajax 获取购物车信息
     *
     * @BeRoute("/cart/get-products")
     */
    public function getProducts()
    {
        $response = Be::getResponse();

        $products = Be::getService('App.Shop.Cart')->getProducts();
        $response->set('products', $products);

        $productTotalQuantity = 0;
        $productTotalAmount = '0.00';
        foreach ($products as $product) {
            $productTotalQuantity += $product->quantity;
            $productTotalAmount = bcadd($productTotalAmount, $product->amount, 2);
        }
        $response->set('productTotalQuantity', $productTotalQuantity);
        $response->set('productTotalAmount', $productTotalAmount);
        $response->set('shippingFee', '0.00');

        $cart = [];
        $cart['products'] = $products;
        $discountAmount = Be::getService('App.Shop.Promotion')->getDiscountAmount($cart);
        $response->set('discountAmount', $discountAmount);

        $totalAmount = bcsub($productTotalAmount, $discountAmount, 2);
        $response->set('totalAmount', $totalAmount);

        $response->set('success', true);
        $response->set('message', 'Fetch cart items success!');
        $response->json();
    }

}

