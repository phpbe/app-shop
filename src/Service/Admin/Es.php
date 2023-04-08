<?php

namespace Be\App\Shop\Service\Admin;

use Be\App\ServiceException;
use Be\Be;

class Es
{

    public function getIndexes()
    {
        $configEs = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();

        $indexes = [];
        foreach ([
                     [
                         'name' => 'product',
                         'label' => '商品索引',
                         'value' => $configEs->indexProduct,
                     ],
                     [
                         'name' => 'productHistory',
                         'label' => '商品访问记录索引',
                         'value' => $configEs->indexProductHistory,
                     ],
                     [
                         'name' => 'productSearchHistory',
                         'label' => '商品搜索记录索引',
                         'value' => $configEs->indexProductSearchHistory,
                     ],
                     [
                         'name' => 'order',
                         'label' => '订单统计',
                         'value' => $configEs->indexOrder,
                     ],
                     [
                         'name' => 'statisticVisit',
                         'label' => '访客统计',
                         'value' => $configEs->indexStatisticVisit,
                     ],
                     [
                         'name' => 'statisticCart',
                         'label' => '购物车统计',
                         'value' => $configEs->indexStatisticCart,
                     ],
                 ] as $index) {

            $params = [
                'index' => $index['value'],
            ];
            if ($es->indices()->exists($params)) {
                $index['exists'] = true;

                $mapping = $es->indices()->getMapping($params);
                $index['mapping'] = $mapping[$index['value']]['mappings'] ?? [];

                $settings = $es->indices()->getSettings($params);
                $index['settings'] = $settings[$index['value']]['settings'] ?? [];

                $count = $es->count($params);
                $index['count'] = $count['count'] ?? 0;
            } else {
                $index['exists'] = false;
            }
            $indexes[] = $index;
        }

        return $indexes;
    }

    /**
     * 创建索引
     *
     * @param string $indexName 索引名
     * @param array $options 参数
     * @return void
     */
    public function createIndex(string $indexName, array $options = [])
    {
        $number_of_shards = $options['number_of_shards'] ?? 2;
        $number_of_replicas = $options['number_of_replicas'] ?? 1;

        $configEs = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();

        $configField = 'index' . ucfirst($indexName);

        $params = [
            'index' => $configEs->$configField,
        ];

        if ($es->indices()->exists($params)) {
            throw new ServiceException('索引（' . $configEs->$configField . '）已存在');
        }

        switch ($indexName) {
            case 'product':
                $mapping = [
                    'properties' => [
                        'id' => [
                            'type' => 'keyword'
                        ],
                        'spu' => [
                            'type' => 'keyword'
                        ],
                        'name' => [
                            'type' => 'text'
                        ],
                        'summary' => [
                            'type' => 'text'
                        ],
                        'url' => [
                            'type' => 'keyword'
                        ],
                        'categories' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'keyword'
                                ],
                                'name' => [
                                    'type' => 'keyword'
                                ]
                            ]
                        ],
                        'brand' => [
                            'type' => 'keyword'
                        ],
                        'tags' => [
                            'type' => 'keyword'
                        ],
                        'style' => [
                            'type' => 'integer'
                        ],
                        'styles' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'keyword'
                                ],
                                'name' => [
                                    'type' => 'keyword'
                                ],
                                'icon_type' => [
                                    'type' => 'keyword'
                                ],
                                'ordering' => [
                                    'type' => 'integer'
                                ],
                                'items' => [
                                    'type' => 'nested',
                                    'properties' => [
                                        'id' => [
                                            'type' => 'keyword'
                                        ],
                                        'value' => [
                                            'type' => 'keyword'
                                        ],
                                        'icon_image' => [
                                            'type' => 'keyword'
                                        ],
                                        'icon_color' => [
                                            'type' => 'keyword'
                                        ],
                                        'ordering' => [
                                            'type' => 'integer'
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'stock_tracking' => [
                            'type' => 'integer'
                        ],
                        'stock_out_action' => [
                            'type' => 'integer'
                        ],
                        'ordering' => [
                            'type' => 'integer'
                        ],
                        'hits' => [
                            'type' => 'integer'
                        ],
                        'sales_volume' => [
                            'type' => 'integer'
                        ],
                        'price_from' => [
                            'type' => 'float'
                        ],
                        'price_to' => [
                            'type' => 'float'
                        ],
                        'original_price_from' => [
                            'type' => 'float'
                        ],
                        'original_price_to' => [
                            'type' => 'float'
                        ],
                        'rating_sum' => [
                            'type' => 'integer'
                        ],
                        'rating_count' => [
                            'type' => 'integer'
                        ],
                        'rating_avg' => [
                            'type' => 'float'
                        ],
                        'is_enable' => [
                            'type' => 'boolean'
                        ],
                        'is_delete' => [
                            'type' => 'boolean'
                        ],
                        'create_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'update_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'images' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'keyword'
                                ],
                                'url' => [
                                    'type' => 'text'
                                ],
                                'is_main' => [
                                    'type' => 'boolean'
                                ],
                                'ordering' => [
                                    'type' => 'integer'
                                ]
                            ]
                        ],
                        'items' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'keyword'
                                ],
                                'sku' => [
                                    'type' => 'keyword'
                                ],
                                'barcode' => [
                                    'type' => 'keyword'
                                ],
                                'style' => [
                                    'type' => 'keyword'
                                ],
                                'style_json' => [
                                    'type' => 'nested',
                                    'properties' => [
                                        'name' => [
                                            'type' => 'keyword'
                                        ],
                                        'value' => [
                                            'type' => 'keyword'
                                        ],
                                    ]
                                ],
                                'price' => [
                                    'type' => 'float'
                                ],
                                'original_price' => [
                                    'type' => 'float'
                                ],
                                'weight' => [
                                    'type' => 'float'
                                ],
                                'weight_unit' => [
                                    'type' => 'keyword'
                                ],
                                'stock' => [
                                    'type' => 'integer'
                                ],
                                'image' => [
                                    'type' => 'keyword'
                                ],
                                /*
                                'images' => [
                                    'type' => 'nested',
                                    'properties' => [
                                        'url' => [
                                            'type' => 'text'
                                        ],
                                        'is_main' => [
                                            'type' => 'boolean'
                                        ],
                                        'ordering' => [
                                            'type' => 'integer'
                                        ]
                                    ]
                                ],
                                */
                            ]
                        ]
                    ]
                ];
                break;

            case 'productHistory':
                $mapping = [
                    'properties' => [
                        'keyword' => [
                            'type' => 'keyword',
                        ],
                    ]
                ];
                break;
            case 'productSearchHistory':
                $mapping = [
                    'properties' => [
                        'keyword' => [
                            'type' => 'keyword',
                        ],
                    ]
                ];
                break;
            case 'order':
                $mapping = [
                    'properties' => [
                        'id' => [
                            'type' => 'keyword'
                        ],
                        'order_sn' => [
                            'type' => 'keyword'
                        ],
                        'user_id' => [
                            'type' => 'keyword'
                        ],
                        'user_token' => [
                            'type' => 'keyword'
                        ],
                        'email' => [
                            'type' => 'keyword'
                        ],
                        'product_amount' => [
                            'type' => 'float'
                        ],
                        'discount_amount' => [
                            'type' => 'float'
                        ],
                        'shipping_fee' => [
                            'type' => 'float'
                        ],
                        'amount' => [
                            'type' => 'float'
                        ],
                        'shipping_plan_id' => [
                            'type' => 'keyword'
                        ],
                        'payment_id' => [
                            'type' => 'keyword'
                        ],
                        'payment_item_id' => [
                            'type' => 'keyword'
                        ],
                        'is_cod' => [
                            'type' => 'integer'
                        ],
                        'is_paid' => [
                            'type' => 'integer'
                        ],
                        'is_shipped' => [
                            'type' => 'integer'
                        ],
                        'status' => [
                            'type' => 'keyword'
                        ],
                        'pay_expire_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'pay_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'cancel_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'ship_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'receive_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'is_delete' => [
                            'type' => 'integer'
                        ],
                        'create_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'update_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ],
                        'products' => [
                            'type' => 'nested',
                            'properties' => [
                                'id' => [
                                    'type' => 'keyword'
                                ],
                                'product_id' => [
                                    'type' => 'keyword'
                                ],
                                'product_item_id' => [
                                    'type' => 'keyword'
                                ],
                                'spu' => [
                                    'type' => 'keyword'
                                ],
                                'name' => [
                                    'type' => 'keyword'
                                ],
                                'sku' => [
                                    'type' => 'keyword'
                                ],
                                'style' => [
                                    'type' => 'keyword'
                                ],
                                'weight' => [
                                    'type' => 'float'
                                ],
                                'weight_unit' => [
                                    'type' => 'keyword'
                                ],
                                'quantity' => [
                                    'type' => 'integer'
                                ],
                                'price' => [
                                    'type' => 'float'
                                ],
                                'amount' => [
                                    'type' => 'float'
                                ]
                            ]
                        ]
                    ]
                ];
                break;
            case 'statisticVisit':
                $mapping = [
                    'properties' => [
                        'user_token' => [
                            'type' => 'keyword'
                        ],
                        'user_id' => [
                            'type' => 'keyword'
                        ],
                        'is_guest' => [
                            'type' => 'boolean'
                        ],
                        'product_id' => [
                            'type' => 'keyword'
                        ],
                        'ip' => [
                            'type' => 'keyword'
                        ],
                        'country_code' => [
                            'type' => 'keyword'
                        ],
                        'is_mobile' => [
                            'type' => 'boolean'
                        ],
                        'url' => [
                            'type' => 'keyword'
                        ],
                        'referer' => [
                            'type' => 'keyword'
                        ],
                        'browser' => [
                            'type' => 'keyword'
                        ],
                        'browser_with_version' => [
                            'type' => 'keyword'
                        ],
                        'os' => [
                            'type' => 'keyword'
                        ],
                        'os_with_version' => [
                            'type' => 'keyword'
                        ],
                        'create_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ]
                    ]
                ];
                break;
            case 'statisticCart':
                $mapping = [
                    'properties' => [
                        'user_token' => [
                            'type' => 'keyword'
                        ],
                        'user_id' => [
                            'type' => 'keyword'
                        ],
                        'product_id' => [
                            'type' => 'keyword'
                        ],
                        'product_item_id' => [
                            'type' => 'keyword'
                        ],
                        'create_time' => [
                            'type' => 'date',
                            'format' => "yyyy-MM-dd HH:mm:ss"
                        ]
                    ]
                ];
                break;
        }

        $params = [
            'index' => $configEs->$configField,
            'body' => [
                'settings' => [
                    'number_of_shards' => $number_of_shards,
                    'number_of_replicas' => $number_of_replicas
                ],
                'mappings' => $mapping,
            ]
        ];

        $es->indices()->create($params);
    }

    /**
     * 删除索引
     *
     * @param string $indexName 索引名
     * @return void
     */
    public function deleteIndex(string $indexName)
    {
        $configEs = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();

        $configField = 'index' . ucfirst($indexName);

        $params = [
            'index' => $configEs->$configField,
        ];

        if ($es->indices()->exists($params)) {
            $es->indices()->delete($params);
        }
    }

}
