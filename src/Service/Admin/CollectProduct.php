<?php

namespace Be\App\Shop\Service\Admin;

use Be\App\ServiceException;
use Be\Be;

class CollectProduct
{

    /**
     * 导入
     *
     * @param array $products 要导入商品数据
     */
    public function import(array $products)
    {
        if (count($products) === 0) return;

        $db = Be::getDb();

        $now = date('Y-m-d H:i:s');
        foreach ($products as $product) {

            $tupleProduct = Be::getTuple('shop_product');
            try {
                $tupleProduct->loadBy([
                    'id' => $product['id'],
                ]);
            } catch (\Throwable $t) {
                throw new ServiceException('采集的商品（# ' . $product['id'] . '）不存在！');
            }

            if ($tupleProduct->is_enable !== -1) {
                continue;
            }

            $db->startTransaction();
            try {

                if (isset($product['category_ids']) && is_array($product['category_ids'])) {
                    $existCategoryIds = Be::getTable('shop_product_category')
                        ->where('product_id', $product['id'])
                        ->getValues('category_id');

                    // 需要删除的分类
                    if (count($existCategoryIds) > 0) {
                        $removeCategoryIds = array_diff($existCategoryIds, $product['category_ids']);
                        if (count($removeCategoryIds) > 0) {
                            Be::getTable('shop_product_category')
                                ->where('product_id', $product['id'])
                                ->where('category_id', 'NOT IN', $removeCategoryIds)
                                ->delete();
                        }
                    }

                    // 新增的分类
                    $newCategoryIds = null;
                    if (count($existCategoryIds) > 0) {
                        $newCategoryIds = array_diff($product['category_ids'], $existCategoryIds);
                    } else {
                        $newCategoryIds = $product['category_ids'];
                    }
                    if (count($newCategoryIds) > 0) {
                        foreach ($newCategoryIds as $category_id) {
                            $tupleProductCategory = Be::getTuple('shop_product_category');
                            $tupleProductCategory->product_id = $tupleProduct->id;
                            $tupleProductCategory->category_id = $category_id;
                            $tupleProductCategory->insert();
                        }
                    }
                }

                $tupleProduct->is_enable = 0;
                $tupleProduct->download_remote = 1;
                $tupleProduct->update_time = $now;
                $tupleProduct->update();

                if ($tupleProduct->relate_id !== '') {
                    $tupleProductRelate = Be::getTuple('shop_product_relate');
                    $tupleProductRelate->load($tupleProduct->relate_id);
                    if ($tupleProductRelate->is_enable === -1) {
                        $tupleProductRelate->is_enable = 1;
                        $tupleProductRelate->update_time = $now;
                        $tupleProductRelate->update();
                    }
                }

                $db->commit();

            } catch (\Throwable $t) {
                $db->rollback();
                Be::getLog()->error($t);

                throw new ServiceException('导入采集的商品发生异常！');
            }
        }
    }

    /**
     * 删除
     *
     * @param array $productIds 要删除的商品ID
     */
    public function delete(array $productIds)
    {
        if (count($productIds) === 0) return;

        foreach ($productIds as $productId) {
            $tupleProduct = Be::getTuple('shop_product');
            try {
                $tupleProduct->loadBy([
                    'id' => $productId,
                ]);
            } catch (\Throwable $t) {
                throw new ServiceException('采集的商品（# ' . $productId . '）不存在！');
            }

            if ($tupleProduct->is_enable === -1) { // 未曾导入，直接物理删除

                // 删除商品主图
                Be::getTable('shop_product_image')
                    ->where('product_id', $productId)
                    ->delete();

                // 删除商品分类
                Be::getTable('shop_product_category')
                    ->where('product_id', $productId)
                    ->delete();

                // 如查商品有设置关联，删除商品关联
                if ($tupleProduct->relate_id !== '') {
                    Be::getTable('shop_product_relate_item')
                        ->where('relate_id', $tupleProduct->relate_id)
                        ->where('product_id', $productId)
                        ->delete();

                    if (Be::getTable('shop_product_relate_item')
                            ->where('relate_id', $tupleProduct->relate_id)
                            ->count() === 0) {

                        Be::getTuple('shop_product_relate')
                            ->delete($tupleProduct->relate_id);
                    }
                }

                // 删除商品SKU
                Be::getTable('shop_product_item')
                    ->where('product_id', $productId)
                    ->delete();

                // 删除商品款式
                Be::getTable('shop_product_style')
                    ->where('product_id', $productId)
                    ->delete();

                // 删除商品款式
                Be::getTable('shop_product_tag')
                    ->where('product_id', $productId)
                    ->delete();

                if ($tupleProduct->collect_product_id !== '') {
                    Be::getTuple('shop_collect_product')
                        ->delete($tupleProduct->collect_product_id);
                }

                // 最后删除商品主表
                $tupleProduct->delete();

            } else {

                if ($tupleProduct->collect_product_id !== '') {
                    Be::getTuple('shop_collect_product')
                        ->delete($tupleProduct->collect_product_id);
                }

                $tupleProduct->collect_product_id = '';
                $tupleProduct->update_time = date('Y-m-d H:i:s');
                $tupleProduct->update();
            }
        }
    }
}
