<?php

namespace Be\App\Shop\Service\Admin;

class Order
{

    public function getStatusKeyValues(): array
    {
        return [
            'pending' => '待付款',
            'paid' => '待发货',
            'shipped' => '待收货',
            'received'  => '已收货',
            'cancelled' => '已取消',
            'expired' => '付款超时',
        ];
    }

}
