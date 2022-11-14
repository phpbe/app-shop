<?php

namespace Be\App\Shop\Service\Admin;

use Be\App\ServiceException;
use Be\Be;

class Statistic
{

    /**
     * 获取时间分组统计
     *
     * @param array $options 参数
     * @return array
     */
    protected function _getDateHistogram(array $options = []): array
    {
        $dateRange = $this->_getDateRange($options);
        $startTime = $dateRange[0];
        $endTime = $dateRange[1];
        $cacheExpire = $dateRange[2];

        $cacheKey = $options['cacheKey'] ?? '';
        if (!$cacheKey) {
            throw new ServiceException('cacheKey 缺失！');
        }

        $cacheKey = 'Shop:Statistic:DateHistogram:' . ($options['cacheKey'] ?? '') . ':' . $startTime . '-' . $endTime;

        $cache = Be::getCache();
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $interval = 'day';
        $timeRangeLength = strtotime($endTime) - strtotime($startTime);
        if ($timeRangeLength <= 86400) {
            $interval = 'hour';
        }

        $query = [
            'index' => $options['esIndex'],
            'body' => [
                'size' => 0,
                'query' => $this->_prepareEsQuery($options, $startTime, $endTime),
                'aggs' => [
                    'report' => [
                        'date_histogram' => [
                            'field' => 'create_time',
                            'interval' => $interval,
                            'min_doc_count' => 0,
                            'extended_bounds' => [
                                'min' => $startTime,
                                'max' => $endTime,
                            ]
                        ],
                    ]
                ]
            ]
        ];

        // 二次聚合计算
        if (isset($options['cardinality'])) {
            // 无 cardinality 时相当于 SELECT COUNT(*) GROUP BY create_time
            // 有 cardinality 时相当于 SELECT COUNT(DISTINCT(field)) GROUP BY create_time
            $query['body']['aggs']['report']['aggs'] = [
                'handle' => [
                    'cardinality' => [
                        'field' => $options['cardinality']
                    ]
                ]
            ];
        } else if (isset($options['sum'])) {
            $query['body']['aggs']['report']['aggs'] = [
                'handle' => [
                    'sum' => [
                        'field' => $options['sum']
                    ]
                ]
            ];
        } else if (isset($options['avg'])) {
            $query['body']['aggs']['report']['aggs'] = [
                'handle' => [
                    'avg' => [
                        'field' => $options['avg']
                    ]
                ]
            ];
        }

        $report = [];
        $es = Be::getEs();
        $results = $es->search($query);
        if (isset($results['aggregations']['report']['buckets'])) {
            $maxHour = 0;
            foreach ($results['aggregations']['report']['buckets'] as $item) {
                if ($interval === 'hour') {
                    $key = gmdate('H:i', $item['key'] / 1000);
                } else {
                    $key = gmdate('m-d', $item['key'] / 1000);
                }

                // 二次聚合计算
                if (
                    isset($options['cardinality']) ||
                    isset($options['sum']) ||
                    isset($options['avg'])
                ) {
                    $val = $item['handle']['value'];
                } else {
                    $val = $item['doc_count'];
                }

                $report[] = [$key, $val];

                if ($interval === 'hour') {
                    $hour = (int)gmdate('H', $item['key'] / 1000);
                    if ($maxHour < $hour) {
                        $maxHour = $hour;
                    }
                }
            }

            if ($interval === 'hour') {
                $maxHour++;
                if ($maxHour < 23) {
                    for ($i = $maxHour; $i < 24; $i++) {
                        $report[] = [($i < 10 ? '0' : '') . $i . ':00'];
                    }
                }
            }
        }

        $cache->set($cacheKey, $report, $cacheExpire);
        return $report;
    }

    /**
     * 获取分组统计
     *
     * _getGroup 相当于 GROUP BY 指定字段
     * _getDateHistogram， 相当于 GROUP BY 时间字段: create_time
     *
     * @param array $options 参数
     * @return array
     */
    protected function _getGroup(array $options = []): array
    {
        $dateRange = $this->_getDateRange($options);
        $startTime = $dateRange[0];
        $endTime = $dateRange[1];
        $cacheExpire = $dateRange[2];

        $cacheKey = $options['cacheKey'] ?? '';
        if (!$cacheKey) {
            throw new ServiceException('cacheKey 缺失！');
        }

        // 相当于 GROUP BY 的字段
        $group = $options['group'] ?? '';
        if (!$group) {
            throw new ServiceException('group 缺失！');
        }

        $cacheKey = 'Shop:Statistic:Group:' . ($options['cacheKey'] ?? '') . ':' . $startTime . '-' . $endTime;

        $cache = Be::getCache();
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $query = [
            'index' => $options['esIndex'],
            'body' => [
                'size' => 0,
                'query' => $this->_prepareEsQuery($options, $startTime, $endTime),
                'aggs' => [
                    'report' => [
                        'terms' => [
                            'field' => $group,
                            'order' => [
                                '_count' => 'DESC',
                            ],
                        ],
                    ]
                ]
            ]
        ];

        if (isset($options['top']) && is_numeric($options['top']) && $options['top'] > 0) {
            $query['body']['aggs']['report']['terms']['size'] = $options['top'];
        }

        // 二次聚合计算
        if (isset($options['cardinality'])) {
            // 无 cardinality 时相当于 SELECT COUNT(*) GROUP BY field
            // 有 cardinality 时相当于 SELECT COUNT(DISTINCT(field)) GROUP BY field
            $query['body']['aggs']['report']['aggs'] = [
                'handle' => [
                    'cardinality' => [
                        'field' => $options['cardinality']
                    ]
                ]
            ];
        } else if (isset($options['sum'])) {
            $query['body']['aggs']['report']['aggs'] = [
                'handle' => [
                    'sum' => [
                        'field' => $options['sum']
                    ]
                ]
            ];
        } else if (isset($options['avg'])) {
            $query['body']['aggs']['report']['aggs'] = [
                'handle' => [
                    'avg' => [
                        'field' => $options['avg']
                    ]
                ]
            ];
        }

        $keyValues = null;
        if (isset($options['keyValues'])) {
            $keyValues = $options['keyValues'];
        }

        $report = [];
        $es = Be::getEs();
        $results = $es->search($query);
        if (isset($results['aggregations']['report']['buckets'])) {
            foreach ($results['aggregations']['report']['buckets'] as $item) {
                $key = $item['key'];
                if ($keyValues !== null && isset($keyValues[$key])) {
                    $key = $keyValues[$key];
                }

                // 二次聚合计算
                if (
                    isset($options['cardinality']) ||
                    isset($options['sum']) ||
                    isset($options['avg'])
                ) {
                    $val = $item['handle']['value'];
                } else {
                    $val = $item['doc_count'];
                }

                $report[] = [$key, $val];
            }
        }

        $cache->set($cacheKey, $report, $cacheExpire);
        return $report;
    }

    /**
     * 获取总数
     *
     * @param array $options 参数
     * @return int
     */
    protected function _getCount(array $options = []): int
    {
        $dateRange = $this->_getDateRange($options);
        $startTime = $dateRange[0];
        $endTime = $dateRange[1];
        $cacheExpire = $dateRange[2];

        $cacheKey = $options['cacheKey'] ?? '';
        if (!$cacheKey) {
            throw new ServiceException('cacheKey 缺失！');
        }

        $cacheKey = 'Shop:Statistic:Count:' . ($options['cacheKey'] ?? '') . ':' . $startTime . '-' . $endTime;

        $cache = Be::getCache();
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $query = [
            'index' => $options['esIndex'],
            'body' => [
                'query' => $this->_prepareEsQuery($options, $startTime, $endTime),
            ]
        ];

        $count = 0;
        $es = Be::getEs();
        if (isset($options['cardinality'])) {
            // 相当于 SELECT COUNT(DISTINCT(field))
            $query['body']['aggs'] = [
                'count' => [
                    'cardinality' => [
                        'field' => $options['cardinality']
                    ]
                ]
            ];

            $results = $es->search($query);
            if (isset($results['aggregations']['count']['value'])) {
                $count = $results['aggregations']['count']['value'];
            }
        } else {
            $results = $es->count($query);
            if (isset($results['count'])) {
                $count = $results['count'];
            }
        }

        $cache->set($cacheKey, $count, $cacheExpire);
        return $count;
    }

    /**
     * 获取总计
     *
     * @param array $options 参数
     * @return int
     */
    protected function _getSum(array $options = []): int
    {
        $dateRange = $this->_getDateRange($options);
        $startTime = $dateRange[0];
        $endTime = $dateRange[1];
        $cacheExpire = $dateRange[2];

        $cacheKey = $options['cacheKey'] ?? '';
        if (!$cacheKey) {
            throw new ServiceException('cacheKey 缺失！');
        }

        $cacheKey = 'Shop:Statistic:Sum:' . ($options['cacheKey'] ?? '') . ':' . $startTime . '-' . $endTime;

        $cache = Be::getCache();
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $query = [
            'index' => $options['esIndex'],
            'body' => [
                'query' => $this->_prepareEsQuery($options, $startTime, $endTime),
                'aggs' => [
                    'handle' => [
                        'sum' => [
                            'field' => $options['sum']
                        ]
                    ]
                ],
            ],
        ];

        $es = Be::getEs();
        $results = $es->search($query);

        $sum = 0;
        if (isset($results['aggregations']['handle']['value'])) {
            $sum = $results['aggregations']['handle']['value'];
        }

        $cache->set($cacheKey, $sum, $cacheExpire);
        return $sum;
    }

    /**
     * 获取平均
     *
     * @param array $options 参数
     * @return int
     */
    protected function _getAvg(array $options = []): int
    {
        $dateRange = $this->_getDateRange($options);
        $startTime = $dateRange[0];
        $endTime = $dateRange[1];
        $cacheExpire = $dateRange[2];

        $cacheKey = $options['cacheKey'] ?? '';
        if (!$cacheKey) {
            throw new ServiceException('cacheKey 缺失！');
        }

        $cacheKey = 'Shop:Statistic:Avg:' . ($options['cacheKey'] ?? '') . ':' . $startTime . '-' . $endTime;

        $cache = Be::getCache();
        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        $query = [
            'index' => $options['esIndex'],
            'body' => [
                'query' => $this->_prepareEsQuery($options, $startTime, $endTime),
                'aggs' => [
                    'handle' => [
                        'avg' => [
                            'field' => $options['avg']
                        ]
                    ]
                ],
            ],
        ];

        $es = Be::getEs();
        $results = $es->search($query);

        $sum = 0;
        if (isset($results['aggregations']['handle']['value'])) {
            $sum = $results['aggregations']['handle']['value'];
        }

        $cache->set($cacheKey, $sum, $cacheExpire);
        return $sum;
    }

    protected function _prepareEsQuery(array $options = [], $startTime = null, $endTime = null): array
    {
        $query = [
            'bool' => [
                'filter' => [
                    [
                        'range' => [
                            'create_time' => [
                                'gte' => $startTime,
                                'lte' => $endTime,
                            ],
                        ],
                    ],
                ]
            ]
        ];

        if (
            isset($options['query'])&&
            is_array($options['query']) &&
            count($options['query']) > 0
        ) {
            if (
                isset($options['query']['bool']['filter']) &&
                is_array($options['query']['bool']['filter']) &&
                count($options['query']['bool']['filter']) > 0
            ) {
                $query['bool']['filter'] = array_merge($query['bool']['filter'], $options['query']['bool']['filter']);
                unset($options['query']['bool']['filter']);
            }

            if (
                isset($options['query']['bool']) &&
                is_array($options['query']['bool']) &&
                count($options['query']['bool']) > 0
            ) {
                $query['bool'] = array_merge($query['bool'], $options['query']['bool']);
            }

            unset($options['query']['bool']);

            if (
                isset($options['query']) &&
                is_array($options['query']) &&
                count($options['query']) > 0
            ) {
                $query = array_merge($query, $options['query']);
                unset($options['query']);
            }
        }

        return $query;
    }

    /**
     * 获取时间范围
     *
     * @param array $options 参数
     */
    protected function _getDateRange(array $options = []): array
    {
        $dateRangeType = $options['dateRangeType'] ?? '';
        if (!in_array($dateRangeType, ['today', 'yesterday', 'last_7_days', 'last_30_days', 'custom'])) {
            throw new ServiceException('无法识别的日期范围类型：' . $dateRangeType);
        }

        $t = time();
        $now = date('Y-m-d H:i:s', $t);
        $today = date('Y-m-d', $t);
        $todayBeginning = date('Y-m-d 00:00:00', $t);
        $todayBeginningTimestamp = strtotime($todayBeginning);
        $todayEnding = date('Y-m-d 23:59:59', $t);
        $todayEndingTimestamp = strtotime($todayEnding);
        $yesterdayEnding = date('Y-m-d H:i:s', $todayBeginningTimestamp - 1);

        $startTime = null;
        $endTime = null;
        switch ($dateRangeType) {
            case 'today':
                $startTime = $todayBeginning;
                $endTime = $now;
                break;
            case 'yesterday':
                $startTime = date('Y-m-d H:i:s', $todayBeginningTimestamp - 86400);
                $endTime = $yesterdayEnding;
                break;
            case 'last_7_days':
                $startTime = date('Y-m-d H:i:s', $todayBeginningTimestamp - 86400 * 7);
                $endTime = $yesterdayEnding;
                break;
            case 'last_30_days':
                $startTime = date('Y-m-d H:i:s', $todayBeginningTimestamp - 86400 * 30);
                $endTime = $yesterdayEnding;
                break;
            case 'custom':
                $startDate = $options['startDate'] ?? '';
                $endDate = $options['endDate'] ?? '';
                if (!$startDate || !$endDate) {
                    throw new ServiceException('请指定日期范围');
                }

                $t1 = strtotime($startDate);
                $t2 = strtotime($endDate);
                $startTime = date('Y-m-d 00:00:00', $t1);
                $endTime = date('Y-m-d 23:59:59', $t2);
                $t1 = strtotime($startTime);
                $t2 = strtotime($endTime);

                if ($t1 > $t2) {
                    throw new ServiceException('指定日期范围无效');
                }

                if ($t1 > $todayBeginningTimestamp) {
                    throw new ServiceException('指定日期范围不可超过今天');
                }

                if ($t2 > $t) {
                    $endTime = $now;
                }
                break;
        }

        $cacheExpire = 86400;
        if (strtotime($endTime) > $todayBeginningTimestamp) {
            $cacheExpire = 600;
        }

        return [$startTime, $endTime, $cacheExpire];
    }
}
