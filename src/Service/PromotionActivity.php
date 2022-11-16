<?php

namespace Be\App\Shop\Service;

use Be\App\ServiceException;
use Be\Be;

/**
 * Class PromotionActivity
 *
 * @package Be\App\Shop\Service
 */
class PromotionActivity extends PromotionDriver
{


    /**
     * 获取商品可用的活动 界面模板
     * @param string $productId
     * @return string 模板HTML
     */
    public function getProductTemplate(string $productId): string
    {
        $configStore = Be::getConfig('App.Shop.Store');
        $storeService = Be::getService('App.Shop.Store');
        $now = $storeService->systemTime2StoreTime(date('Y-m-d H:i:s'));

        $promotionActivities = Be::getTable('shop_promotion_activity')
            ->where('start_time', '<', $now)
            ->where('end_time', '>', $now)
            ->where('is_enable', 1)
            ->where('is_delete', 0)
            ->getObjects();

        if (count($promotionActivities) === 0) {
            return '';
        }

        $template = '';
        foreach ($promotionActivities as $promotionActivity) {
            $match = false;
            if ($promotionActivity->scope_product === 'all') {
                $match = true;
            } elseif ($promotionActivity->scope_product === 'assign') {
                if (Be::getTable('shop_promotion_activity_scope_product')
                        ->where('promotion_activity_id', $promotionActivity->id)
                        ->where('product_id', $productId)
                        ->count() > 0) {
                    $match = true;
                }
            } elseif ($promotionActivity->scope_product === 'category') {
                $product = Be::getService('Shop.Product')->getProduct($productId);
                $productCategoryIds = $product->category_ids;
                $promotionActivityCategoryIds = Be::getTable('shop_promotion_activity_scope_category')
                    ->where('promotion_activity_id', $promotionActivity->id)
                    ->getValues('category_id');
                if (count(array_intersect($productCategoryIds, $promotionActivityCategoryIds)) > 0) {
                    $match = true;
                }
            }

            if ($match) {
                $promotionActivity->discounts = Be::getTable('shop_promotion_activity_discount')
                    ->where('promotion_activity_id', $promotionActivity->id)
                    ->orderBy('ordering', 'ASC')
                    ->getObjects();

                if ($promotionActivity->discount_text !== '') {
                    $text = '';
                    foreach ($promotionActivity->discounts as $discount) {
                        $param1 = '';
                        $param2 = '';
                        if ($promotionActivity->condition === 'min_amount' && $discount->min_amount !== '') {
                            if ($promotionActivity->discount_type === 'percent' && $discount->discount_percent !== '') {
                                $param1 = $configStore->currencySymbol . $discount->min_amount;
                                $param2 = $discount->discount_percent . '%';
                            } else if ($discount->discount_amount !== '') {
                                $param1 = $configStore->currencySymbol . $discount->min_amount;
                                $param2 = $configStore->currencySymbol . $discount->discount_amount;
                            }
                        }

                        if ($promotionActivity->condition === 'min_quantity' && $discount->min_quantity !== '') {
                            if ($promotionActivity->discount_type === 'percent' && $discount->discount_percent !== '') {
                                $param1 = $discount->min_quantity;
                                $param2 = $discount->discount_percent . '%';
                            } else if ($discount->discount_amount !== '') {
                                $param1 = $discount->min_quantity;
                                $param2 = $configStore->currencySymbol . $discount->discount_amount;
                            }
                        }

                        if ($param1 !== '' && $param2 !== '') {
                            $textItem = $promotionActivity->discount_text;
                            $textItem = str_replace('{优惠条件}', $param1, $textItem);
                            $textItem = str_replace('{优惠值}', $param2, $textItem);
                            $text .= $textItem . '&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                    }

                    if ($text !== '') {
                        $template .= '<div class="be-row be-mt-50 be-px-100 be-py-50" style="background-color: #FDF7F7;">';

                        $template .= '<div class="be-col-auto">';
                        $template .= '<div class="be-pr-100" style="color:#C20000;">';
                        $template .= '<svg width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.334 11.48L19.5 4.5l-6.979-.833L3.187 13a.5.5 0 000 .707l7.105 7.105a.5.5 0 00.707 0l9.335-9.334z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path opacity="0.5" d="M13.5 8.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" fill="currentColor"></path></svg>';
                        $template .= '</div>';
                        $template .= '</div>';

                        $template .= '<div class="be-col be-py-20" style="color:#C20000;">';
                        $template .= $text;
                        $template .= '</div>';

                        $template .= '<div class="be-col-auto">';
                        $template .= '<div class="be-pl-100 be-py-20"><a href="' . beUrl('Shop.PromotionActivity.detail', ['id' => $promotionActivity->id]) . '">Shop more &gt;</a></div>';
                        $template .= '</div>';
                        $template .= '</div>';
                    }
                }
            }
        }

        return $template;
    }


    /**
     * 获取优惠金额
     *
     * @param array $cart 购物车数据
     * @return array|false
     */
    public function getDiscount(array $cart = [])
    {
        if (isset($cart['promotion_activity_id'])) {
            try {
                $tuplePromotionActivity = Be::getTuple('shop_promotion_activity');
                $tuplePromotionActivity->loadBy([
                    'id' => $cart['promotion_activity_id'],
                    'is_enable' => 1,
                    'is_delete' => 0,
                ]);

                $storeService = Be::getService('App.Shop.Store');
                $now = $storeService->systemTime2StoreTime(date('Y-m-d H:i:s'));
                $t0 = strtotime($now);
                $t1 = strtotime($tuplePromotionActivity->start_time);
                $t2 = strtotime($tuplePromotionActivity->end_time);
                if ($t0 < $t1 || $t0 > $t2) {
                    throw new ServiceException('');
                }

                $discountAmount = $this->getActivityDiscountAmount($tuplePromotionActivity, $cart);
                if ($discountAmount === false) {
                    throw new ServiceException('');
                }

                return [
                    'promotion_type' => 'promotion_activity',
                    'promotion_id' => $cart['promotion_activity_id'],
                    'amount' => $discountAmount,
                ];

            } catch (\Throwable $t) {
            }
        }

        $optimalActivity = $this->getOptimalActivity($cart);
        if ($optimalActivity === false) {
            return false;
        }

        return [
            'promotion_type' => 'promotion_activity',
            'promotion_id' => $optimalActivity->id,
            'amount' => $optimalActivity->discount_amount,
        ];
    }

    /**
     * 获取优惠详细明细
     *
     * @param string $id 优惠ID
     * @return object
     */
    public function getDetails(string $id): object
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM shop_promotion_activity WHERE id=?';
        $promotionActivity = $db->getObject($sql, [$id]);

        $promotionActivity->poster = (int)$promotionActivity->poster;
        $promotionActivity->seo = (int)$promotionActivity->seo;
        $promotionActivity->never_expire = (int)$promotionActivity->never_expire;
        $promotionActivity->is_enable = (int)$promotionActivity->is_enable;
        $promotionActivity->is_delete = (int)$promotionActivity->is_delete;

        $promotionActivity->scope_products = [];
        $promotionActivity->scope_categories = [];
        if ($promotionActivity->scope_product === 'assign') {
            $productIds = Be::getTable('shop_promotion_activity_scope_product')
                ->where('promotion_coupon_id', $id)
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
                            $product->image = Be::getProperty('App.Shop')->getWwwUrl() . '/image/product/no-image.jpg';
                        }
                    }
                    unset($product);

                    $promotionActivity->scope_products = $products;
                }
            }
        } elseif ($promotionActivity->scope_product === 'category') {
            $categoryIds = Be::getTable('shop_promotion_activity_scope_category')
                ->where('promotion_coupon_id', $id)
                ->getValues('category_id');
            if (count($categoryIds) > 0) {
                $categories = Be::getTable('shop_category')
                    ->where('id', 'IN', $categoryIds)
                    ->getObjects();
                foreach ($categories as &$category) {
                    if (!$category->image) {
                        $category->image = Be::getProperty('App.Shop')->getWwwUrl() . '/image/category/no-image.jpg';
                    }
                }
                unset($category);

                $promotionActivity->scope_categories = $categories;
            }
        }

        $promotionActivity->discounts = Be::getTable('shop_promotion_activity_discount')
            ->where('promotion_activity_id', $id)
            ->orderBy('ordering', 'ASC')
            ->getObjects();

        return $promotionActivity;
    }


    // ----------------------------------------------------------------------------------------------------------------- 以下为满减活动专有函数

    /**
     * 获取有效的的满减活动
     * @param array $cart
     * @return array
     */
    public function getAvailableActivities(array $cart = []): array
    {
        $storeService = Be::getService('App.Shop.Store');
        $now = $storeService->systemTime2StoreTime(date('Y-m-d H:i:s'));

        $productActivities = Be::getTable('shop_promotion_activity')
            ->where('start_time', '<', $now)
            ->where('end_time', '>', $now)
            ->where('is_enable', 1)
            ->where('is_delete', 0)
            ->getObjects();

        $availableActivities = [];
        foreach ($productActivities as $promotionActivity) {
            $discountAmount = $this->getActivityDiscountAmount($promotionActivity, $cart);
            if ($discountAmount !== false) {
                $promotionActivity->discount_amount = $discountAmount;
                $availableActivities[] = $promotionActivity;
            }
        }

        return $availableActivities;
    }

    /**
     * 获取最优的满减活动
     * @param array $cart
     * @return object|false
     */
    public function getOptimalActivity(array $cart = [])
    {
        $optimalActivity = false;
        $availableActivities = $this->getAvailableActivities($cart);
        if (count($availableActivities) > 0) {
            foreach ($availableActivities as $promotionActivity) {
                if ($optimalActivity === false) {
                    $optimalActivity = $promotionActivity;
                } else {
                    // 比较找出优惠金额最多的
                    if (bccomp($promotionActivity->discount_amount, $optimalActivity->discount_amount, 2) === 1) {
                        $optimalActivity = $promotionActivity;
                    }
                }
            }
        }
        return $optimalActivity;
    }

    /**
     * 获取活动优惠金额
     * @param object $promotionActivity 活动数据
     * @param array $cart 处理过的购物车数据
     * @return string|false
     */
    private function getActivityDiscountAmount(object $promotionActivity, array $cart = [])
    {
        if (!isset($cart['products']) || !is_array($cart['products']) || count($cart['products']) === 0) {
            $cart['products'] = Be::getService('App.Shop.Cart')->formatProducts($cart, true);
        }

        $totalAmount = '0.00';
        $totalQuantity = 0;

        $match = false;
        if ($promotionActivity->scope_product === 'all') { // 不限商品
            $match = true;

            if ($promotionActivity->condition === 'min_amount') {
                // 最低消费金额
                foreach ($cart['products'] as $product) {
                    $totalAmount = bcadd($totalAmount, bcmul($product->price, $product->quantity, 2), 2);
                }

            } elseif ($promotionActivity->condition === 'min_quantity') {
                // 最低购买数量
                foreach ($cart['products'] as $product) {
                    $totalQuantity += $product->quantity;
                }
            }

        } elseif ($promotionActivity->scope_product === 'assign') { // 指定商品
            $assignedProductIds = Be::getTable('shop_promotion_activity_scope_product')
                ->where('promotion_activity_id', $promotionActivity->id)
                ->getValues('product_id');

            if ($promotionActivity->condition === 'min_amount') {
                // 最低消费金额
                foreach ($cart['products'] as $product) {
                    if (in_array($product->product_id, $assignedProductIds)) {
                        $match = true;
                        $totalAmount = bcadd($totalAmount, bcmul($product->price, $product->quantity, 2), 2);
                    }
                }
            } elseif ($promotionActivity->condition === 'min_quantity') {
                // 最低购买数量
                foreach ($cart['products'] as $product) {
                    if (in_array($product->product_id, $assignedProductIds)) {
                        $match = true;
                        $totalQuantity += $product->quantity;
                    }
                }
            }
        } elseif ($promotionActivity->scope_product === 'category') { // 指定分类
            // 指定分类
            $assignedCategoryIds = Be::getTable('shop_promotion_activity_scope_category')
                ->where('promotion_activity_id', $promotionActivity->id)
                ->getValues('category_id');

            if ($promotionActivity->condition === 'min_amount') {
                // 最低消费金额
                foreach ($cart['products'] as $product) {
                    if (count(array_intersect($product->category_ids, $assignedCategoryIds)) > 0) {
                        $match = true;
                        $totalAmount = bcadd($totalAmount, bcmul($product->price, $product->quantity, 2), 2);
                    }
                }
            } elseif ($promotionActivity->condition === 'min_quantity') {
                // 最低购买数量
                foreach ($cart['products'] as $product) {
                    if (count(array_intersect($product->category_ids, $assignedCategoryIds)) > 0) {
                        $match = true;
                        $totalQuantity += $product->quantity;
                    }
                }
            }
        }

        if (!$match) {
            return false;
        }

        $discounts = Be::getTable('shop_promotion_activity_discount')
            ->where('promotion_activity_id', $promotionActivity->id)
            ->orderBy('ordering', 'ASC')
            ->getObjects();

        // 最大优惠金额
        $maxDiscountAmount = '0.00';
        foreach ($discounts as $discount) {
            $discountAmount = '0.00';
            if ($promotionActivity->condition === 'min_amount') {
                if (bccomp($totalAmount, $discount->min_amount, 2) === -1) {
                    continue;
                }
            } elseif ($promotionActivity->condition === 'min_quantity') {
                if ($totalQuantity < (int)$discount->min_quantity) {
                    continue;
                }
            }

            if ($promotionActivity->discount_type === 'percent') {
                $discountAmount = bcmul($totalAmount, bcdiv($discount->discount_percent, 100, 2), 2);
            } elseif ($promotionActivity->discount_type === 'amount') {
                $discountAmount = $discount->discount_amount;
            }

            // 比较找出优惠金额最多的
            if (bccomp($discountAmount, $maxDiscountAmount, 2) === 1) {
                $maxDiscountAmount = $discountAmount;
            }
        }

        return $maxDiscountAmount;
    }

    /**
     * 获取指定ID的满减活动
     *
     * @param $promotionActivityId
     * @return Object
     * @throws ServiceException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getPromotionActivity(string $promotionActivityId): object
    {
        $tuplePromotionActivity = Be::getTuple('shop_promotion_activity');
        try {
            $tuplePromotionActivity->loadBy([
                'id' => $promotionActivityId,
                'is_enable' => 1,
                'is_delete' => 0,
            ]);
        } catch (\Throwable $t) {
            throw new ServiceException('Activity (#' . $promotionActivityId . ') does not exist!');
        }

        $storeService = Be::getService('App.Shop.Store');
        $now = $storeService->systemTime2StoreTime(date('Y-m-d H:i:s'));
        $t0 = strtotime($now);
        $t1 = strtotime($tuplePromotionActivity->start_time);
        $t2 = strtotime($tuplePromotionActivity->end_time);
        if ($t0 < $t1 || $t0 > $t2) {
            throw new ServiceException('Activity (#' . $promotionActivityId . ' ' . $tuplePromotionActivity->name . ') is disabled!');
        }

        return $tuplePromotionActivity->toObject();
    }

    /**
     * 获取指定ID的满减活动下的商品
     *
     * @param object $promotionActivity
     * @return array
     * @throws ServiceException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getPromotionActivityProducts(object $promotionActivity, array $options): array
    {
        if (isset($options['pageSize']) && is_numeric($options['pageSize'])) {
            $pageSize = (int)$options['pageSize'];
            if ($pageSize < 1) {
                $pageSize = 15;
            }
        } else {
            $pageSize = 15;
        }

        if (isset($options['page']) && is_numeric($options['page'])) {
            $page = (int)$options['page'];
            if ($page < 1) {
                $page = 1;
            }
        } else {
            $page = 1;
        }

        if ($promotionActivity->scope_product === 'all') {
            return Be::getService('App.Shop.Product')->filter([
                'pageSize' => $pageSize,
                'page' => $page,
            ]);
        } elseif ($promotionActivity->scope_product === 'assign') {
            $promotionActivityProductIds = Be::getTable('shop_promotion_activity_scope_product')
                ->where('promotion_activity_id', $promotionActivity->id)
                 ->getValues('product_id');
            return Be::getService('App.Shop.Product')->filter([
                'productIds' => $promotionActivityProductIds,
                'pageSize' => $pageSize,
                'page' => $page,
            ]);
        } elseif ($promotionActivity->scope_product === 'category') {
            $promotionActivityCategoryIds = Be::getTable('shop_promotion_activity_scope_category')
                ->where('promotion_activity_id', $promotionActivity->id)
                ->getValues('category_id');
            return Be::getService('App.Shop.Product')->filter([
                'categoryIds' => $promotionActivityCategoryIds,
                'pageSize' => $pageSize,
                'page' => $page,
            ]);
        }
    }

    /**
     * 获取满减活动伪静态页网址
     *
     * @param array $params
     * @return string
     * @throws ServiceException
     */
    public function getPromotionActivityUrl(array $params = []): string
    {
        $promotionActivity = $this->getPromotionActivity($params['id']);
        return '/activity/' . $promotionActivity->url;
    }

}
