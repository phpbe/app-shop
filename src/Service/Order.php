<?php

namespace Be\App\ShopFai\Service;

use Be\App\ServiceException;
use Be\Be;

class Order
{

    /**
     * 获取订单列表
     *
     * @param $userId
     * @return array
     */
    public function getStatusKeyValues()
    {
        return [
            'pending' => 'Awaiting Payment',
            'paid' => 'Awaiting Shipping',
            'expired' => 'Payment Overtime',
            'shipped' => 'Shipped',
            'received' => 'Received',
            'cancelled' => 'Cancelled'
        ];
    }

    /**
     * 设置订单的支付方式
     *
     * @param string $orderId
     * @param string $paymentId
     * @param string $paymentItemId
     * @return object
     */
    public function setPayment(string $orderId, string $paymentId, string $paymentItemId): object
    {
        $tupleOrder = Be::getTuple('shopfai_order');
        try {
            $tupleOrder->load($orderId);
        } catch (\Throwable $t) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        if ($tupleOrder->status !== 'pending') {
            throw new ServiceException('Order (#' . $tupleOrder->order_sn . ') current status could not change payment method!');
        }

        $servicePayment = Be::getService('App.ShopFai.Payment');
        $storePayment = $servicePayment->getStorePayment($paymentId, $paymentItemId);

        $cod = 0;
        if ($storePayment->name === 'cod') {
            if ($tupleOrder->shipping_plan_id === '') {
                throw new ServiceException('Order (#' . $tupleOrder->order_sn . ') shipping method does support payment: ' . $storePayment->label . '!');
            }

            $tupleShippingPlan = Be::getTuple('shopfai_shipping_plan');
            try {
                $tupleShippingPlan->load($tupleOrder->shipping_plan_id);
            } catch (\Throwable $t) {
                throw new ServiceException('Order (#' . $tupleOrder->order_sn . ') shipping method does not exist!');
            }

            if ($tupleShippingPlan->cod === 0) {
                throw new ServiceException('Order (#' . $tupleOrder->order_sn . ') shipping method ('.$tupleShippingPlan->name.') does support payment: ' . $storePayment->label . '!');
            }

            $cod = 1;
        }

        $tupleOrder->payment_id = $paymentId;
        $tupleOrder->payment_item_id = $paymentItemId;
        $tupleOrder->cod = $cod;
        if ($cod) {
            $tupleOrder->status = 'paid';
        }

        $tupleOrder->update_time = date('Y-m-d H:i:s');
        $tupleOrder->update();

        return $storePayment;
    }

    /**
     * 未支付的订单自动取消
     */
    public function paymentExpire()
    {
        $my = Be::getUser();
        $now = date('Y-m-d H:i:s');

        Be::getTable('shopfai_order')
            ->where('user_id', $my->id)
            ->where('is_delete', 0)
            ->where('status', 'pending')
            ->where('pay_expire_time', '<', $now)
            ->update([
                'status' => 'expired',
                'update_time' => $now,
            ]);
    }

    /**
     * 获取订单总数
     *
     * @param array $option
     * @return int
     */
    public function getCount(array $option = []): int
    {
        $my = Be::getUser();
        $db = Be::getDb();

        $sql = 'SELECT COUNT(*) FROM shopfai_order WHERE user_id = \'' . $my->id . '\' AND is_delete = 0';

        if (isset($option['status']) && $option['status']) {
            if ($option['status'] != 'ALL') {
                $sql .= ' AND status = ' . $db->quoteValue($option['status']);
            }
        } elseif (isset($option['statusIn']) && $option['statusIn']) {
            $statusIn = [];
            foreach ($option['statusIn'] as $x) {
                $statusIn[] = $db->quoteValue($x);
            }
            $sql .= ' AND status IN(' . implode(',', $statusIn) . ')';
        }

        if (isset($option['keywords']) && $option['keywords']) {
            $sql .= ' AND (order_sn LIKE ' . $db->quoteValue('%' . $option['keywords'] . '%') . '';
            $sql .= ' OR id IN(SELECT order_id FROM shopfai_order_product WHERE user_id = ' . $my->id . ' AND product_name LIKE ' . $db->quoteValue('%' . $option['keywords'] . '%') . '))';
        }

        return $db->getValue($sql);
    }

    /**
     * 获取订单列表
     *
     * @param array $option
     * @return array
     */
    public function getOrders(array $option = []): array
    {
        $my = Be::getUser();
        $db = Be::getDb();

        $sql = 'SELECT * FROM shopfai_order WHERE user_id = \'' . $my->id . '\' AND is_delete = 0';

        if (isset($option['status']) && $option['status']) {
            if ($option['status'] != 'ALL') {
                $sql .= ' AND status = ' . $db->quoteValue($option['status']);
            }
        } elseif (isset($option['statusIn']) && is_array($option['statusIn']) && $option['statusIn']) {
            $statusIn = [];
            foreach ($option['statusIn'] as $x) {
                $statusIn[] = $db->quoteValue($x);
            }
            $sql .= ' AND status IN(' . implode(',', $statusIn) . ')';
        }

        if (isset($option['keywords']) && $option['keywords']) {
            $sql .= ' AND (order_sn LIKE ' . $db->quoteValue('%' . $option['keywords'] . '%') . '';
            $sql .= ' OR id IN(SELECT order_id FROM shopfai_order_product WHERE user_id = ' . $my->id . ' AND product_name LIKE ' . $db->quoteValue('%' . $option['keywords'] . '%') . '))';
        }

        $sql .= ' ORDER BY id DESC';

        $pageSize = 10;
        $page = 1;
        if (isset($option['pageSize']) && is_numeric($option['pageSize']) && $option['pageSize'] >= 1 && $option['pageSize'] <= 100) {
            $pageSize = $option['pageSize'];
        }
        if (isset($option['page']) && is_numeric($option['page']) && $option['page'] >= 1) {
            $page = $option['page'];
        }
        $sql .= ' LIMIT ' . ($page - 1) * $pageSize . ',' . $pageSize;

        $statusKeyValues = $this->getStatusKeyValues();

        $orders = $db->getObjects($sql);
        foreach ($orders as &$order) {
            $order->status_name = $statusKeyValues[$order->status];

            $sql = 'SELECT * FROM shopfai_order_product WHERE order_id=?';
            $order->products = $db->getObjects($sql, [$order->id]);
        }

        return $orders;
    }

    /**
     * 获取订单
     *
     * @param string $userId
     * @param string $orderId
     * @param array $option
     * @return object
     */
    public function getOrder(string $orderId, array $with = []): object
    {
        $my = Be::getUser();
        $db = Be::getDb();

        $tupleOrder = Be::getTuple('shopfai_order');
        try {
            $tupleOrder->load($orderId);
        } catch (\Throwable $t) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        if ($tupleOrder->user_id !== $my->id) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        $order = $tupleOrder->toObject();

        $statusKeyValues = $this->getStatusKeyValues();
        $order->status_name = $statusKeyValues[$order->status];

        if (isset($with['shipping_address']) && $with['shipping_address']) {
            $sql = 'SELECT * FROM shopfai_order_shipping_address WHERE order_id=?';
            $order->shipping_address = $db->getObject($sql, [$orderId]);
        }

        if (isset($with['billing_address']) && $with['billing_address']) {
            $sql = 'SELECT * FROM shopfai_order_billing_address WHERE order_id=?';
            $order->billing_address = $db->getObject($sql, [$orderId]);
        }

        if (isset($with['products']) && $with['products']) {
            $sql = 'SELECT * FROM shopfai_order_product WHERE order_id=?';
            $order->products = $db->getObjects($sql, [$orderId]);
        }

        return $order;
    }

    /**
     * 获取订单联系信息
     *
     * @param string $orderId
     * @return array
     */
    public function getContacts(string $orderId): array
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM shopfai_order_contact WHERE order_id=? ORDER BY create_time ASC';
        $contacts = $db->getObjects($sql, [$orderId]);

        return $contacts;
    }

    /**
     * 取消订单
     *
     * @param string $orderId 订单ID
     * @param string $reason 取消原因
     * @throws \Throwable
     */
    public function cancel(string $orderId, string $reason)
    {
        $my = Be::getUser();

        $tupleOrder = Be::getTuple('shopfai_order');
        try {
            $tupleOrder->load($orderId);
        } catch (\Throwable $t) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        if ($tupleOrder->user_id !== $my->id) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        if ($tupleOrder->status != 'pending') {
            throw new ServiceException('Order (#' . $orderId . ') current status could not cancel!');
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {

            $now = date('Y-m-d H:i:s');
            $tupleOrder->status = 'cancelled';
            $tupleOrder->cancel_time = $now;
            $tupleOrder->update_time = $now;
            $tupleOrder->update();

            $tupleOrderCancel = Be::getTuple('shopfai_order_cancel');
            $tupleOrderCancel->order_id = $orderId;
            $tupleOrderCancel->reason = $reason;
            $tupleOrderCancel->create_time = $now;
            $tupleOrderCancel->update_time = $now;
            $tupleOrderCancel->insert();

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException('Cancel order exception (log id: ' . $logId . ')');
        }
    }

    /**
     * 联系
     *
     * @param string $orderId 订单ID
     * @param string $content 内容
     * @param array $file 图像
     * @throws \Throwable
     */
    public function contact(string $orderId, string $content, array $file)
    {
        $my = Be::getUser();

        $tupleOrder = Be::getTuple('shopfai_order');
        try {
            $tupleOrder->load($orderId);
        } catch (\Throwable $t) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        if ($tupleOrder->user_id !== $my->id) {
            throw new ServiceException('Order (#' . $orderId . ') does not exist!');
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $image = '';
            if ($file) {
                $storageCategoryNames = '订单>客户联系>'. $tupleOrder->order_sn;
                $serviceStorage = Be::getService('App.ShopFai.Storage');
                $storageCategory = $serviceStorage->makeCategory($storageCategoryNames);
                $image = Be::getService('App.ShopFai.Storage')->uploadImage($storageCategory->id, $file);
            }

            $now = date('Y-m-d H:i:s');
            $tupleOrderContact = Be::getTuple('shopfai_order_contact');
            $tupleOrderContact->order_id = $orderId;
            $tupleOrderContact->publisher = 'customer';
            $tupleOrderContact->content = $content;
            $tupleOrderContact->image = $image;
            $tupleOrderContact->create_time = $now;
            $tupleOrderContact->update_time = $now;
            $tupleOrderContact->insert();

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException('Post order contact exception (log id: ' . $logId . ') ' );
        }
    }


}
