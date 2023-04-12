<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * 自动下载远程图片
 *
 * @BeTask("自动下载远程图片", timeout="1800", schedule="50 * * * *")
 */
class DownloadRemoteImage extends Task
{

    protected $parallel = false;


    public function execute()
    {
        $timeout = $this->timeout;
        if ($timeout <= 0) {
            $timeout = 60;
        }

        $service = Be::getService('App.Shop.Admin.TaskProduct');
        $t0 = time();
        do {
            $sql = 'SELECT * FROM shop_product WHERE download_remote_image = 1';
            $product = Be::getDb()->getObject($sql);
            if (!$product) {
                break;
            }

            $service->downloadRemoteImages($product);

            $this->taskLog->update_time = date('Y-m-d H:i:s');
            $this->updateTaskLog();

            $t1 = time();
        } while($t1 - $t0 < $timeout );
    }


}
