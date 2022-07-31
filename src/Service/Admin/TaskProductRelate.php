<?php

namespace Be\App\ShopFai\Service\Admin;

use Be\Be;


/**
 * 商品关联 计划任务
 */
class TaskProductRelate
{

    /**
     * 同步到 Redis
     *
     * @param array $relates
     */
    public function syncCache(array $relates)
    {
        if (count($relates) === 0) return;

        $db = Be::getDb();
        $cache = Be::getCache();

        $keyValues = [];
        foreach ($relates as $relate) {
            $relate->is_enable = (int)$relate->is_enable;

            // 采集的商品，不处理
            if ($relate->is_enable === -1) {
                continue;
            }

            $key = 'ShopFai:ProductRelate:' . $relate->id;

            $relate->is_delete = (int)$relate->is_delete;

            if ($relate->is_delete === 1) {
                $cache->delete($key);
            } else {
                $sql = 'SELECT * FROM shopfai_product_relate_detail WHERE relate_id = ? ORDER BY ordering ASC';
                $relate->details = $db->getObjects($sql, [$relate->id]);
                $keyValues[$key] = $relate;
            }
        }

        if (count($keyValues) > 0) {
            $cache->setMany($keyValues);
        }
    }

}
