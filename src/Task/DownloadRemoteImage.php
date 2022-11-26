<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\TaskInterval;

/**
 * 自动下载远程图片
 *
 * @BeTask("自动下载远程图片", schedule="20 * * * *")
 */
class DownloadRemoteImage extends TaskInterval
{

    protected $parallel = false;

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

        $service = Be::getService('App.Shop.Admin.TaskProduct');
        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product WHERE update_time >= ? AND update_time <= ? AND download_remote_image = 1';
        $products = $db->getYieldObjects($sql, [$d1, $d2]);
        foreach ($products as $product) {
            $service->downloadRemoteImages($product);
        }

        $this->breakpoint = $d2;
    }


}
