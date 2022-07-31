<?php

namespace Be\App\ShopFai\Service\Admin;


use Be\Be;

class StatisticCart extends Statistic
{


    /**
     * 获取购物车报表
     *
     * 相当于 SELECT COUNT(*) GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Cart:Report',
            'esIndex' => Be::getConfig('App.ShopFai.Es')->indexStatisticCart,
        ]));
    }

    /**
     * 获取唯一访客 加购物车
     *
     * 相当于 SELECT COUNT(DISTINCT(user_id)) GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getUniqueUserReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Cart:UniqueUserReport',
            'esIndex' => Be::getConfig('App.ShopFai.Es')->indexStatisticCart,

            // 按 user_token 取唯一
            'cardinality' => 'user_token',
        ]));
    }


    /**
     * 获取加购物车总数量
     *
     * 相当于 SELECT COUNT(*)
     *
     * @param array $options 参数
     * @return int
     */
    public function getCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Cart:Count',
            'esIndex' => Be::getConfig('App.ShopFai.Es')->indexStatisticCart,
        ]));
    }

    /**
     * 获取总唯一访客 加购物车总数量
     *
     * 相当于 SELECT COUNT(DISTINCT(user_id))
     *
     * @param array $options 参数
     * @return int
     */
    public function getUniqueUserCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Cart:UniqueUserCount',
            'esIndex' => Be::getConfig('App.ShopFai.Es')->indexStatisticCart,

            // 按 user_token 取唯一
            'cardinality' => 'user_token',
        ]));
    }

}
