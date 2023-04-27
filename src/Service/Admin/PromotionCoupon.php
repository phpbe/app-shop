<?php

namespace Be\App\Shop\Service\Admin;

use Be\App\ServiceException;
use Be\Be;
use Be\Db\Tuple;
use Be\Util\Crypt\Random;

class PromotionCoupon
{

    /**
     * 获取优惠券
     *
     * @param string $promotionCouponId
     * @return object
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getPromotionCoupon(string $promotionCouponId): object
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM shop_promotion_coupon WHERE id=?';
        $promotionCoupon = $db->getObject($sql, [$promotionCouponId]);

        $promotionCoupon->discount_percent = (int)$promotionCoupon->discount_percent;
        $promotionCoupon->condition_min_quantity = (int)$promotionCoupon->condition_min_quantity;
        $promotionCoupon->show = (int)$promotionCoupon->show;
        $promotionCoupon->limit_quantity = (int)$promotionCoupon->limit_quantity;
        $promotionCoupon->limit_times = (int)$promotionCoupon->limit_times;
        $promotionCoupon->never_expire = (int)$promotionCoupon->never_expire;
        $promotionCoupon->is_enable = (int)$promotionCoupon->is_enable;
        $promotionCoupon->is_delete = (int)$promotionCoupon->is_delete;

        $promotionCoupon->scope_products = [];
        $promotionCoupon->scope_categories = [];
        if ($promotionCoupon->scope_product === 'assign') {
            $productIds = Be::getTable('shop_promotion_coupon_scope_product')
                ->where('promotion_coupon_id', $promotionCouponId)
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

                    $promotionCoupon->scope_products = $products;
                }
            }
        } elseif ($promotionCoupon->scope_product === 'category') {
            $categoryIds = Be::getTable('shop_promotion_coupon_scope_category')
                ->where('promotion_coupon_id', $promotionCouponId)
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

                $promotionCoupon->scope_categories = $categories;
            }
        }

        $promotionCoupon->scope_users = [];
        if ($promotionCoupon->scope_user === 'assign') {
            $userIds = Be::getTable('shop_promotion_coupon_scope_user')
                ->where('promotion_coupon_id', $promotionCouponId)
                ->getValues('user_id');
            if (count($userIds) > 0) {
                $users = Be::getTable('shop_user')
                    ->where('id', 'IN', $userIds)
                    ->getObjects();

                $promotionCoupon->scope_users = $users;
            }
        }

        return $promotionCoupon;
    }

    /**
     * 编辑优惠券
     *
     * @param array $data 优惠券数据
     * @return Tuple
     * @throws \Throwable
     */
    public function edit($data)
    {
        $db = Be::getDb();

        $isNew = true;
        $promotionCouponId = null;
        if (isset($data['id']) && is_string($data['id']) && $data['id'] !== '') {
            $isNew = false;
            $promotionCouponId = $data['id'];
        }

        $tuplePromotionCoupon = Be::getTuple('shop_promotion_coupon');
        if (!$isNew) {
            try {
                $tuplePromotionCoupon->load($promotionCouponId);
            } catch (\Throwable $t) {
                throw new ServiceException('优惠券（# ' . $promotionCouponId . '）不存在！');
            }
        }

        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new ServiceException('名称未填写！');
        }

        if (!isset($data['code']) || !is_string($data['code'])) {
            throw new ServiceException('优惠码未填写！');
        }

        if ($isNew || $tuplePromotionCoupon->code !== $data['code']) {
            $table = Be::getTable('shop_promotion_coupon')
                ->where('code', $data['code']);

            if (!$isNew) {
                $table->where('id', '!=', $promotionCouponId);
            }

            if ($table->getValue('COUNT(*)') > 0) {
                throw new ServiceException('优惠码（' . $data['code'] . '）已存在！');
            }
        }

        if (!isset($data['discount_type']) || !is_string($data['discount_type']) || !in_array($data['discount_type'], ['percent', 'amount'])) {
            throw new ServiceException('优惠类型无效！');
        }

        if ($data['discount_type'] === 'percent') {
            if (!isset($data['discount_percent']) || !is_numeric($data['discount_percent'])) {
                throw new ServiceException('优惠减免折扣无效！');
            }

            $data['discount_percent'] = (int)$data['discount_percent'];
            if ($data['discount_percent'] <= 0 || $data['discount_percent'] >= 100) {
                throw new ServiceException('优惠减免折扣无效！');
            }
        } else {
            if (!isset($data['discount_amount']) || !is_numeric($data['discount_amount'])) {
                throw new ServiceException('优惠减免金额无效！');
            }

            $data['discount_amount'] = number_format($data['discount_amount'], 2, '.', '');

            if (bccomp($data['discount_amount'], '0.01') === -1) {
                throw new ServiceException('优惠减免金额无效！');
            }
        }

        if (!isset($data['condition']) || !is_string($data['condition']) || !in_array($data['condition'], ['none', 'min_amount', 'min_quantity'])) {
            throw new ServiceException('优惠条件无效！');
        }

        if ($data['condition'] === 'min_amount') {
            if (!isset($data['condition_min_amount']) || !is_numeric($data['condition_min_amount'])) {
                throw new ServiceException('优惠条件-最低消费金额无效！');
            }

            $data['condition_min_amount'] = number_format($data['condition_min_amount'], 2, '.', '');

            if (bccomp($data['condition_min_amount'], '0.01') === -1) {
                throw new ServiceException('优惠条件-最低消费金额无效！');
            }

        } elseif ($data['condition'] === 'min_quantity') {
            if (!isset($data['condition_min_quantity']) || !is_numeric($data['condition_min_quantity'])) {
                throw new ServiceException('优惠条件-最低购买数量无效！');
            }

            $data['condition_min_quantity'] = (int)$data['condition_min_quantity'];
            if ($data['condition_min_quantity'] <= 0) {
                throw new ServiceException('优惠条件-最低购买数量无效！');
            }
        }

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

        if (!isset($data['scope_user']) || !is_string($data['scope_user']) || !in_array($data['scope_user'], ['all', 'assign'])) {
            throw new ServiceException('适用客户无效！');
        }

        if ($data['scope_user'] === 'assign') {
            if (!isset($data['scope_users']) || !is_array($data['scope_users']) || count($data['scope_users']) === 0) {
                throw new ServiceException('适用客户 - 未指定客户！');
            }

            foreach ($data['scope_users'] as $user) {
                if (!isset($user['id']) || !is_string($user['id'])) {
                    throw new ServiceException('适用商品 - 指定的客户无效！');
                }
            }
        }

        if (!isset($data['show']) || !is_numeric($data['show'])) {
            throw new ServiceException('是否在商品详情页显示该优惠券参数无效！');
        }

        $data['show'] = (int)$data['show'];
        if (!in_array($data['show'], [0, 1])) {
            throw new ServiceException('是否在商品详情页显示该优惠券参数无效！');
        }

        if (!isset($data['limit_quantity']) || !is_numeric($data['limit_quantity'])) {
            throw new ServiceException('总发放量无效！');
        }
        $data['limit_quantity'] = (int)$data['limit_quantity'];
        if ($data['limit_quantity'] < 0) {
            throw new ServiceException('总发放量无效！');
        }

        if (!isset($data['limit_times']) || !is_numeric($data['limit_times'])) {
            throw new ServiceException('每人可用次数无效！');
        }
        $data['limit_times'] = (int)$data['limit_times'];
        if ($data['limit_times'] < 0) {
            throw new ServiceException('每人可用次数无效！');
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

        $serviceStore = Be::getService('App.Shop.Admin.Store');

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tuplePromotionCoupon->name = $data['name'];
            $tuplePromotionCoupon->code = $data['code'];
            $tuplePromotionCoupon->discount_type = $data['discount_type'];

            if ($data['discount_type'] === 'percent') {
                $tuplePromotionCoupon->discount_percent = $data['discount_percent'];
                //$tuplePromotionCoupon->discount_amount = '0.00';
            } else {
                //$tuplePromotionCoupon->discount_percent = '0';
                $tuplePromotionCoupon->discount_amount = $data['discount_amount'];
            }

            $tuplePromotionCoupon->condition = $data['condition'];
            if ($data['condition'] === 'min_amount') {
                $tuplePromotionCoupon->condition_min_amount = $data['condition_min_amount'];
                //$tuplePromotionCoupon->condition_min_quantity = '0';
            } elseif ($data['condition'] === 'min_quantity') {
                //$tuplePromotionCoupon->condition_min_amount = '0.00';
                $tuplePromotionCoupon->condition_min_quantity = $data['condition_min_quantity'];
            }

            $tuplePromotionCoupon->scope_product = $data['scope_product'];

            $tuplePromotionCoupon->scope_user = $data['scope_user'];

            $tuplePromotionCoupon->show = $data['show'];

            $tuplePromotionCoupon->limit_quantity = $data['limit_quantity'];
            $tuplePromotionCoupon->limit_times = $data['limit_times'];

            $tuplePromotionCoupon->start_time = $serviceStore->storeTime2SystemTime($data['start_time']);
            $tuplePromotionCoupon->never_expire = $data['never_expire'];

            if ($data['never_expire'] === 0) {
                $tuplePromotionCoupon->end_time = $serviceStore->storeTime2SystemTime($data['end_time']);;
            } else {
                $tuplePromotionCoupon->end_time = '2038-01-01 00:00:00';
            }

            $tuplePromotionCoupon->update_time = $now;
            if ($isNew) {
                $tuplePromotionCoupon->create_time = $now;
                $tuplePromotionCoupon->insert();
            } else {
                $changeDetails = $tuplePromotionCoupon->getChangeDetails();
                $tuplePromotionCoupon->update();

                if (count($changeDetails) > 1) {
                    $tuplePromotionCouponChange = Be::getTuple('shop_promotion_coupon_change');
                    $tuplePromotionCouponChange->promotion_coupon_id = $tuplePromotionCoupon->id;
                    $tuplePromotionCouponChange->details = json_encode($changeDetails);
                    $tuplePromotionCouponChange->create_time = $now;
                    $tuplePromotionCouponChange->insert();
                }
            }

            if ($data['scope_product'] === 'assign') {
                if ($isNew) {
                    foreach ($data['scope_products'] as $product) {
                        $tuplePromotionCouponScopeProduct = Be::getTuple('shop_promotion_coupon_scope_product');
                        $tuplePromotionCouponScopeProduct->promotion_coupon_id = $tuplePromotionCoupon->id;
                        $tuplePromotionCouponScopeProduct->product_id = $product['id'];
                        $tuplePromotionCouponScopeProduct->insert();
                    }
                } else {
                    $productIds = [];
                    foreach ($data['scope_products'] as $product) {
                        $productIds[] = $product['id'];
                    }

                    $existProductIds = Be::getTable('shop_promotion_coupon_scope_product')
                        ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                        ->getValues('product_id');

                    // 需要删除的
                    if (count($existProductIds) > 0) {
                        $removeProductIds = array_diff($existProductIds, $productIds);
                        if (count($removeProductIds) > 0) {
                            Be::getTable('shop_promotion_coupon_scope_product')
                                ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
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
                            $tuplePromotionCouponScopeProduct = Be::getTuple('shop_promotion_coupon_scope_product');
                            $tuplePromotionCouponScopeProduct->promotion_coupon_id = $tuplePromotionCoupon->id;
                            $tuplePromotionCouponScopeProduct->product_id = $newProductId;
                            $tuplePromotionCouponScopeProduct->insert();
                        }
                    }
                }
            } elseif ($data['scope_product'] === 'category') {
                if ($isNew) {
                    foreach ($data['scope_categories'] as $category) {
                        $tuplePromotionCouponScopeCategory = Be::getTuple('shop_promotion_coupon_scope_category');
                        $tuplePromotionCouponScopeCategory->promotion_coupon_id = $tuplePromotionCoupon->id;
                        $tuplePromotionCouponScopeCategory->category_id = $category['id'];
                        $tuplePromotionCouponScopeCategory->insert();
                    }
                } else {
                    $categoryIds = [];
                    foreach ($data['scope_categories'] as $category) {
                        $categoryIds[] = $category['id'];
                    }

                    $existCategoryIds = Be::getTable('shop_promotion_coupon_scope_category')
                        ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                        ->getValues('category_id');

                    // 需要删除的
                    if (count($existCategoryIds) > 0) {
                        $removeCategoryIds = array_diff($existCategoryIds, $categoryIds);
                        if (count($removeCategoryIds) > 0) {
                            Be::getTable('shop_promotion_coupon_scope_category')
                                ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
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
                            $tuplePromotionCouponScopeCategory = Be::getTuple('shop_promotion_coupon_scope_category');
                            $tuplePromotionCouponScopeCategory->promotion_coupon_id = $tuplePromotionCoupon->id;
                            $tuplePromotionCouponScopeCategory->category_id = $newCategoryId;
                            $tuplePromotionCouponScopeCategory->insert();
                        }
                    }
                }
            }

            if ($data['scope_user'] === 'assign') {
                if ($isNew) {
                    foreach ($data['scope_users'] as $user) {
                        $tuplePromotionCouponScopeUser = Be::getTuple('shop_promotion_coupon_scope_user');
                        $tuplePromotionCouponScopeUser->promotion_coupon_id = $tuplePromotionCoupon->id;
                        $tuplePromotionCouponScopeUser->user_id = $user['id'];
                        $tuplePromotionCouponScopeUser->insert();
                    }
                } else {
                    $userIds = [];
                    foreach ($data['scope_users'] as $user) {
                        $userIds[] = $user['id'];
                    }

                    $existUserIds = Be::getTable('shop_promotion_coupon_scope_user')
                        ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                        ->getValues('user_id');

                    // 需要删除的
                    if (count($existUserIds) > 0) {
                        $removeUserIds = array_diff($existUserIds, $userIds);
                        if (count($removeUserIds) > 0) {
                            Be::getTable('shop_promotion_coupon_scope_user')
                                ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                                ->where('user_id', 'IN', $removeUserIds)
                                ->delete();
                        }
                    }

                    // 新增的
                    $newUserIds = null;
                    if (count($existUserIds) > 0) {
                        $newUserIds = array_diff($userIds, $existUserIds);
                    } else {
                        $newUserIds = $userIds;
                    }
                    if (count($newUserIds) > 0) {
                        foreach ($newUserIds as $newUserId) {
                            $tuplePromotionCouponUser = Be::getTuple('shop_promotion_coupon_scope_user');
                            $tuplePromotionCouponUser->promotion_coupon_id = $tuplePromotionCoupon->id;
                            $tuplePromotionCouponUser->user_id = $newUserId;
                            $tuplePromotionCouponUser->insert();
                        }
                    }
                }
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException(($isNew ? '新建' : '编辑') . '优惠券发生异常！');
        }

        return $tuplePromotionCoupon;
    }

    /**
     * 删除优惠券
     *
     * @param array $promotionCouponIds
     * @return void
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function delete(array $promotionCouponIds)
    {
        if (count($promotionCouponIds) === 0) return;

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            foreach ($promotionCouponIds as $promotionCouponId) {

                $tuplePromotionCoupon = Be::getTuple('shop_promotion_coupon');
                try {
                    $tuplePromotionCoupon->loadBy($promotionCouponId);
                } catch (\Throwable $t) {
                    throw new ServiceException('优惠券（# ' . $promotionCouponId . '）不存在！');
                }

                $tuplePromotionCoupon->is_delete = 1;
                $tuplePromotionCoupon->update_time = $now;
                $tuplePromotionCoupon->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('删除优惠券发生异常！');
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
            $exist = Be::getTable('shop_promotion_coupon')
                    ->where('code', $code)
                    ->getValue('COUNT(*)') > 0;
        } while ($exist);

        return $code;
    }


    /**
     * 获取优惠券修改记录
     *
     * @param string $promotionCouponId
     * @return array
     */
    public function getChanges(string $promotionCouponId): array
    {
        $discountTypeKeyValues = ['percent' => '百分比折扣', 'amount' => '固定金额'];
        $conditionKeyValues = ['none' => '无', 'min_amount' => '需消费指定金额', 'min_quantity' => '需购买指定数量'];
        $scopeProductKeyValues = ['all' => '所有商品', 'assign' => '指定商品', 'category' => '指定分类'];
        $scopeUserKeyValues = ['all' => '所有客户', 'assign' => '指定客户'];

        $changes = Be::getTable('shop_promotion_coupon_change')
            ->where('promotion_coupon_id', $promotionCouponId)
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
                    case 'code':
                        $items[] = '优惠码 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'discount_type':
                        $items[] = '优惠类型 从 ' . ($discountTypeKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($discountTypeKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'discount_percent':
                        $items[] = '优惠百分比 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'discount_amount':
                        $items[] = '优惠金额 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'condition':
                        $items[] = '使用条件 从 ' . ($conditionKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($conditionKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'condition_min_amount':
                        $items[] = '最低消费金额 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'condition_min_quantity':
                        $items[] = '最低购买数量 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'scope_product':
                        $items[] = '适用商品 从 ' . ($scopeProductKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($scopeProductKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'scope_user':
                        $items[] = '适用客户 从 ' . ($scopeUserKeyValues[$val['from']] ?? $val['from']) . ' 改为 ' . ($scopeUserKeyValues[$val['to']] ?? $val['to']);
                        break;
                    case 'show':
                        $items[] = '是否在商品详情页显示 从 ' . ($val['from'] ? '是' : '否') . ' 改为 ' . ($val['to'] ? '是' : '否');
                        break;
                    case 'limit_quantity':
                        $items[] = '总发放量 从 ' . $val['from'] . ' 改为 ' . $val['to'];
                        break;
                    case 'limit_times':
                        $items[] = '每人可用次数 从 ' . $val['from'] . ' 改为 ' . $val['to'];
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
                }
            }

            $change->details = $items;
        }

        return $changes;
    }

    /**
     * 获取优惠券统计
     *
     * @param string $promotionCouponId
     * @return array
     */
    public function getStatisticsSummary(string $promotionCouponId): array
    {
        $orderIds = Be::getTable('shop_order_promotion')
            ->where('promotion_type', 'promotion_coupon')
            ->where('promotion_id', $promotionCouponId)
            ->getValues('order_id');

        $orderCount = count($orderIds);

        if ($orderCount > 0) {
            $discountAmount = Be::getTable('shop_order_promotion')
                ->where('promotion_type', 'promotion_coupon')
                ->where('promotion_id', $promotionCouponId)
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
     * 获取优惠券统计
     *
     * @param string $promotionCouponId
     * @return array
     */
    public function getStatistics(string $promotionCouponId): array
    {
        $orderIds = Be::getTable('shop_order_promotion')
            ->where('promotion_type', 'promotion_coupon')
            ->where('promotion_id', $promotionCouponId)
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
                ->where('promotion_type', 'promotion_coupon')
                ->where('promotion_id', $promotionCouponId)
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

}
