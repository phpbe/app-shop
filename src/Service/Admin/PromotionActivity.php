<?php

namespace Be\App\Shop\Service\Admin;

use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\App\ServiceException;
use Be\Be;
use Be\Db\Tuple;
use Be\Util\Crypt\Random;

class PromotionActivity
{

    /**
     * 获取满减活动
     *
     * @param string $promotionActivityId
     * @return object
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getPromotionActivity(string $promotionActivityId): object
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM shop_promotion_activity WHERE id=?';
        $promotionActivity = $db->getObject($sql, [$promotionActivityId]);

        $promotionActivity->poster = (int)$promotionActivity->poster;
        $promotionActivity->seo = (int)$promotionActivity->seo;
        $promotionActivity->never_expire = (int)$promotionActivity->never_expire;
        $promotionActivity->is_enable = (int)$promotionActivity->is_enable;
        $promotionActivity->is_delete = (int)$promotionActivity->is_delete;

        $promotionActivity->scope_products = [];
        $promotionActivity->scope_categories = [];
        if ($promotionActivity->scope_product === 'assign') {
            $productIds = Be::getTable('shop_promotion_activity_scope_product')
                ->where('promotion_activity_id', $promotionActivityId)
                ->getValues('product_id');
            if (count($productIds) > 0) {
                $products = Be::getTable('shop_product')
                    ->where('id', 'IN', $productIds)
                    ->getObjects();
                if (count($products) > 0) {
                    foreach ($products as &$product) {
                        $sql = 'SELECT url FROM shop_product_image WHERE product_id = ? AND product_item_id = \'\' AND is_main = 1';
                        $image = $db->getValue($sql, [$product->id]);
                        if ($image) {
                            $product->image = $image;
                        } else {
                            $product->image = Be::getProperty('App.Shop')->getWwwUrl() . '/image/product/no-image.webp';
                        }
                    }
                    unset($product);

                    $promotionActivity->scope_products = $products;
                }
            }
        } elseif ($promotionActivity->scope_product === 'category') {
            $categoryIds = Be::getTable('shop_promotion_activity_scope_category')
                ->where('promotion_activity_id', $promotionActivityId)
                ->getValues('category_id');
            if (count($categoryIds) > 0) {
                $categories = Be::getTable('shop_category')
                    ->where('id', 'IN', $categoryIds)
                    ->getObjects();
                foreach ($categories as &$category) {
                    if (!$category->image) {
                        $category->image = Be::getProperty('App.Shop')->getWwwUrl() . '/image/category/no-image.webp';
                    }
                }
                unset($category);

                $promotionActivity->scope_categories = $categories;
            }
        }

        $promotionActivity->discounts = Be::getTable('shop_promotion_activity_discount')
            ->where('promotion_activity_id', $promotionActivityId)
            ->orderBy('ordering', 'ASC')
            ->getObjects();

        return $promotionActivity;
    }

    /**
     * 编辑满减活动
     *
     * @param array $data 满减活动数据
     * @return Tuple
     * @throws \Throwable
     */
    public function edit($data)
    {
        $db = Be::getDb();

        $isNew = true;
        $promotionActivityId = null;
        if (isset($data['id']) && is_string($data['id']) && $data['id'] !== '') {
            $isNew = false;
            $promotionActivityId = $data['id'];
        }

        $tuplePromotionActivity = Be::getTuple('shop_promotion_activity');
        if (!$isNew) {
            try {
                $tuplePromotionActivity->load($promotionActivityId);
            } catch (\Throwable $t) {
                throw new ServiceException('满减活动（# ' . $promotionActivityId . '）不存在！');
            }
        }

        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new ServiceException('名称未填写！');
        }


        if (!isset($data['poster']) || !is_numeric($data['poster'])) {
            throw new ServiceException('是否展示活动页海报参数无效！');
        }

        $data['poster'] = (int)$data['poster'];
        if (!in_array($data['poster'], [0, 1])) {
            throw new ServiceException('是否展示活动页海报参数无效！');
        }

        $data['poster_desktop'] = '';
        $data['poster_mobile'] = '';
        if ($data['poster'] === 1) {
            if (!isset($data['poster_desktop']) || !is_string($data['poster_desktop']) || strlen($data['poster_desktop']) > 200) {
                $data['poster_desktop'] = '';
            }

            if (!isset($data['poster_mobile']) || !is_string($data['poster_mobile']) || strlen($data['poster_mobile']) > 200) {
                $data['poster_desktop'] = '';
            }
        }

        if (!isset($data['discount_type']) || !is_string($data['discount_type']) || !in_array($data['discount_type'], ['percent', 'amount'])) {
            throw new ServiceException('优惠类型无效！');
        }

        if (!isset($data['condition']) || !is_string($data['condition']) || !in_array($data['condition'], ['min_amount', 'min_quantity'])) {
            throw new ServiceException('优惠条件无效！');
        }

        if (!isset($data['discounts']) || !is_array($data['discounts']) || count($data['discounts']) === 0) {
            throw new ServiceException('梯度优惠数据无效！');
        }

        $i = 1;
        foreach ($data['discounts'] as &$discount) {

            if ($data['condition'] === 'min_amount') {
                if (!isset($discount['min_amount']) || !is_numeric($discount['min_amount'])) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 购买金额无效！');
                }

                $discount['min_amount'] = number_format($discount['min_amount'], 2, '.', '');

                if (bccomp($discount['min_amount'], '0.01') === -1) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 购买金额无效！');
                }

                $discount['min_quantity'] = 0;
            } else {
                if (!isset($discount['min_quantity']) || !is_numeric($discount['min_quantity'])) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 购买数量无效！');
                }

                $discount['min_quantity'] = (int)$discount['min_quantity'];

                if ($discount['min_quantity'] <= 0) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 购买数量无效！');
                }

                $discount['min_amount'] = '0.00';
            }

            if ($data['discount_type'] === 'percent') {
                if (!isset($discount['discount_percent']) || !is_numeric($discount['discount_percent'])) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 减免折扣无效！');
                }

                $discount['discount_percent'] = (int)$discount['discount_percent'];

                if ($discount['discount_percent'] <= 0 || $discount['discount_percent'] >= 100) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 减免折扣无效！');
                }

                $discount['discount_amount'] = '0.00';
            } else {
                if (!isset($discount['discount_amount']) || !is_numeric($discount['discount_amount'])) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 减免金额无效！');
                }

                $discount['discount_amount'] = number_format($discount['discount_amount'], 2, '.', '');

                if (bccomp($discount['discount_amount'], '0.01') === -1) {
                    throw new ServiceException('梯度优惠第'.$i.'项 - 减免金额无效！');
                }

                $discount['discount_percent'] = 0;
            }

            $i++;
        }
        unset($discount);

        if (!isset($data['scope_product']) || !is_string($data['scope_product']) || !in_array($data['scope_product'], ['all', 'assign', 'category'])) {
            throw new ServiceException('适用商品无效！');
        }

        if ($data['scope_product'] === 'assign') {
            if (!isset($data['scope_products']) || !is_array($data['scope_products']) || count($data['scope_products']) === 0) {
                throw new ServiceException('适用商品 - 未指定商品！');
            }

            foreach ($data['scope_products'] as $product) {
                if (!isset($product['id']) || !is_string($product['id'])) {
                    throw new ServiceException('适用商品 - 指定的商品无效！');
                }
            }
        } elseif ($data['scope_product'] === 'category') {
            if (!isset($data['scope_categories']) || !is_array($data['scope_categories']) || count($data['scope_categories']) === 0) {
                throw new ServiceException('适用商品 - 未指定分类！');
            }

            foreach ($data['scope_categories'] as $category) {
                if (!isset($category['id']) || !is_string($category['id'])) {
                    throw new ServiceException('适用商品 - 指定的分类无效！');
                }
            }
        }

        if (!isset($data['start_time']) || !is_string($data['start_time']) || !strtotime($data['start_time'])) {
            throw new ServiceException('活动开始时间无效！');
        }

        if (!isset($data['never_expire']) || !is_numeric($data['never_expire'])) {
            throw new ServiceException('是否永不过期无效！');
        }

        $data['never_expire'] = (int)$data['never_expire'];
        if (!in_array($data['never_expire'], [0, 1])) {
            throw new ServiceException('是否永不过期无效！');
        }

        if ($data['never_expire'] === 0) {
            if (!isset($data['end_time']) || !is_string($data['end_time']) || !strtotime($data['end_time'])) {
                throw new ServiceException('活动结束时间无效！');
            }
        }

        $url = null;
        if (!isset($data['url']) || !is_string($data['url'])) {
            $url = strtolower($data['name']);
            $url = preg_replace('/[^a-z0-9]/', '-', $url);
            $url = str_replace(' ', '-', $url);
            while (strpos($url, '--') !== false) {
                $url = str_replace('--', '-', $url);
            }
        } else {
            $url = $data['url'];
        }
        $urlUnique = $url;
        $urlIndex = 0;
        $urlExist = null;
        do {
            if ($isNew) {
                $urlExist = Be::getTable('shop_promotion_activity')
                        ->where('url', $urlUnique)
                        ->getValue('COUNT(*)') > 0;
            } else {
                $urlExist = Be::getTable('shop_promotion_activity')
                        ->where('url', $urlUnique)
                        ->where('id', '!=', $promotionActivityId)
                        ->getValue('COUNT(*)') > 0;
            }

            if ($urlExist) {
                $urlIndex++;
                $urlUnique = $url . '-' . $urlIndex;
            }
        } while ($urlExist);

        $data['url'] = $urlUnique;


        if (!isset($data['seo']) || !is_numeric($data['seo'])) {
            throw new ServiceException('是否单独编辑SEO无效！');
        }

        $data['seo'] = (int)$data['seo'];
        if (!in_array($data['seo'], [0, 1])) {
            throw new ServiceException('是否单独编辑SEO无效！');
        }

        if (!isset($data['seo_title']) || !is_string($data['seo_title'])) {
            $data['seo_title'] = $data['name'];
        }

        if (!isset($data['seo_description']) || !is_string($data['seo_description'])) {
            $data['seo_description'] = '';
        }

        if (!isset($data['seo_keywords']) || !is_string($data['seo_keywords'])) {
            $data['seo_keywords'] = '';
        }

        if (!isset($data['discount_text']) || !is_string($data['discount_text'])) {
            $data['discount_text'] = '';
        }

        $serviceStore = Be::getService('App.Shop.Admin.Store');

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tuplePromotionActivity->name = $data['name'];
            $tuplePromotionActivity->url = $data['url'];
            $tuplePromotionActivity->seo = $data['seo'];
            $tuplePromotionActivity->seo_title = $data['seo_title'];
            $tuplePromotionActivity->seo_description = $data['seo_description'];
            $tuplePromotionActivity->seo_keywords = $data['seo_keywords'];
            $tuplePromotionActivity->poster = $data['poster'];
            $tuplePromotionActivity->poster_desktop = $data['poster_desktop'];
            $tuplePromotionActivity->poster_mobile = $data['poster_mobile'];
            $tuplePromotionActivity->discount_type = $data['discount_type'];
            $tuplePromotionActivity->condition = $data['condition'];

            $tuplePromotionActivity->scope_product = $data['scope_product'];

            $tuplePromotionActivity->start_time = $serviceStore->storeTime2SystemTime($data['start_time']);
            $tuplePromotionActivity->never_expire = $data['never_expire'];

            if ($data['never_expire'] === 0) {
                $tuplePromotionActivity->end_time = $serviceStore->storeTime2SystemTime($data['end_time']);;
            } else {
                $tuplePromotionActivity->end_time = '2038-01-01 00:00:00';
            }

            $tuplePromotionActivity->discount_text = $data['discount_text'];

            $tuplePromotionActivity->update_time = $now;
            if ($isNew) {
                $tuplePromotionActivity->create_time = $now;
                $tuplePromotionActivity->insert();
            } else {
                $changeDetails = $tuplePromotionActivity->getChangeDetails();
                $tuplePromotionActivity->update();

                if (count($changeDetails) > 1) {
                    $tuplePromotionActivityChange = Be::getTuple('shop_promotion_activity_change');
                    $tuplePromotionActivityChange->promotion_activity_id = $tuplePromotionActivity->id;
                    $tuplePromotionActivityChange->details = json_encode($changeDetails);
                    $tuplePromotionActivityChange->create_time = $now;
                    $tuplePromotionActivityChange->insert();
                }
            }

            if ($isNew) {
                $ordering = 0;
                foreach ($data['discounts'] as $discount) {
                    $tuplePromotionActivityDiscount = Be::getTuple('shop_promotion_activity_discount');
                    $tuplePromotionActivityDiscount->promotion_activity_id = $tuplePromotionActivity->id;
                    $tuplePromotionActivityDiscount->min_amount = $discount['min_amount'];
                    $tuplePromotionActivityDiscount->min_quantity = $discount['min_quantity'];
                    $tuplePromotionActivityDiscount->discount_percent = $discount['discount_percent'];
                    $tuplePromotionActivityDiscount->discount_amount = $discount['discount_amount'];
                    $tuplePromotionActivityDiscount->ordering = $ordering++;
                    $tuplePromotionActivityDiscount->insert();
                }
            } else {
                $keepIds = [];
                foreach ($data['discounts'] as $discount) {
                    if (isset($discount['id']) && $discount['id'] !== '') {
                        $keepIds[] = $discount['id'];
                    }
                }

                if (count($keepIds) > 0) {
                    Be::getTable('shop_promotion_activity_discount')
                        ->where('promotion_activity_id', $tuplePromotionActivity->id)
                        ->where('id', 'NOT IN', $keepIds)
                        ->delete();
                } else {
                    Be::getTable('shop_promotion_activity_discount')
                        ->where('promotion_activity_id', $tuplePromotionActivity->id)
                        ->delete();
                }

                $ordering = 0;
                foreach ($data['discounts'] as $discount) {
                    $tuplePromotionActivityDiscount = Be::getTuple('shop_promotion_activity_discount');
                    if (isset($discount['id']) && $discount['id'] !== '') {
                        try {
                            $tuplePromotionActivityDiscount->loadBy([
                                'id' => $discount['id'],
                                'promotion_activity_id' => $tuplePromotionActivity->id,
                            ]);
                        } catch (\Throwable $t) {
                            throw new ServiceException('满减活动（# ' . $tuplePromotionActivity->id . ' ' . $tuplePromotionActivity->name . '）下的梯度优惠数据（# ' . $discount['id'] . '）不存在！');
                        }
                    }

                    $tuplePromotionActivityDiscount->promotion_activity_id = $tuplePromotionActivity->id;
                    $tuplePromotionActivityDiscount->min_amount = $discount['min_amount'];
                    $tuplePromotionActivityDiscount->min_quantity = $discount['min_quantity'];
                    $tuplePromotionActivityDiscount->discount_percent = $discount['discount_percent'];
                    $tuplePromotionActivityDiscount->discount_amount = $discount['discount_amount'];
                    $tuplePromotionActivityDiscount->ordering = $ordering++;
                    $tuplePromotionActivityDiscount->save();
                }
            }

            if ($data['scope_product'] === 'assign') {
                if ($isNew) {
                    foreach ($data['scope_products'] as $product) {
                        $tuplePromotionActivityScopeProduct = Be::getTuple('shop_promotion_activity_scope_product');
                        $tuplePromotionActivityScopeProduct->promotion_activity_id = $tuplePromotionActivity->id;
                        $tuplePromotionActivityScopeProduct->product_id = $product['id'];
                        $tuplePromotionActivityScopeProduct->insert();
                    }
                } else {
                    $productIds = [];
                    foreach ($data['scope_products'] as $product) {
                        $productIds[] = $product['id'];
                    }

                    $existProductIds = Be::getTable('shop_promotion_activity_scope_product')
                        ->where('promotion_activity_id', $tuplePromotionActivity->id)
                        ->getValues('product_id');

                    // 需要删除的
                    if (count($existProductIds) > 0) {
                        $removeProductIds = array_diff($existProductIds, $productIds);
                        if (count($removeProductIds) > 0) {
                            Be::getTable('shop_promotion_activity_scope_product')
                                ->where('promotion_activity_id', $tuplePromotionActivity->id)
                                ->where('product_id', 'IN', $removeProductIds)
                                ->delete();
                        }
                    }

                    // 新增的
                    $newProductIds = null;
                    if (count($existProductIds) > 0) {
                        $newProductIds = array_diff($productIds, $existProductIds);
                    } else {
                        $newProductIds = $productIds;
                    }
                    if (count($newProductIds) > 0) {
                        foreach ($newProductIds as $newProductId) {
                            $tuplePromotionActivityScopeProduct = Be::getTuple('shop_promotion_activity_scope_product');
                            $tuplePromotionActivityScopeProduct->promotion_activity_id = $tuplePromotionActivity->id;
                            $tuplePromotionActivityScopeProduct->product_id = $newProductId;
                            $tuplePromotionActivityScopeProduct->insert();
                        }
                    }
                }
            } elseif ($data['scope_product'] === 'category') {
                if ($isNew) {
                    foreach ($data['scope_categories'] as $category) {
                        $tuplePromotionActivityScopeCategory = Be::getTuple('shop_promotion_activity_scope_category');
                        $tuplePromotionActivityScopeCategory->promotion_activity_id = $tuplePromotionActivity->id;
                        $tuplePromotionActivityScopeCategory->category_id = $category['id'];
                        $tuplePromotionActivityScopeCategory->insert();
                    }
                } else {
                    $categoryIds = [];
                    foreach ($data['scope_categories'] as $category) {
                        $categoryIds[] = $category['id'];
                    }

                    $existCategoryIds = Be::getTable('shop_promotion_activity_scope_category')
                        ->where('promotion_activity_id', $tuplePromotionActivity->id)
                        ->getValues('category_id');

                    // 需要删除的
                    if (count($existCategoryIds) > 0) {
                        $removeCategoryIds = array_diff($existCategoryIds, $categoryIds);
                        if (count($removeCategoryIds) > 0) {
                            Be::getTable('shop_promotion_activity_scope_category')
                                ->where('promotion_activity_id', $tuplePromotionActivity->id)
                                ->where('category_id', 'IN', $removeCategoryIds)
                                ->delete();
                        }
                    }

                    // 新增的
                    $newCategoryIds = null;
                    if (count($existCategoryIds) > 0) {
                        $newCategoryIds = array_diff($categoryIds, $existCategoryIds);
                    } else {
                        $newCategoryIds = $categoryIds;
                    }
                    if (count($newCategoryIds) > 0) {
                        foreach ($newCategoryIds as $newCategoryId) {
                            $tuplePromotionActivityScopeCategory = Be::getTuple('shop_promotion_activity_scope_category');
                            $tuplePromotionActivityScopeCategory->promotion_activity_id = $tuplePromotionActivity->id;
                            $tuplePromotionActivityScopeCategory->category_id = $newCategoryId;
                            $tuplePromotionActivityScopeCategory->insert();
                        }
                    }
                }
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException(($isNew ? '新建' : '编辑') . '满减活动发生异常！');
        }

        return $tuplePromotionActivity;
    }

    /**
     * 删除满减活动
     *
     * @param array $promotionActivityIds
     * @return void
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function delete(array $promotionActivityIds)
    {
        if (count($promotionActivityIds) === 0) return;

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            foreach ($promotionActivityIds as $promotionActivityId) {

                $tuplePromotionActivity = Be::getTuple('shop_promotion_activity');
                try {
                    $tuplePromotionActivity->load($promotionActivityId);
                } catch (\Throwable $t) {
                    throw new ServiceException('满减活动（# ' . $promotionActivityId . '）不存在！');
                }

                $tuplePromotionActivity->is_delete = 1;
                $tuplePromotionActivity->update_time = $now;
                $tuplePromotionActivity->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('删除满减活动发生异常！');
        }
    }

    /**
     * 生成一个不重复的优惠码
     * @param int $len 长度
     * @return string
     */
    public function generate(int $len = 8): string
    {
        $code = null;
        $exist = null;
        do {
            $code = Random::create($len, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
            $exist = Be::getTable('shop_promotion_activity')
                    ->where('code', $code)
                    ->getValue('COUNT(*)') > 0;
        } while ($exist);

        return $code;
    }


    /**
     * 获取满减活动修改记录
     *
     * @param string $promotionActivityId
     * @return array
     */
    public function getChanges(string $promotionActivityId): array
    {
        $discountTypeKeyValues = ['percent' => '百分比折扣', 'amount' => '固定金额'];
        $conditionKeyValues = ['min_amount' => '需消费指定金额', 'min_quantity' => '需购买指定数量'];
        $scopeProductKeyValues = ['all' => '所有商品', 'assign' => '指定商品', 'category' => '指定分类'];

        $changes = Be::getTable('shop_promotion_activity_change')
            ->where('promotion_activity_id', $promotionActivityId)
            ->orderBy('create_time', 'DESC')
            ->limit(30)
            ->getObjects();

        foreach ($changes as &$change) {
            $details = json_decode($change->details, true);

            $items = [];
            foreach ($details as $key => $val) {
                switch ($key) {
                    case 'name':
                        $items[] = '名称 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'poster':
                        $items[] = '展示活动页海报 从 ' . ($val['from'] ? '是' : '否') . ' 改为 ' . ($val['to'] ? '是' : '否');
                        break;
                    case 'poster_desktop':
                        $items[] = '海报 - 电脑端 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'poster_mobile':
                        $items[] = '海报 - 移动端 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'discount_type':
                        $items[] = '优惠类型 从 ' . ($discountTypeKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($discountTypeKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'condition':
                        $items[] = '优惠条件 从 ' . ($conditionKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($conditionKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'scope_product':
                        $items[] = '适用商品 从 ' . ($scopeProductKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($scopeProductKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'start_time':
                        $items[] = '活动开始时间 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'end_time':
                        if (substr($val['from'], 0, 4) === '2038') {
                            $items[] = '活动结束时间 设置为 ' . $val['to'];
                        } else {
                            if (substr($val['to'], 0, 4) === '2038') {
                                $items[] = '活动结束时间 从 ' . $val['from'] . ' 改为 无';
                            } else {
                                $items[] = '活动结束时间 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                            }
                        }
                        break;
                    case 'never_expire':
                        $items[] = '永不过期 从 ' . ($val['from'] ? '是' : '否') . ' 改为 ' . ($val['to'] ? '是' : '否');
                        break;
                    case 'discount_text':
                        $items[] = ' 优惠文案 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'seo':
                        $items[] = '单独编辑SEO 从 ' . ($val['from'] ? '是' : '否') . ' 改为 ' . ($val['to'] ? '是' : '否');
                        break;
                    case 'seo_title':
                        $items[] = 'SEO标题 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'seo_description':
                        $items[] = 'SEO描述 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'url':
                        $items[] = 'SEO友好链接 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'seo_keywords':
                        $items[] = 'SEO关键词 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                }
            }

            $change->details = $items;
        }

        return $changes;
    }

    /**
     * 获取满减活动统计
     *
     * @param string $promotionActivityId
     * @return array
     */
    public function getStatisticsSummary(string $promotionActivityId): array
    {
        $orderIds = Be::getTable('shop_order_promotion')
            ->where('promotion_type', 'promotion_activity')
            ->where('promotion_id', $promotionActivityId)
            ->getValues('order_id');

        $orderCount = count($orderIds);

        if ($orderCount > 0) {
            $discountAmount = Be::getTable('shop_order_promotion')
                ->where('promotion_type', 'promotion_activity')
                ->where('promotion_id', $promotionActivityId)
                ->sum('discount_amount');
            if ($discountAmount === null) {
                $discountAmount = 0;
            }

            $orderAmount = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->sum('amount');
            if ($orderAmount === null) {
                $orderAmount = 0;
            }
        } else {
            $discountAmount = 0;
            $orderAmount = 0;
        }

        $statistics = [];
        $statistics['orderCount'] = $orderCount;
        $statistics['discountAmount'] = $discountAmount;
        $statistics['orderAmount'] = $orderAmount;

        return $statistics;
    }

    /**
     * 获取满减活动统计
     *
     * @param string $promotionActivityId
     * @return array
     */
    public function getStatistics(string $promotionActivityId): array
    {
        $orderIds = Be::getTable('shop_order_promotion')
            ->where('promotion_type', 'promotion_activity')
            ->where('promotion_id', $promotionActivityId)
            ->getValues('order_id');

        if (count($orderIds) > 0) {
            $paidOrderCount = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->where('paid', '1')
                ->count();

            $paidOrderTotalAmount = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->where('paid', '1')
                ->sum('amount');
            if ($paidOrderTotalAmount === null) {
                $paidOrderTotalAmount = 0;
            }

            if ($paidOrderCount > 0) {
                $paidOrderAvgAmount = bcdiv($paidOrderTotalAmount, $paidOrderCount, 2);
            } else {
                $paidOrderAvgAmount = 0;
            }

            $paidOrderIds = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->where('paid', '1')
                ->getValues('id');

            $paidOrderDiscountAmount = Be::getTable('shop_order_promotion')
                ->where('promotion_type', 'promotion_activity')
                ->where('promotion_id', $promotionActivityId)
                ->where('order_id', 'IN', $paidOrderIds)
                ->sum('discount_amount');

            if ($paidOrderDiscountAmount === null) {
                $paidOrderDiscountAmount = 0;
            }

            $unpaidOrderCount = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->where('paid', '0')
                ->count();

            $unpaidOrderTotalAmount = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->where('paid', '0')
                ->sum('amount');
            if ($unpaidOrderTotalAmount === null) {
                $unpaidOrderTotalAmount = 0;
            }

            $paidOrders = Be::getTable('shop_order')
                ->where('id', 'IN', $orderIds)
                ->where('paid', '1')
                ->limit(100)
                ->getObjects('id, order_sn, email, order_sn, amount, pay_time, create_time');

        } else {
            $paidOrderCount = 0;
            $paidOrderTotalAmount = 0;
            $paidOrderAvgAmount = 0;
            $paidOrderDiscountAmount = 0;

            $unpaidOrderCount = 0;
            $unpaidOrderTotalAmount = 0;

            $paidOrders = [];
        }

        $statistics = [];
        $statistics['paidOrderCount'] = $paidOrderCount;
        $statistics['paidOrderTotalAmount'] = $paidOrderTotalAmount;
        $statistics['paidOrderAvgAmount'] = $paidOrderAvgAmount;
        $statistics['paidOrderDiscountAmount'] = $paidOrderDiscountAmount;

        $statistics['unpaidOrderCount'] = $unpaidOrderCount;
        $statistics['unpaidOrderTotalAmount'] = $unpaidOrderTotalAmount;

        $statistics['paidOrders'] = $paidOrders;

        return $statistics;
    }


    /**
     * 获取菜单参数选择器
     *
     * @return array
     */
    public function getPromotionActivityMenuPicker(): array
    {
        $serviceStore = Be::getService('App.Shop.Store');
        $now = $serviceStore->systemTime2StoreTime(date('Y-m-d H:i:s'));

        return [
            'name' => 'id',
            'value' => '满减活动：{name}',
            'table' => 'shop_promotion_activity',
            'grid' => [
                'title' => '选择一个满减活动',

                'filter' => [
                    ['start_time', '<', $now],
                    ['end_time', '>', $now],
                    ['is_enable', 1],
                    ['is_delete', 0],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '活动名称',
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '活动名称',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'discount',
                            'label' => '优惠规则',
                            'align' => 'left',
                            'driver' => TableItemCustom::class,
                            'value' => function ($row) {
                                $configStore = Be::getConfig('App.Shop.Store');

                                $discounts = Be::getTable('shop_promotion_activity_discount')
                                    ->where('promotion_activity_id', $row['id'])
                                    ->orderBy('ordering', 'ASC')
                                    ->getObjects();

                                $html = '';
                                foreach ($discounts as $discount) {
                                    if ($html === '') {
                                        $html .= '<div>';
                                    } else {
                                        $html .= '<div class="be-mt-50">';
                                    }

                                    if ($row['condition'] === 'min_amount') {
                                        $html .= '满 ' . $configStore->currencySymbol . $discount->min_amount . ' 减 ';
                                    } else {
                                        $html .= '满 ' . $discount->min_quantity . ' 件减 ';
                                    }

                                    if ($row['discount_type'] === 'percent') {
                                        $html .= $discount->discount_percent . '%';
                                    } else {
                                        $html .= $configStore->currencySymbol .$discount->discount_amount;
                                    }

                                    $html .= '</div>';
                                }
                                return $html;
                            },
                        ],
                        [
                            'name' => 'time',
                            'label' => '生效时间',
                            'width' => '180',
                            'driver' => TableItemCustom::class,
                            'value' => function ($row) use ($serviceStore) {
                                if ($row['never_expire'] === '1') {
                                    return $serviceStore->systemTime2StoreTime($row['start_time']);
                                } else {
                                    return $serviceStore->systemTime2StoreTime($row['start_time']) . '<br>' . $serviceStore->systemTime2StoreTime($row['end_time']);
                                }
                            },
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                ],
            ]
        ];
    }


}
