<?php

namespace Be\App\ShopFai\Service\Admin;

use Be\App\ServiceException;
use Be\Be;


/**
 * 订单 计划任务
 */
class TaskOrder
{

    /**
     * 同步到 ES
     *
     * @param array $orders
     */
    public function syncEs(array $orders)
    {
        if (count($orders) === 0) return;

        $config = Be::getConfig('App.ShopFai.Es');

        $db = Be::getDb();

        $batch = [];
        foreach ($orders as $order) {

            $batch[] = [
                'index' => [
                    '_index' => $config->indexOrder,
                    '_id' => $order->id,
                ]
            ];

            $order->is_delete = (int)$order->is_delete;

            if ($order->is_delete === 1) {
                $batch[] = [
                    'id' => $order->id,
                    'is_delete' => true
                ];
            } else {

                $sql = 'SELECT * FROM shopfai_order_product WHERE order_id = ?';
                $products = $db->getObjects($sql, [$order->id]);

                $formattedProducts = [];
                foreach ($products as $product) {
                    $formattedProducts[] = [
                        'id' => $product->id,
                        'product_id' => $product->product_id,
                        'product_item_id' => $product->product_item_id,
                        'spu' => $product->spu,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'style' => $product->style,
                        'weight' => (float)$product->weight,
                        'weight_unit' => $product->weight_unit,
                        'quantity' => (int)$product->quantity,
                        'price' => (float)$product->price,
                        'amount' => (float)$product->amount,
                    ];
                }

                $batch[] = [
                    'id' => $order->id,
                    'order_sn' => $order->order_sn,
                    'user_id' => $order->user_id,
                    'user_token' => $order->user_token,
                    'email' => $order->email,
                    'product_amount' => (float)$order->product_amount,
                    'discount_amount' => (float)$order->discount_amount,
                    'shipping_fee' => (float)$order->shipping_fee,
                    'amount' => (float)$order->amount,
                    'shipping_plan_id' => $order->shipping_plan_id,
                    'payment_id' => $order->payment_id,
                    'payment_item_id' => $order->payment_item_id,
                    'is_cod' => (int)$order->is_cod,
                    'is_paid' => (int)$order->is_paid,
                    'is_shipped' => (int)$order->is_shipped,
                    'status' => $order->status,
                    'pay_expire_time' => $order->pay_expire_time,
                    'pay_time' => $order->pay_time,
                    'cancel_time' => $order->cancel_time,
                    'ship_time' => $order->ship_time,
                    'receive_time' => $order->receive_time,
                    'is_delete' => (int)$order->is_delete,
                    'create_time' => $order->create_time,
                    'update_time' => $order->update_time,
                    'products' => $formattedProducts,
                ];
            }
        }

        if (count($batch) > 0) {
            $es = Be::getEs();
            $response = $es->bulk(['body' => $batch]);
            if ($response['errors'] > 0) {
                $reason = '';
                if (isset($response['items']) && count($response['items']) > 0) {
                    foreach ($response['items'] as $item) {
                        if (isset($item['index']['error']['reason'])) {
                            $reason = $item['index']['error']['reason'];
                            break;
                        }
                    }
                }
                throw new ServiceException('订单同步到ES出错：' . $reason);
            }
        }
    }

}
