<?php

namespace Be\App\Shop\Service\Admin;


use Be\Be;

class StatisticSales extends Statistic
{

    /**
     * 获取 订单 时间分布 报表
     *
     * 相当于 SELECT COUNT(*) FROM order GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Sales:Report',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,
        ]));
    }

    /**
     * 获取 已付款订单 时间分布 报表
     *
     * 相当于 SELECT COUNT(*) FROM order WHERE [paid] GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getPaidReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Sales:PaidReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

        ]));
    }

    /**
     * 获取 已付款订单 金额 时间分布 报表
     *
     * 相当于 SELECT COUNT(*) FROM order WHERE [paid] GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getPaidSumReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Sales:PaidSumReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,


            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

            'sum' => 'amount'
        ]));
    }

    /**
     * 获取 已付款订单 平均客单价 时间分布 报表
     *
     * 相当于 SELECT COUNT(*) FROM order WHERE [paid] GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getPaidAvgReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Sales:PaidAvgReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,


            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

            'sum' => 'amount'
        ]));
    }

    /**
     * 获取 有下单的 唯一访客 时间分布 报表
     *
     * 相当于 SELECT COUNT(DISTINCT(user_token)) FROM order GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getUniqueUserReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Sales:UniqueUserReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            // 按 user_token 取唯一
            'cardinality' => 'user_token',
        ]));
    }


    /**
     * 获取 有下单并付款的 唯一访客 时间分布 报表
     *
     * 相当于 SELECT COUNT(DISTINCT(user_token)) FROM order WHERE [paid] GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getPaidUniqueUserReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Sales:PaidUniqueUserReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,


            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

            // 按 user_token 取唯一
            'cardinality' => 'user_token',
        ]));
    }

    /**
     * 获取 总订单数
     *
     * 相当于 SELECT COUNT(*) FROM order
     *
     * @param array $options 参数
     * @return int
     */
    public function getCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Sales:Count',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,
        ]));
    }

    /**
     * 获取 已付款的 总订单数
     *
     * 相当于 SELECT COUNT(*) FROM order WHERE [paid]
     *
     * @param array $options 参数
     * @return int
     */
    public function getPaidCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Sales:PaidCount',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

        ]));
    }

    /**
     * 获取 已付款的 但未发货的 总订单数
     *
     * 相当于 SELECT COUNT(*) FROM order WHERE [paid]
     *
     * @param array $options 参数
     * @return int
     */
    public function getPaidNotShippedCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Sales:PaidCount',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'term' => [
                                'is_shipped' => 0,
                            ],
                        ],
                    ],
                ]
            ],

        ]));
    }

    /**
     * 获取 下单的 唯一访客数
     *
     * 相当于 SELECT COUNT(DISTINCT(user_token)) FROM order
     *
     * @param array $options 参数
     * @return int
     */
    public function getUniqueUserCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Sales:UniqueUserCount',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            // 按 user_token 取唯一
            'cardinality' => 'user_token',
        ]));
    }

    /**
     * 获取 下单并付款的 唯一访客数
     *
     * 相当于 SELECT COUNT(DISTINCT(user_token)) FROM order WHERE [paid]
     *
     * @param array $options 参数
     * @return int
     */
    public function getUniqueUserPaidCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Sales:UniqueUserPaidCount',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

            // 按 user_token 取唯一
            'cardinality' => 'user_token',
        ]));
    }

    /**
     * 获取 所有订单的 销售额
     *
     * 相当于 SELECT SUM(amount) FROM order
     *
     * @param array $options 参数
     * @return int
     */
    public function getSum(array $options = []): string
    {
        $sum = $this->_getSum(array_merge($options, [
            'cacheKey' => 'Sales:Sum',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            // amount 求和
            'sum' => 'amount',
        ]));

        return number_format($sum, 2, '.', '');
    }

    /**
     * 获取 已付款订单的 销售额
     *
     * 相当于 SELECT SUM(amount) FROM order WHERE [paid]
     *
     * @param array $options 参数
     * @return int
     */
    public function getPaidSum(array $options = []): string
    {
        $sum = $this->_getSum(array_merge($options, [
            'cacheKey' => 'Sales:PaidSum',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,


            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

            // amount 求和
            'sum' => 'amount',
        ]));

        return number_format($sum, 2, '.', '');
    }

    /**
     * 获取 已付款订单的 平均客单价
     *
     * 相当于 SELECT AVG(amount) FROM order WHERE [paid]
     *
     * @param array $options 参数
     * @return int
     */
    public function getPaidAvg(array $options = []): string
    {
        $avg = $this->_getAvg(array_merge($options, [
            'cacheKey' => 'Sales:PaidAvg',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexOrder,

            'query' => [
                'bool' => [
                    'filter' => [
                        [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'is_cod' => 1,
                                        ],
                                    ],
                                    [
                                        'term' => [
                                            'is_paid' => 1,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],

            'avg' => 'amount',
        ]));

        return number_format($avg, 2, '.', '');
    }

}
