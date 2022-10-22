<?php
namespace Be\App\ShopFai\Task;

use Be\App\ServiceException;
use Be\Be;
use Be\Task\TaskInterval;

/**
 * 间隔一段时间晨，定时执行 商品同步到ES和缓存
 *
 * @BeTask("商品境量同步到ES和缓存")
 */
class ProductSyncEsAndCache extends TaskInterval
{

    // 每 10 分钟执行一次
    protected $schedule = '* * * * *';

    // 默认断点
    protected $breakpoint = '2021-05-01 00:00:00';

    // 时间间隔：1天
    protected $step = 86400;

    public function execute()
    {
        $t0 = time();
        $t1 = strtotime($this->breakpoint);
        $t2 = $t1 + $this->step;

        if ($t1 >= $t0) return;
        if ($t2 > $t0) {
            $t2 = $t0;
        }

        $d1 = date('Y-m-d H:i:s', $t1 - 60);
        $d2 = date('Y-m-d H:i:s', $t2);

        $service = Be::getService('App.ShopFai.Admin.TaskProduct');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shopfai_product WHERE is_enable != -1 AND update_time >= ? AND update_time <= ?';
        $products = $db->getYieldObjects($sql, [$d1, $d2]);

        $batch = [];
        $i = 0;
        foreach ($products as $product) {
            $batch[] = $product;

            $i++;
            if ($i >= 100) {
                $service->syncEs($batch);
                $service->syncCache($batch);

                $batch = [];
                $i = 0;
            }
        }

        if ($i > 0) {
            $service->syncEs($batch);
            $service->syncCache($batch);
        }

        $this->breakpoint = $d2;
    }


}
