<?php
namespace Be\App\ShopFai\Task;

use Be\Be;
use Be\Task\Task;

/**
 * @BeTask("商品关联全量同步到缓存")
 */
class AllProductRelateSyncCache extends Task
{


    public function execute()
    {
        $service = Be::getService('App.ShopFai.Admin.TaskProductRelate');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shopfai_product_relate WHERE is_enable != -1';
        $relates = $db->getYieldObjects($sql);

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

    }

}
