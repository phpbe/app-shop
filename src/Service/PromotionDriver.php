<?php

namespace Be\App\ShopFai\Service;


use Be\App\ServiceException;
use Be\Be;

/**
 * Class PromotionDriver
 *
 * @package Be\App\ShopFai\Service
 */
abstract class PromotionDriver
{

    /**
     * 获取商品可用的活动 界面模板
     * @param string $productId
     * @return string 模板HTML
     */
    abstract function getProductTemplate(string $productId): string;

    /**
     * 获取优惠
     * 只匹配并返回一个优惠，
     * 如需指定优惠，可在购物车传入指定参数
     *
     * @param array $cart 购物车数据
     * @return array|false
     * [
     *      'promotion_type' => 'promotion_activity', // 优惠类型： promotion_coupon / promotion_activity
     *      'promotion_id' => '[promotion_activity_id]', // 优惠ID
     *      'amount' => '0.00', // 优惠金额
     * ]
     */
    abstract function getDiscount(array $cart = []);

    /**
     * 获取优惠详细明细
     *
     * @param string $id 优惠ID
     * @return object
     */
    abstract function getDetails(string $id): object;

}
