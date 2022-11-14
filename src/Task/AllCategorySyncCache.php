<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * @BeTask("分类全量同步到缓存")
 */
class AllCategorySyncCache extends Task
{


    public function execute()
    {
        $service = Be::getService('App.Shop.Admin.TaskCategory');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_category';
        $categories = $db->getObjects($sql);

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

    }

}
