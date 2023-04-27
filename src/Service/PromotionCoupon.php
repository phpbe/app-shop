<?php

namespace Be\App\Shop\Service;

use Be\App\ServiceException;
use Be\Be;

/**
 * Class PromotionCoupon
 *
 * @package Be\App\Shop\Service
 */
class PromotionCoupon extends PromotionDriver
{

    /**
     * 获取商品可用的活动 界面模板
     * @param string $productId
     * @return string 模板HTML
     */
    public function getProductTemplate(string $productId): string
    {
        $configStore = Be::getConfig('App.Shop.Store');
        $serviceStore = Be::getService('App.Shop.Store');
        $now = $serviceStore->systemTime2StoreTime(date('Y-m-d H:i:s'));

        $promotionCoupons = Be::getTable('shop_promotion_coupon')
            ->where('start_time', '<', $now)
            ->where('end_time', '>', $now)
            ->where('show', 1)
            ->where('is_enable', 1)
            ->where('is_delete', 0)
            ->getObjects();

        if (count($promotionCoupons) === 0) {
            return '';
        }

        $template = '';
        foreach ($promotionCoupons as $promotionCoupon) {
            $match = false;
            if ($promotionCoupon->scope_product === 'all') {
                $match = true;
            } elseif ($promotionCoupon->scope_product === 'assign') {
                if (Be::getTable('shop_promotion_coupon_scope_product')
                        ->where('promotion_coupon_id', $promotionCoupon->id)
                        ->where('product_id', $productId)
                        ->count() > 0) {
                    $match = true;
                }
            } elseif ($promotionCoupon->scope_product === 'category') {
                $product = Be::getService('Shop.Product')->getProduct($productId);
                $productCategoryIds = $product->category_ids;
                $promotionCategoryIds = Be::getTable('shop_promotion_coupon_scope_category')
                    ->where('promotion_coupon_id', $promotionCoupon->id)
                    ->getValues('category_id');
                if (count(array_intersect($productCategoryIds, $promotionCategoryIds)) > 0) {
                    $match = true;
                }
            }

            if (!$match) {
                continue;
            }

            $match = false;
            if ($promotionCoupon->scope_user === 'all') {
                $match = true;
            } elseif ($promotionCoupon->scope_user === 'assign') {
                // 指定用户
                $my = Be::getUser();
                if (Be::getTable('shop_promotion_coupon_scope_user')
                        ->where('promotion_coupon_id', $promotionCoupon->id)
                        ->where('user_id', $my->id)
                        ->count() > 0) {
                    $match = true;
                }
            }

            if (!$match) {
                continue;
            }


            // 检查总发放量
            if ($promotionCoupon->limit_quantity > 0) {
                $count = Be::getTable('shop_order_promotion')
                    ->where('promotion_type', 'promotion_coupon')
                    ->where('promotion_id', $promotionCoupon->id)
                    ->count();
                if ($count >= $promotionCoupon->limit_quantity) {
                    continue;
                }
            }

            // 检查每人可用次数
            if ($promotionCoupon->limit_times > 0) {
                $my = Be::getUser();
                $tuple = Be::getTuple('shop_promotion_coupon_user');
                try {
                    $tuple->loadBy([
                        'promotion_coupon_id' => $promotionCoupon->id,
                        'user_id' => $my->id,
                    ]);
                } catch (\Throwable $t) {
                }

                if ($tuple->isLoaded()) {
                    if ($tuple->usage >= $promotionCoupon->limit_times) {
                        continue;
                    }
                }
            }

            $template .= '<div class="be-col-auto">';
            $template .= '<div class="be-pt-50 be-pr-50">';
            $template .= '<div class="be-p-100" style="background-color:#FDF7F7;">';
            $template .= '<div class="be-row">';

            $template .= '<div class="be-col-auto">';
            $template .= '<div class="be-pr-400">';
            if ($promotionCoupon->condition !== 'none') {
                $template .= '<div style="color:#C20000;">Buy ';
                if ($promotionCoupon->condition === 'min_amount') {
                    $template .= $configStore->currencySymbol . $promotionCoupon->condition_min_amount;
                } elseif ($promotionCoupon->condition === 'min_quantity') {
                    $template .= $promotionCoupon->condition_min_quantity;
                }
                $template .= '</div>';
            }
            $template .= '<div class="be-fs-150 be-lh-200 be-fw-bold" style="color:#C20000;">';
            if ($promotionCoupon->discount_type === 'percent') {
                $template .= $promotionCoupon->discount_percent . '%';
            } else {
                $template .= $configStore->currencySymbol . $promotionCoupon->discount_amount;
            }
            $template .= ' OFF</div>';
            if ($promotionCoupon->never_expire !== '1') {
                $endTime = $serviceStore->systemTime2StoreTime($promotionCoupon->end_time);
                $template .= '<div class="be-c-999 be-fs-80">until '.date('M j, Y', strtotime($endTime)).'</div>';
            }
            $template .= '</div>';
            $template .= '</div>';

            $template .= '<div class="be-col-auto">';
            $template .= '<div class="be-ta-center be-c-666">' . $promotionCoupon->code . '</div>';
            $template .= '<div class="be-ta-center be-mt-50"><input type="button" class="be-btn be-btn-major be-btn-round be-btn-sm" value="APPLY"></div>';
            $template .= '</div>';

            $template .= '</div>';
            $template .= '</div>';
            $template .= '</div>';
            $template .= '</div>';
        }

        if ($template) {
            $template = '<div class="be-row">' . $template . '</div>';
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
        $couponDiscount = false;
        try {
            $couponDiscount = $this->getCouponDiscount($cart);
        } catch (\Throwable $t) {
        }

        if ($couponDiscount === false) {
            return false;
        }

        return [
            'promotion_type' => 'promotion_coupon',
            'promotion_id' => $couponDiscount->id,
            'amount' => $couponDiscount->discount_amount,
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

        $sql = 'SELECT * FROM shop_promotion_coupon WHERE id=?';
        $promotionCoupon = $db->getObject($sql, [$id]);

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
                            $product->image = Be::getProperty('App.Shop')->getWwwUrl() . '/image/product/no-image.webp';
                        }
                    }
                    unset($product);

                    $promotionCoupon->scope_products = $products;
                }
            }
        } elseif ($promotionCoupon->scope_product === 'category') {
            $categoryIds = Be::getTable('shop_promotion_coupon_scope_category')
                ->where('promotion_coupon_id', $id)
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
                ->where('promotion_coupon_id', $id)
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

    // ----------------------------------------------------------------------------------------------------------------- 以下为优惠码专有函数

    /**
     * 检查优惠码是否可用
     *
     * @param array $cart 购物车数据
     * @return bool
     */
    public function check(array $cart = []): bool
    {
        $this->getCouponDiscount($cart);
        return true;
    }


    /**
     * 获取活动优惠金额
     * @param array $cart 处理过的购物车数据
     * @return object
     */
    private function getCouponDiscount(array $cart = []): object
    {
        if (!isset($cart['promotion_coupon_code']) || !is_string($cart['promotion_coupon_code'])) {
            throw new ServiceException('Please entry your discount code!');
        }

        if (!isset($cart['products']) || !is_array($cart['products']) || count($cart['products']) === 0) {
            $cart['products'] = Be::getService('App.Shop.Cart')->formatProducts($cart, true);
        }

        $my = Be::getUser();

        $tuplePromotionCoupon = Be::getTuple('shop_promotion_coupon');
        try {
            $tuplePromotionCoupon->loadBy([
                'code' => $cart['promotion_coupon_code'],
                'is_enable' => 1,
                'is_delete' => 0,
            ]);
        } catch (\Throwable $t) {
            throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）does not exist!');
        }

        $storeService = Be::getService('App.Shop.Store');
        $now = $storeService->systemTime2StoreTime(date('Y-m-d H:i:s'));
        $t0 = strtotime($now);
        $t1 = strtotime($tuplePromotionCoupon->start_time);
        $t2 = strtotime($tuplePromotionCoupon->end_time);
        if ($t0 < $t1) {
            throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）is not available!');
        }

        if ($t0 > $t2) {
            throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）is out of date!');
        }

        // 检查总发放量
        if ($tuplePromotionCoupon->limit_quantity > 0) {
            $count = Be::getTable('shop_order_promotion')
                ->where('promotion_type', 'promotion_coupon')
                ->where('promotion_id', $tuplePromotionCoupon->id)
                ->count();
            if ($count >= $tuplePromotionCoupon->limit_quantity) {
                throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）is out of usage!');
            }
        }

        // 检查每人可用次数
        if ($tuplePromotionCoupon->limit_times > 0) {
            $tuple = Be::getTuple('shop_promotion_coupon_user');
            try {
                $tuple->loadBy([
                    'promotion_coupon_id' => $tuplePromotionCoupon->id,
                    'user_id' => $my->id,
                ]);
            } catch (\Throwable $t) {
            }

            if ($tuple->isLoaded()) {
                if ($tuple->usage >= $tuplePromotionCoupon->limit_times) {
                    throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）is out of usage!');
                }
            }
        }

        if ($tuplePromotionCoupon->scope_user === 'assign') {
            // 指定用户
            if (Be::getTable('shop_promotion_coupon_scope_user')
                    ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                    ->where('user_id', $my->id)
                    ->count() === 0) {
                throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）does not exists!');
            }
        }

        $totalAmount = '0.00';
        $totalQuantity = 0;

        $match = false;
        if ($tuplePromotionCoupon->scope_product === 'all') { // 不限商品
            $match = true;

            if ($tuplePromotionCoupon->condition === 'min_amount') {
                // 最低消费金额
                foreach ($cart['products'] as $product) {
                    $totalAmount = bcadd($totalAmount, bcmul($product->price, $product->quantity, 2), 2);
                }

            } elseif ($tuplePromotionCoupon->condition === 'min_quantity') {
                // 最低购买数量
                foreach ($cart['products'] as $product) {
                    $totalQuantity += $product->quantity;
                }
            }

        } elseif ($tuplePromotionCoupon->scope_product === 'assign') { // 指定商品
            $assignedProductIds = Be::getTable('shop_promotion_coupon_scope_product')
                ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                ->getValues('product_id');

            if ($tuplePromotionCoupon->condition === 'min_amount') {
                // 最低消费金额
                foreach ($cart['products'] as $product) {
                    if (in_array($product->product_id, $assignedProductIds)) {
                        $match = true;
                        $totalAmount = bcadd($totalAmount, bcmul($product->price, $product->quantity, 2), 2);
                    }
                }
            } elseif ($tuplePromotionCoupon->condition === 'min_quantity') {
                // 最低购买数量
                foreach ($cart['products'] as $product) {
                    if (in_array($product->product_id, $assignedProductIds)) {
                        $match = true;
                        $totalQuantity += $product->quantity;
                    }
                }
            }
        } elseif ($tuplePromotionCoupon->scope_product === 'category') { // 指定分类
            // 指定分类
            $assignedCategoryIds = Be::getTable('shop_promotion_coupon_scope_category')
                ->where('promotion_coupon_id', $tuplePromotionCoupon->id)
                ->getValues('category_id');

            if ($tuplePromotionCoupon->condition === 'min_amount') {
                // 最低消费金额
                foreach ($cart['products'] as $product) {
                    if (count(array_intersect($product->category_ids, $assignedCategoryIds)) > 0) {
                        $match = true;
                        $totalAmount = bcadd($totalAmount, bcmul($product->price, $product->quantity, 2), 2);
                    }
                }
            } elseif ($tuplePromotionCoupon->condition === 'min_quantity') {
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
            throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）assigned product categories does match your cart!');
        }

        if ($tuplePromotionCoupon->condition === 'min_amount') {
            // 最低消费金额
            if (bccomp($totalAmount, $tuplePromotionCoupon->condition_min_amount, 2) === -1) {
                throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）required min amount: ' . $configStore->currencySymbol . $tuplePromotionCoupon->condition_min_amount . '!');
            }

            $discountAmount = bcmul($totalAmount, bcdiv($tuplePromotionCoupon->discount_percent, 100, 2), 2);

        } elseif ($tuplePromotionCoupon->condition === 'min_quantity') {
            if ($totalQuantity < $tuplePromotionCoupon->condition_min_quantity) {
                throw new ServiceException('Discount code（' . $cart['promotion_coupon_code'] . '）required min quantity: ' . $tuplePromotionCoupon->condition_min_quantity . '!');
            }

            $discountAmount = $tuplePromotionCoupon->discount_amount;
        }

        $tuplePromotionCoupon->discount_amount = $discountAmount;

        return $tuplePromotionCoupon->toObject();
    }

}
