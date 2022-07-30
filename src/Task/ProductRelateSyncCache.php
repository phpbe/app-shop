<?php
namespace Be\App\ShopFai\Task;

use Be\Be;
use Be\Task\TaskInterval;

/**
 * @BeTask("商品关联增量同步到缓存")
 */
class ProductRelateSyncCache extends TaskInterval
{

    // 每 10 分钟执行一次
    protected $schedule = '* * * * *';

    // 默认断点
    protected $breakpoint = '2022-05-01 00:00:00';

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

        $service = Be::getService('App.ShopFai.Admin.TaskProductRelate');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shopfai_product_relate WHERE is_enable != -1 AND update_time >= ? AND update_time < ?';
        $relates = $db->getYieldObjects($sql, [$d1, $d2]);

        $batch = [];
        $i = 0;
        foreach ($relates as $relate) {
            $batch[] = $relate;

            $i++;
            if ($i >= 100) {
                $service->syncCache($batch);

                $batch = [];
                $i = 0;
            }
        }

        if ($i > 0) {
            $service->syncCache($batch);
        }

        $this->breakpoint = $d2;
    }



}
