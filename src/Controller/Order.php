<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class Order extends Auth
{
    /**
     * 我的订单
     *
     * @BeMenu("用户 - 订单列表")
     * @BeRoute("/orders")
     */
    public function orders()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $option = [];

        $status = $request->get('status', 'ALL');
        $option['status'] = $status;
        $response->set('status', $status);

        $keywords = $request->get('keywords', '');
        $keywords = urldecode($keywords);
        $option['keywords'] = $keywords;
        $response->set('keywords', $keywords);

        Be::getService('App.Shop.Order')->paymentExpire();

        $total = Be::getService('App.Shop.Order')->getCount($option);
        $response->set('total', $total);

        $pageSize = $request->get('page_size', 12);
        $page = $request->get('page', 1);

        if ($pageSize < 1) $pageSize = 1;
        if ($pageSize > 100) $pageSize = 100;
        if ($page <= 0) $page = 1;
        $pages = $total > 0 ? ceil($total / $pageSize) : 1;
        if ($page > $pages) $page = $pages;
        $response->set('pageSize', $pageSize);
        $response->set('pages', $pages);
        $response->set('page', $page);

        $option['pageSize'] = $pageSize;
        $option['page'] = $page;

        $orders = Be::getService('App.Shop.Order')->getOrders($option);
        $response->set('orders', $orders);

        $statusKeyValues = Be::getService('App.Shop.Order')->getStatusKeyValues();
        $response->set('statusKeyValues', $statusKeyValues);

        $response->display();
    }

    /**
     * 订单明细
     *
     * @BeRoute("/order")
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $orderId = $request->get('order_id');

        $order = Be::getService('App.Shop.Order')->getOrder($orderId, ['products' => 1, 'shipping_address' => 1, 'billing_address' => 1]);
        $response->set('order', $order);

        $response->display();
    }


    /**
     * 订单打印
     *
     * @BeRoute("/order/print")
     */
    public function printOrder()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $orderId = $request->get('order_id');

        $order = Be::getService('App.Shop.Order')->getOrder($orderId, ['products' => 1]);
        $response->set('order', $order);

        $response->display();
    }

    /**
     * 订单联系
     *
     * @BeRoute("/order/contact")
     */
    public function contact()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $orderId = $request->get('order_id');

        $order = Be::getService('App.Shop.Order')->getOrder($orderId);
        $response->set('order', $order);

        $contacts = Be::getService('App.Shop.Order')->getContacts($orderId);
        $response->set('contacts', $contacts);

        $response->display();
    }

    /**
     * @BeRoute("/order/contact-save")
     */
    public function contactSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $orderId = $request->post('order_id');

            $content = $request->post('content', '');
            $content = trim($content);

            $image = $request->files('image');
            Be::getService('App.Shop.Order')->contact($orderId, $content, $image);

            $response->set('success', true);
            $response->set('message', 'Submit success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 订单取消
     *
     * @BeRoute("/order/cancel")
     */
    public function cancel()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $orderId = $request->get('order_id');

        $order = Be::getService('App.Shop.Order')->getOrder($orderId);

        if ($order->status != 'pending') {
            $response->error('Order status exception!');
            return;
        }

        $response->set('order', $order);
        $response->display();
    }

    /**
     *
     * @BeRoute("/order/cancel-save")
     */
    public function cancelSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $orderId = $request->post('order_id');

            $reason = $request->post('reason', '');
            $reason = trim($reason);
            if ($reason === '') {
                $response->error('Please enter cancel reason!');
                return;
            }

            Be::getService('App.Shop.Order')->cancel($orderId, $reason);
            $response->set('success', true);
            $response->set('message', 'Cancel order success!');
            $response->set('redirectUrl', beUrl('Shop.Order.orders'));
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }


}
