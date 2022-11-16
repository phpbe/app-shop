<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\TaskInterval;

/**
 * @BeTask("分类增量同步到缓存")
 */
class CategorySyncCache extends TaskInterval
{

    // 每 10 分钟执行一次
    protected $schedule = '* * * * *';

    // 时间间隔：1天
    protected $step = 86400;

    public function execute()
    {
        if (!$this->breakpoint) {
            $this->breakpoint = date('Y-m-d h:i:s', time() - $this->step);
        }

        $t0 = time();
        $t1 = strtotime($this->breakpoint);
        $t2 = $t1 + $this->step;

        if ($t1 >= $t0) return;
        if ($t2 > $t0) {
            $t2 = $t0;
        }

        $d1 = date('Y-m-d H:i:s', $t1 - 60);
        $d2 = date('Y-m-d H:i:s', $t2);

        $service = Be::getService('App.Shop.Admin.TaskCategory');
        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_category WHERE update_time >= ? AND update_time <= ?';
        $categories = $db->getObjects($sql, [$d1, $d2]);

        if (count($categories) === 0) return;

        $batch = [];
        $i = 0;
        foreach ($categories as $category) {
            $batch[] = $category;

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
