<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * 商品全量量同步到ES和缓存
 *
 * @BeTask("商品全量量同步到ES和缓存")
 */
class AllProductSyncEsAndCache extends Task
{

    public function execute()
    {
        $service = Be::getService('App.Shop.Admin.TaskProduct');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product WHERE is_enable != -1';
        $products = $db->getYieldObjects($sql);

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
    }

}
