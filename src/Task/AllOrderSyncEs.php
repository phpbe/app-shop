<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * 订单全量量同步到ES和Redis
 *
 * @BeTask("订单全量量同步到ES和Redis")
 */
class AllOrderSyncEs extends Task
{

    public function execute()
    {
        $service = Be::getService('App.Shop.Admin.TaskOrder');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_order';
        $orders = $db->getYieldObjects($sql);

        $batch = [];
        $i = 0;
        foreach ($orders as $order) {
            $batch[] = $order;

            $i++;
            if ($i >= 100) {
                $service->syncEs($batch);

                $batch = [];
                $i = 0;
            }
        }

        if ($i > 0) {
            $service->syncEs($batch);
        }
    }

}
