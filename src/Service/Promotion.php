<?php

namespace Be\App\Shop\Service;

use Be\Be;

/**
 * Class Promotion
 *
 * @package Be\App\Shop\Service
 */
class Promotion
{

    /**
     * 获取展示在商品详情页的界面模板
     *
     * @param string $productId
     * @return array
     */
    public function getProductTemplates(string $productId): array
    {
        $templates = [];
        $availablePromotionTypes = $this->getAvailablePromotionTypes();
        foreach ($availablePromotionTypes as $type) {
            $service = Be::getService('App.Shop.Promotion' . $type);
            $template = $service->getProductTemplate($productId);
            if ($template !== '') {
                $templates[] = $template;
            }
        }

        return $templates;
    }

    /**
     * 获取优惠金额
     *
     * @param array $cart 购物车数据
     * @return string
     */
    public function getDiscountAmount(array $cart = []): string
    {
        $discountAmount = '0.00';
        $discount = $this->getDiscount($cart);
        if ($discount) {
            $discountAmount = $discount['amount'];
        }

        return $discountAmount;
    }

    /**
     * 获取优惠金额
     *
     * @param array $cart 购物车数据
     * @return array|false
     */
    public function getDiscount(array $cart = [])
    {
        $availablePromotions = $this->getCartAvailablePromotionTypes($cart);
        foreach ($availablePromotions as $type) {
            $service = Be::getService('App.Shop.Promotion' . $type);
            $discount = $service->getDiscount($cart);
            if ($discount !== false) {
                return $discount;
            }
        }

        return false;
    }

    /**
     * 获取可用的优惠
     *
     * @return array
     */
    private function getAvailablePromotionTypes(): array
    {
        return [
            'Coupon',
            'Activity',
        ];
    }

    /**
     * 获取购物车可用的优惠
     *
     * @param array $cart 购物车数据
     * @return array
     */
    private function getCartAvailablePromotionTypes(array $cart = []): array
    {
        $availablePromotionTypes = [];
        if (isset($cart['promotion_coupon_code']) && is_string($cart['promotion_coupon_code'])) {
            $availablePromotionTypes[] = 'Coupon';
        }

        $availablePromotionTypes[] = 'Activity';

        return $availablePromotionTypes;
    }

}
