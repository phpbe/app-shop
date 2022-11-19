<?php

namespace Be\App\Shop\Service;

use Be\App\ServiceException;
use Be\Be;
use Be\Util\Crypt\Random;

class Product
{

    /**
     * 从REDIS 获取商品数据
     *
     * @param string $productId 商品ID
     * @return object
     * @throws ServiceException|\Be\Runtime\RuntimeException
     */
    public function getProduct(string $productId, array $with = [])
    {
        $key = 'Shop:Product:' . $productId;
        if (Be::hasContext($key)) {
            $product = Be::getContext($key);
        } else {
            $cache = Be::getCache();
            $product = $cache->get($key);

            if (!$product) {
                //throw new ServiceException('Product #' . $productId . ' does not exists！');
            }

            Be::setContext($key, $product);
        }

        if ($product) {
            $product = $this->formatProduct($product, $with);
        }

        return $product;
    }


    /**
     * 从REDIS 获取多个商品数据
     *
     * @param array $productIds 多个商品ID
     * @return array
     * @throws ServiceException|\Be\Runtime\RuntimeException
     */
    public function getProducts(array $productIds = [], array $with = []): array
    {
        $cache = Be::getCache();

        $keys = [];
        foreach ($productIds as $productId) {
            $keys[] = 'Shop:Product:' . $productId;
        }

        $products = $cache->getMany($keys);

        $decodedProducts = [];
        $i = 0;
        foreach ($products as $product) {
            if (!$product) {
                throw new ServiceException('Product #' . $keys[$i] . ' does not exists！');
            }

            $product = $this->formatProduct($product, $with);

            $decodedProducts[] = $product;

            $i++;
        }

        return $decodedProducts;
    }

    /**
     * 获取一个示例商品
     *
     * @return object
     */
    public function getSampleProduct(): object
    {
        $spu = Random::uppercaseLetters(6);
        $style = rand(1, 2);
        $stockTracking = rand(0, 1);
        $stockOutAction = rand(0, 2) - 1;
        $ordering = rand(0, 99999999);
        $hits = rand(1, 99999999);
        $salesVolume = rand(1, 99999);
        $ratingSum = rand(1, 99999);
        $ratingAvg = (float)(rand(30, 50) / 10);
        $ratingCount = (int)$ratingSum / $ratingAvg;
        $createTime = date('Y-m-d H:i:s');
        $updateTime = $createTime;

        $priceFrom = 0;
        $priceTo = 0;
        $originalPriceFrom = 0;
        $originalPriceTo = 0;

        $image = Be::getProperty('App.Shop')->getWwwUrl() . '/images/product/no-image.jpg';

        $items = [];
        if ($style === 1) {

            $price = number_format(rand(10, 100), 2, '.', '');
            $originalPrice = bcmul($price,  '1.2',  2);
            $weight = rand(100, 1000);
            $stock = rand(100, 1000);

            $items[] = (object)[
                'id' => '',
                'sku' => $spu,
                'barcode' => $spu,
                'style' => '',
                'price' => $price,
                'original_price' => $originalPrice,
                'weight' => $weight,
                'weight_unit' => 'g',
                'stock' => $stock,
                'images' => [
                    (object)[
                        'id' => '',
                        'product_id' => '',
                        'url' => $image,
                        'is_main' => 1,
                        'ordering' => 0,
                        'create_time' => $createTime,
                        'update_time' => $updateTime,
                    ]
                ],
            ];

            $priceFrom = $price;
            $priceTo = $price;
            $originalPriceFrom = $originalPrice;
            $originalPriceTo = $originalPrice;

        } else {
            foreach (['S', 'M', 'L'] as $size) {

                $price = number_format(rand(10, 100), 2, '.', '');
                $originalPrice = bcmul($price,  '1.2',  2);
                $weight = rand(1000, 100000) / 1000;
                $stock = rand(100, 1000);

                $items[] = (object)[
                    'id' => '',
                    'product_id' => '',
                    'sku' => $spu . '-' . $size,
                    'barcode' => $spu,
                    'style' => $size,
                    'style_json' => '[{"name":"Size","value":"' . $size . '"}]',
                    'price' => $price,
                    'original_price' => $originalPrice,
                    'weight' => $weight,
                    'weight_unit' => 'g',
                    'stock' => $stock,
                    'images' => [],
                ];

                if ($priceFrom === 0 || $price < $priceFrom) {
                    $priceFrom = $price;
                }

                if ($priceTo === 0 || $price > $priceTo) {
                    $priceTo = $price;
                }

                if ($originalPriceFrom === 0 || $originalPrice < $originalPriceFrom) {
                    $originalPriceFrom = $originalPrice;
                }

                if ($originalPriceTo === 0 || $originalPrice > $originalPriceTo) {
                    $originalPriceTo = $originalPrice;
                }
            }
        }

        $images = [];
        $images[] = (object)[
            'id' => '',
            'product_id' => '',
            'url' => $image,
            'is_main' => 1,
            'ordering' => 0,
            'create_time' => $createTime,
            'update_time' => $updateTime,
        ];

        return (object)[
            'id' => '',
            'spu' => $spu,
            'name' => 'Product Name',
            'summary' => 'Product Summary',
            'url' => '',
            'categories' => [],
            'brand' => 'Brand',
            'tags' => [],
            'style' => $style,
            'stock_tracking' => $stockTracking,
            'stock_out_action' => $stockOutAction,
            'ordering' => $ordering,
            'hits' => $hits,
            'sales_volume' => $salesVolume,
            'price_from' => $priceFrom,
            'price_to' => $priceTo,
            'original_price_from' => $originalPriceFrom,
            'original_price_to' => $originalPriceTo,
            'rating_sum' => $ratingSum,
            'rating_count' => $ratingCount,
            'rating_avg' => $ratingAvg,
            'is_enable' => 1,
            'is_delete' => 0,
            'create_time' => $createTime,
            'update_time' => $updateTime,
            'items' => $items,
            'images' => $images,
        ];
    }



    /**
     * 获取多个示例商品
     *
     * @param int $n 数量
     * @return array
     */
    public function getSampleProducts($n = 4): array
    {
        $products = [];
        for ($i = 0; $i < $n; $i++) {
            $products[] = $this->getSampleProduct();
        }
        return $products;
    }

    /**
     * 格式化商品
     *
     * @param array $product
     * @param array $with
     * @return object
     * @throws \Be\Runtime\RuntimeException
     */
    public function formatProduct(object $product, array $with = []): object
    {
        if (isset($with['relate']) && $with['relate']) {
            $product->relate = [];
            if ($product->relate_id !== '') {
                $key = 'Shop:ProductRelate:' . $product->relate_id;
                $cache = Be::getCache();
                $productRelate = $cache->get($key);
                if ($productRelate) {
                    foreach ($productRelate->items as &$relateItem) {
                        $relateItem->url = beUrl('Shop.Product.detail', ['id' => $relateItem->product_id]);
                        if ($relateItem->product_id === $product->id) {
                            $relateItem->self = 1;
                            $productRelate->value = $relateItem->value;
                        } else {
                            $relateItem->self = 0;
                        }
                    }
                    unset($relateItem);

                    $product->relate = $productRelate;
                }
            }
        }

        if (isset($with['style']) && $with['style']) {
            if ($product->style === 2) {
                if (isset($product->styles) && is_array($product->styles) && count($product->styles) > 0) {
                    foreach ($product->styles as &$style) {
                        $style->values = json_decode($style->values);
                    }
                    unset($style);
                }

                if (isset($product->items) && is_array($product->items) && count($product->items) > 0) {
                    foreach ($product->items as &$item) {
                        $item->style_json = json_decode($item->style_json);
                    }
                    unset($item);
                }
            }
        }

        return $product;
    }

    /**
     * 查看商品明细
     *
     * 从商品标题中提取关銉词，存入ES 用作 "猜你喜欢"
     *
     * @param string $userId 用户ID
     * @param string $productId 商品ID
     * @return object
     */
    public function hit(string $productId): object
    {
        $my = Be::getUser();
        $cache = Be::getCache();

        $product = $this->getProduct($productId, [
            'relate' => 1,
            'style' => 1,
        ]);

        $historyKey = 'Shop:ProductHistory:' . $my->id;
        $history = $cache->get($historyKey);

        if (!$history || !is_array($history)) {
            $history = [];
        }

        $history[] = $product->name;

        if (count($history) > 10) {
            $history = array_slice($history, -10);
        }

        // 最近浏览的商品名称存入 redis，有效期 30 天
        $cache->set($historyKey, $history, 86400 * 30);

        // 点击量 使用REDIS 存放
        $hits = $product->hits;
        $n = 0;
        $hitsKey = 'Shop:Product:hits:' . $productId;
        $cacheHits = $cache->get($hitsKey);
        if ($cacheHits !== false) {
            $cacheHitsArr = explode(',', $cacheHits);
            if (count($cacheHitsArr) == 2) {
                $hits = (int)$cacheHitsArr[0];
                $n = (int)$cacheHitsArr[1];
            }
        }
        $hits++;
        $n++;
        $cache->set($hitsKey, $hits . ',' . ($n >= 1000 ? 0 : $n));

        // 每 1000 次访问，更新到数据库
        if ($n >= 1000) {
            $sql = 'UPDATE shop_product SET hits=?, update_time=? WHERE id=?';
            Be::getDb()->query($sql, [$hits, date('Y-m-d H:i:s'), $productId]);
        }

        $product->hits = $hits;

        return $product;
    }

    /**
     * 按关銉词搜索
     *
     * @param string $keywords 关銉词
     * @param array $params
     * @param array $with 返回的数据控制
     * @return array
     */
    public function search(string $keywords, array $params = [], array $with = []): array
    {
        $config = Be::getConfig('App.Shop.Es');
        $cache = Be::getCache();
        $es = Be::getEs();

        $keywords = trim($keywords);
        if ($keywords !== '') {
            // 将本用户搜索的关键词写入ES search_history
            $counterKey = 'Shop:ProductSearchHistory';
            $counter = (int)$cache->get($counterKey);
            $query = [
                'index' => $config->indexProductSearchHistory,
                'id' => $counter,
                'body' => [
                    'keyword' => $keywords,
                ]
            ];
            $es->index($query);

            // 累计写入1千个
            $counter++;
            if ($counter >= 1000) {
                $counter = 0;
            }

            $cache->set($counterKey, $counter);
        }

        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];

        if ($keywords === '') {
            $query['body']['min_score'] = 0;
        } else {
            $query['body']['min_score'] = 0.01;
            $query['body']['query']['bool']['should'] = [
                [
                    'match' => [
                        'name' => $keywords
                    ],
                ],
                [
                    'nested' => [
                        'path' => 'items',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'term' => [
                                            'items.sku' => $keywords,
                                        ],
                                    ],
                                ]
                            ],
                        ],
                    ],
                ],
            ];
        }

        if (isset($params['productIds']) && $params['productIds']) {
            $query['body']['query']['bool']['filter'][] = ['terms' => ['id' => $params['productIds']]];
        }

        if (isset($params['categoryId']) && $params['categoryId']) {
            $query['body']['query']['bool']['filter'][] = [
                'nested' => [
                    'path' => 'categories',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                [
                                    'term' => [
                                        'categories.id' => $params['categoryId'],
                                    ],
                                ],
                            ]
                        ],
                    ],
                ]
            ];
        } elseif (isset($params['categoryIds']) && is_array($params['categoryIds']) && count($params['categoryIds']) > 0) {
            $query['body']['query']['bool']['filter'][] = [
                'nested' => [
                    'path' => 'categories',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                [
                                    'terms' => [
                                        'categories.id' => $params['categoryIds'],
                                    ],
                                ],
                            ]
                        ],
                    ],
                ]
            ];
        }

        if (isset($params['brand']) && $params['brand']) {
            $query['body']['query']['bool']['filter'][] = ['term' => ['brand' => $params['brand']]];
        }

        if (isset($params['priceRange']) && $params['priceRange']) {
            $priceRange = explode('-', $params['priceRange']);
            if (count($priceRange) === 2) {
                $query['body']['query']['bool']['filter'][] = [
                    'range' => [
                        'price_from' => [
                            'gte' => $priceRange[0],
                        ]
                    ]
                ];

                $query['body']['query']['bool']['filter'][] = [
                    'range' => [
                        'price_to' => [
                            'lte' => $priceRange[1],
                        ]
                    ]
                ];
            }
        }

        if (isset($params['orderBy']) && $params['orderBy'] && $params['orderBy'] != 'common') {
            $orderByDir = 'desc';
            if (isset($params['orderByDir']) && in_array($params['orderByDir'], ['asc', 'desc'])) {
                $orderByDir = $params['orderByDir'];
            }

            $orderBy = null;
            switch ($params['orderBy']) {
                case 'sales_volume':
                    $orderBy = 'sales_volume';
                    break;
                case 'hits':
                    $orderBy = 'hits';
                    break;
                case 'price':
                    $orderBy = $orderByDir === 'asc' ? 'price_from' : 'price_to';
                    break;
                case 'create_time':
                    $orderBy = 'create_time';
                    break;
                case 'update_time':
                    $orderBy = 'update_time';
                    break;
            }

            if ($orderBy) {
                $query['body']['sort'] = [];
                $query['body']['sort'][] = [
                    $orderBy => [
                        'order' => $orderByDir
                    ]
                ];
            }
        }

        // 分页
        $pageSize = null;
        if (isset($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] > 0) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 15;
        }

        if ($pageSize > 200) {
            $pageSize = 200;
        }

        $page = null;
        if (isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0) {
            $page = $params['page'];
        } else {
            $page = 1;
        }

        $query['body']['size'] = $pageSize;
        $query['body']['from'] = ($page - 1) * $pageSize;

        if (isset($with['priceStep']) && $with['priceStep']) {
            $query['body']['aggs'] = [
                'min_price' => [
                    'min' => ['field' => 'price_from']
                ],
                'max_price' => [
                    'max' => ['field' => 'price_to']
                ]
            ];
        }

        $results = $es->search($query);

        $total = 0;
        if (isset($results['hits']['total']['value'])) {
            $total = $results['hits']['total']['value'];
        }

        $rows = [];
        foreach ($results['hits']['hits'] as $x) {
            $rows[] = $this->formatEsProduct($x['_source']);
        }

        $return = [
            'total' => $total,
            'pageSize' => $pageSize,
            'page' => $page,
            'rows' => $rows,
        ];

        if (isset($with['priceStep']) && $with['priceStep'] && is_numeric($with['priceStep'])) {
            $n = (int)$with['priceStep'];
            if ($n > 1) {
                $minPrice = $results['aggregations']['min_price']['value'];
                $maxPrice = $results['aggregations']['max_price']['value'];
                $min = $minPrice;
                if ($minPrice < 100) {
                    $min = 0;
                }

                $priceSteps = [];
                $steps = $maxPrice - $min;
                if ($steps > 100 && $total > $n) {
                    $step = ceil($steps / $n);
                    for ($i = 0; $i <= $maxPrice; $i += $step) {
                        $r1 = $i;
                        $r2 = $i + $step;
                        if ($r2 > $maxPrice) {
                            $r2 = $maxPrice;
                        }

                        $priceSteps[] = $r1 . '-' . $r2;
                    }
                }
                $return['priceSteps'] = $priceSteps;
            }
        }

        return $return;
    }

    /**
     * 跟据商品名称，获取相似商品
     *
     * @param string $productId 商品ID
     * @param string $productName 商品名称
     * @param int $n
     * @return array
     */
    public function getSimilarProducts(string $productId, string $productName, int $n = 12): array
    {
        $config = Be::getConfig('App.Shop.Es');
        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'size' => $n,
                'query' => [
                    'bool' => [
                        'must_not' => [
                            'term' => [
                                '_id' => $productId
                            ]
                        ],
                        'must' => [
                            'match' => [
                                'name' => $productName
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $es = Be::getEs();
        $results = $es->search($query);

        if (!isset($results['hits']['hits'])) {
            return [];
        }

        $return = [];
        foreach ($results['hits']['hits'] as $x) {
            $return[] = $this->formatEsProduct($x['_source']);
        }

        return $return;
    }

    /**
     * 获取按指定排序的前N个商品
     *
     * @param int $n
     * @param string $orderBy
     * @param string $orderByDir
     * @return array
     * @throws \Be\Runtime\RuntimeException
     */
    public function getTopProducts(int $n, string $orderBy, string $orderByDir = 'desc'): array
    {
        $config = Be::getConfig('App.Shop.Es');
        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'size' => $n,
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                        ]
                    ]
                ],
                'sort' => [
                    $orderBy => [
                        'order' => $orderByDir
                    ]
                ]
            ]
        ];

        $es = Be::getEs();
        $results = $es->search($query);

        if (!isset($results['hits']['hits'])) {
            return [];
        }

        $return = [];
        foreach ($results['hits']['hits'] as $x) {
            $return[] = $this->formatEsProduct($x['_source']);
        }

        return $return;
    }

    /**
     * 最新产品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getLatestProducts(int $n = 10): array
    {
        return $this->getTopProducts($n, 'create_time', 'desc');
    }

    /**
     * 热门产品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getHottestProducts(int $n = 10): array
    {
        return $this->getTopProducts($n, 'hits', 'desc');
    }

    /**
     * 热销产品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getTopSalesProducts(int $n = 10): array
    {
        return $this->getTopProducts($n, 'sales_volume', 'desc');
    }

    /**
     * 热搜商品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getTopSearchProducts(int $n = 10): array
    {
        $config = Be::getConfig('App.Shop.Es');

        $keywords = $this->getTopSearchKeywords(5);
        if (!$keywords) {
            return [];
        }

        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'size' => $n,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'name' => implode(', ', $keywords)
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $es = Be::getEs();
        $results = $es->search($query);

        if (!isset($results['hits']['hits'])) {
            return [];
        }

        $return = [];
        foreach ($results['hits']['hits'] as $x) {
            $return[] = $this->formatEsProduct($x['_source']);
        }

        return $return;
    }

    /**
     * 猜你喜欢
     *
     * @param string $userId 用户ID
     * @param int $n 结果数量
     * @param string $excludeProductId 排除拽定的商品
     * @return array
     */
    public function getGuessYouLikeProducts(int $n = 40, string $excludeProductId = null): array
    {
        $my = Be::getUser();
        $config = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();
        $cache = Be::getCache();

        $historyKey = 'Shop:ProductHistory:' . $my->id;
        $history = $cache->get($historyKey);

        $keywords = [];
        if ($history && is_array($history) && count($history) > 0) {
            $keywords = $history;
        }

        if (!$keywords) {
            $keywords = $this->getTopSearchKeywords(10);
        }

        if (!$keywords) {
            return [];
        }

        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'size' => $n,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'name' => implode(',', $keywords)
                            ]
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        ];

        if ($excludeProductId !== null) {
            $query['body']['query']['bool']['must_not'] = [
                'term' => [
                    '_id' => $excludeProductId
                ]
            ];
        }

        $results = $es->search($query);

        if (!isset($results['hits']['hits'])) {
            return [];
        }

        $return = [];
        foreach ($results['hits']['hits'] as $x) {
            $return[] = $this->formatEsProduct($x['_source']);
        }

        return $return;
    }

    /**
     * 指定分类下的热门商品
     *
     * @param string $categoryId 分类ID
     * @param int $n 结果数量
     * @return array
     */
    public function getCategoryTopSearchProducts(string $categoryId, int $n = 10): array
    {
        $subCategoryIds = Be::getService('App.Shop.Category')->getSubCategoryIds($categoryId);
        if (!$subCategoryIds) return [];

        $keywords = $this->getTopSearchKeywords(10);
        if (!$keywords) {
            return [];
        }

        $config = Be::getConfig('App.Shop.Es');
        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'size' => $n,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'name' => implode(',', $keywords)
                            ],
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                            [
                                'nested' => [
                                    'path' => 'categories',
                                    'query' => [
                                        'bool' => [
                                            'filter' => [
                                                [
                                                    'term' => [
                                                        'categories.id' => $categoryId,
                                                    ],
                                                ],
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        ];

        $es = Be::getEs();
        $results = $es->search($query);

        if (!isset($results['hits']['hits'])) {
            return [];
        }

        $return = [];
        foreach ($results['hits']['hits'] as $x) {
            $return[] = $this->formatEsProduct($x['_source']);
        }

        return $return;
    }

    /**
     * 指定分类下的猜你喜欢
     *
     * @param string $categoryId 分类ID
     * @param string $userId 用户ID
     * @param int $n 结果数量
     * @param string $excludeProductId 排除拽定的商品
     * @return array
     */
    public function getCategoryGuessYouLikeProducts(string $categoryId, int $n = 40, string $excludeProductId = null): array
    {
        $my = Be::getUser();
        $config = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();
        $cache = Be::getCache();

        $historyKey = 'Shop:ProductHistory:' . $my->id;
        $history = $cache->get($historyKey);

        $keywords = [];
        if ($history && is_array($history) && count($history) > 0) {
            $keywords = $history;
        }

        if (!$keywords) {
            $keywords = $this->getTopSearchKeywords(10);
        }

        if (!$keywords) {
            return [];
        }

        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'size' => $n,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'name' => implode(' ', $keywords)
                            ],
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'is_enable' => true,
                                ],
                            ],
                            [
                                'term' => [
                                    'is_delete' => false,
                                ],
                            ],
                            [
                                'nested' => [
                                    'path' => 'categories',
                                    'query' => [
                                        'bool' => [
                                            'filter' => [
                                                [
                                                    'term' => [
                                                        'categories.id' => $categoryId,
                                                    ],
                                                ],
                                            ]
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        ];

        if ($excludeProductId !== null) {
            $query['body']['query']['bool']['must_not'] = [
                'term' => [
                    '_id' => $excludeProductId
                ]
            ];
        }

        $results = $es->search($query);

        if (!isset($results['hits']['hits'])) {
            return [];
        }

        $return = [];
        foreach ($results['hits']['hits'] as $x) {
            $return[] = $this->formatEsProduct($x['_source']);
        }

        return $return;
    }

    /**
     * 格式化ES查询出来的商品
     *
     * @param array $rows
     * @return object
     */
    private function formatEsProduct(array $row): object
    {
        $product = (object)$row;

        $categories = [];
        if (is_array($product->categories) && count($product->categories) > 0) {
            foreach ($product->categories as $category) {
                $categories[] = (object)$category;
            }
        }
        $product->categories = $categories;

        $images = [];
        if (is_array($product->images) && count($product->images) > 0) {
            foreach ($product->images as $image) {
                $images[] = (object)$image;
            }
        }
        $product->images = $images;

        $items = [];
        if (is_array($product->items) && count($product->items) > 0) {
            foreach ($product->items as $item) {
                $items[] = (object)$item;
            }
        }
        $product->items = $items;

        return $product;
    }

    /**
     * 从搜索历史出提取热门搜索词
     *
     * @param int $n
     * @return array
     */
    public function getTopSearchKeywords(int $n = 6): array
    {
        $config = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();
        $query = [
            'index' => $config->indexProductSearchHistory,
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'filter' => [

                        ]
                    ]
                ],
                'aggs' => [
                    'topN' => [
                        'terms' => [
                            'field' => 'keyword',
                            'size' => $n
                        ]
                    ]
                ]
            ]
        ];

        $result = $es->search($query);

        $hotKeywords = [];
        if (isset($result['aggregations']['topN']['buckets']) &&
            is_array($result['aggregations']['topN']['buckets']) &&
            count($result['aggregations']['topN']['buckets']) > 0
        ) {
            foreach ($result['aggregations']['topN']['buckets'] as $v) {
                $hotKeywords[] = $v['key'];
            }
        }
        return $hotKeywords;
    }


    /**
     * 获取商品伪静态页网址
     *
     * @param array $params
     * @return string
     * @throws ServiceException
     */
    public function getProductUrl(array $params = []): string
    {
        $config = Be::getConfig('App.Shop.Product');

        $product = $this->getProduct($params['id']);
        if (!$product) {
            return '/' . $config->urlPrefix . '/null';
        }

        return '/' . $config->urlPrefix . '/' . $product->url . $config->urlSuffix;
    }

}
