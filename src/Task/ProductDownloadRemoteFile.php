<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * 下载商品中引用的远程文件
 *
 * @BeTask("下载商品中引用的远程文件"，schedule="0 * * * *")
 */
class ProductDownloadRemoteFile extends Task
{

    public function execute()
    {
        $service = Be::getService('App.Shop.Admin.TaskProduct');

        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product WHERE download_remote = 1 AND is_enable != -1 AND is_delete = 0 LIMIT 300';
        $products = $db->getObjects($sql);
        foreach ($products as $product) {
            try {
                $service->downloadRemoteFile($product);
            } catch (\Throwable $t) {
            }

            // 采完一个商品休眼10秒
            if (Be::getRuntime()->isSwooleMode()) {
                \Swoole\Coroutine::sleep(10);
            }
        }
    }

}
