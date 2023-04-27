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
     * @param array $with 跟随返回数据
     * @return object
     * @throws ServiceException|\Be\Runtime\RuntimeException
     */
    public function getProduct(string $productId, array $with = [])
    {
        $key = 'App:Shop:Product:' . $productId;
        if (Be::hasContext($key)) {
            $product = Be::getContext($key);
        } else {
            $cache = Be::getCache();
            $product = $cache->get($key);
            if ($product === false) {
                try {
                    $product = $this->getProductFromDb($productId);
                } catch (\Throwable $t) {
                    $product = '-1';
                }

                $configCache = Be::getConfig('App.Shop.Cache');
                $cache->set($key, $product, $configCache->product);
            }

            Be::setContext($key, $product);
        }

        if ($product === '-1') {
            throw new ServiceException('Product #' . $productId . ' does not exists！');
        }

        $product = $this->formatProduct($product, $with);

        return $product;
    }

    /**
     * 获取文章
     *
     * @param string $productId 商品ID
     * @param array $with 跟随返回数据
     * @return object 文章对象
     * @throws ServiceException
     */
    public function getProductFromDb(string $productId, array $with = []): object
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM `shop_product` WHERE id=?';
        $product = $db->getObject($sql, [$productId]);
        if (!$product) {
            throw new ServiceException('Product #' . $productId . ' does not exists！');
        }

        if ($product->is_enable !== '1' || $product->is_delete !== '0') {
            throw new ServiceException('Product #' . $productId . ' does not exists！');
        }

        if ($product->relate_id !== '') {
            $sql = 'SELECT * FROM shop_product_relate WHERE id = ?';
            $relate = $db->getObject($sql, [$product->relate_id]);

            $sql = 'SELECT * FROM shop_product_relate_item WHERE relate_id = ? ORDER BY ordering ASC';
            $relateItems = $db->getObjects($sql, [$product->relate_id]);
            foreach ($relateItems as &$relateItem) {
                $sql = 'SELECT `name` FROM shop_product WHERE id = ?';
                $relateItem->product_name = $db->getValue($sql, [$relateItem->product_id]);
            }
            unset($relateItem);

            $relate->items = $relateItems;

            $product->relate = $relate;
        }

        $sql = 'SELECT url, is_main FROM shop_product_image WHERE product_id = ? AND product_item_id = \'\' ORDER BY ordering ASC';
        $images = $db->getObjects($sql, [$product->id]);
        foreach ($images as $image) {
            $image->is_main = (int)$image->is_main;
        }
        $product->images = $images;

        $categories = [];
        $sql = 'SELECT category_id FROM shop_product_category WHERE product_id = ?';
        $categoryIds = $db->getValues($sql, [$product->id]);
        if (count($categoryIds) > 0) {
            $sql = 'SELECT id, name FROM shop_category WHERE is_enable=1 AND is_delete=0 AND id IN (\'' . implode('\',\'', $categoryIds) . '\') ORDER BY ordering ASC';
            $categories = $db->getObjects($sql);
        }
        $product->categories = $categories;
        $product->category_ids = array_column($categories, 'id');


        $sql = 'SELECT tag FROM shop_product_tag WHERE product_id = ?';
        $product->tags = $db->getValues($sql, [$product->id]);

        $sql = 'SELECT * FROM shop_product_style WHERE product_id = ?';
        $styles = $db->getObjects($sql, [$product->id]);

        foreach ($styles as &$style) {
            $sql = 'SELECT * FROM shop_product_style_item WHERE product_style_id = ? ORDER BY ordering ASC';
            $styleItems = $db->getObjects($sql, [$style->id]);
            $style->items = $styleItems;
        }
        unset($style);

        $product->styles = $styles;

        $sql = 'SELECT id, sku, barcode, style, style_json, price, original_price, weight, weight_unit, stock FROM shop_product_item WHERE product_id = ? ORDER BY ordering ASC';
        $items = $db->getObjects($sql, [$product->id]);
        foreach ($items as $item) {
            $styleJson = null;
            if ($item->style_json) {
                $styleJson = json_decode($item->style_json, true);
            }
            if (!$styleJson) {
                $styleJson = [];
            }
            $item->style_json = $styleJson;

            $item->stock = (int)$item->stock;

            $sql = 'SELECT url, is_main FROM shop_product_image WHERE product_id = ? AND  product_item_id = ? ORDER BY ordering ASC';
            $itemImages = $db->getObjects($sql, [$product->id, $item->id]);
            foreach ($itemImages as &$itemImage) {
                $itemImage->is_main = (int)$itemImage->is_main;
            }
            unset($itemImage);
            $item->images = $itemImages;
        }
        $product->items = $items;

        $newProduct = new \stdClass();
        $newProduct->id = $product->id;
        $newProduct->spu = $product->spu;
        $newProduct->name = $product->name;
        $newProduct->summary = $product->summary;
        $newProduct->description = $product->description;
        $newProduct->url = $product->url;
        //$newProduct->url_custom = (int)$product->url_custom;
        $newProduct->seo_title = $product->seo_title;
        //$newProduct->seo_title_custom = (int)$product->seo_title_custom;
        $newProduct->seo_description = $product->seo_description;
        //$newProduct->seo_description_custom = (int)$product->seo_description_custom;
        $newProduct->seo_keywords = $product->seo_keywords;
        $newProduct->brand = $product->brand;
        $newProduct->relate_id = $product->relate_id;
        $newProduct->style = (int)$product->style;
        $newProduct->stock_tracking = (int)$product->stock_tracking;
        $newProduct->stock_out_action = (int)$product->stock_out_action;
        $newProduct->publish_time = $product->publish_time;
        //$newProduct->ordering = (int)$product->ordering;
        $newProduct->hits = (int)$product->hits;
        $newProduct->sales_volume = (int)$product->sales_volume_base + (int)$product->sales_volume;
        $newProduct->price_from = $product->price_from;
        $newProduct->price_to = $product->price_to;
        $newProduct->original_price_from = $product->original_price_from;
        $newProduct->original_price_to = $product->original_price_to;
        $newProduct->rating_sum = (int)$product->rating_sum;
        $newProduct->rating_count = (int)$product->rating_count;
        $newProduct->rating_avg = $product->rating_avg;

        if ($newProduct->relate_id !== '') {
            $newProduct->relate = $product->relate;
        }
        $newProduct->images = $product->images;
        $newProduct->categories = $product->categories;
        $newProduct->category_ids = $product->category_ids;
        $newProduct->tags = $product->tags;
        $newProduct->styles = $product->styles;
        $newProduct->items = $product->items;

        return $newProduct;
    }

    /**
     * 从缓存获取多个商品数据
     *
     * @param array $productIds 多个商品ID
     * @param bool $throwException 不存在的文章是否抛出异常
     * @return array
     */
    public function getProducts(array $productIds = [], bool $throwException = true): array
    {
        $configCache = Be::getConfig('App.Shop.Cache');
        $cache = Be::getCache();

        $keys = [];
        foreach ($productIds as $productId) {
            $keys[] = 'App:Shop:Product:' . $productId;
        }

        $products = $cache->getMany($keys);

        $noProducts = true;
        foreach ($products as $product) {
            if ($product) {
                $noProducts = false;
            }
        }

        // 缓存中没有任何商品，全部从数据库中读取并缓存
        if ($noProducts) {

            $newProducts = [];
            foreach ($productIds as $productId) {

                $key = 'App:Shop:Product:' . $productId;
                try {
                    $product = $this->getProductFromDb($productId);
                } catch (\Throwable $t) {
                    $product = '-1';
                }

                $cache->set($key, $product, $configCache->product);

                if ($product === '-1') {
                    if ($throwException) {
                        throw new ServiceException('Product #' . $productId . ' does not exists！');
                    } else {
                        continue;
                    }
                }

                $newProducts[] = $product;
            }

        } else {

            $newProducts = [];
            $i = 0;
            foreach ($products as $product) {
                if ($product === false || $product === '-1') {
                    if ($throwException) {
                        throw new ServiceException('Product #' . $productId . ' does not exists！');
                    } else {
                        continue;
                    }
                }

                $newProducts[] = $product;

                $i++;
            }
        }

        return $newProducts;
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

        $image = Be::getProperty('App.Shop')->getWwwUrl() . '/images/product/no-image.webp';

        $items = [];
        if ($style === 1) {

            $price = number_format(rand(10, 100), 2, '.', '');
            $originalPrice = bcmul($price, '1.2', 2);
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
                        'url' => $image,
                        'is_main' => 1,
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
                $originalPrice = bcmul($price, '1.2', 2);
                $weight = rand(1000, 100000) / 1000;
                $stock = rand(100, 1000);

                $items[] = (object)[
                    'id' => '',
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
            'url' => $image,
            'is_main' => 1,
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
                $key = 'App:Shop:Product:Relate:' . $product->relate_id;
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

        $historyKey = 'App:Shop:Product:history:' . $my->id;
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
        $hitsKey = 'App:Shop:Product:hits:' . $productId;
        $cacheHits = $cache->get($hitsKey);
        if ($cacheHits !== false) {
            if (is_numeric($cacheHits)) {
                $cacheHits = (int)$cacheHits;
                if ($cacheHits > $product->hits) {
                    $hits = $cacheHits;
                }
            }
        }
        $hits++;
        $cache->set($hitsKey, $hits);

        // 每 100 次访问，更新到数据库
        if ($hits % 100 === 0) {
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
        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return $this->searchFromDb($keywords, $params, $with);
        }

        $cache = Be::getCache();
        $es = Be::getEs();

        $keywords = trim($keywords);
        if ($keywords !== '') {
            // 将本用户搜索的关键词写入ES search_history
            $counterKey = 'App:Shop:Product:searchHistory';
            $counter = (int)$cache->get($counterKey);
            $query = [
                'index' => $configEs->indexProductSearchHistory,
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

        $cacheKey = 'App:Shop:Product:search';
        if ($keywords !== '') {
            $cacheKey .= ':' . $keywords;
        }
        $cacheKey .= ':' . md5(serialize($params));

        $results = $cache->get($cacheKey);
        if ($results !== false) {
            return $results;
        }

        $query = [
            'index' => $configEs->indexProduct,
            'body' => []
        ];

        if ($keywords === '') {
            $query['body']['min_score'] = 0;
        } else {
            $query['body']['min_score'] = 0.01;

            if (!isset($query['body']['query'])) {
                $query['body']['query'] = [];
            }

            if (!isset($query['body']['query']['bool'])) {
                $query['body']['query']['bool'] = [];
            }

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
            if (!isset($query['body']['query'])) {
                $query['body']['query'] = [];
            }

            if (!isset($query['body']['query']['bool'])) {
                $query['body']['query']['bool'] = [];
            }

            if (!isset($query['body']['query']['bool']['filter'])) {
                $query['body']['query']['bool']['filter'] = [];
            }

            $query['body']['query']['bool']['filter'][] = ['terms' => ['id' => $params['productIds']]];
        }

        if (isset($params['categoryId']) && $params['categoryId']) {

            if (!isset($query['body']['query'])) {
                $query['body']['query'] = [];
            }

            if (!isset($query['body']['query']['bool'])) {
                $query['body']['query']['bool'] = [];
            }

            if (!isset($query['body']['query']['bool']['filter'])) {
                $query['body']['query']['bool']['filter'] = [];
            }

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
            if (!isset($query['body']['query'])) {
                $query['body']['query'] = [];
            }

            if (!isset($query['body']['query']['bool'])) {
                $query['body']['query']['bool'] = [];
            }

            if (!isset($query['body']['query']['bool']['filter'])) {
                $query['body']['query']['bool']['filter'] = [];
            }

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
            if (!isset($query['body']['query'])) {
                $query['body']['query'] = [];
            }

            if (!isset($query['body']['query']['bool'])) {
                $query['body']['query']['bool'] = [];
            }

            if (!isset($query['body']['query']['bool']['filter'])) {
                $query['body']['query']['bool']['filter'] = [];
            }

            $query['body']['query']['bool']['filter'][] = ['term' => ['brand' => $params['brand']]];
        }

        if (isset($params['priceRange']) && $params['priceRange']) {
            $priceRange = explode('-', $params['priceRange']);
            if (count($priceRange) === 2) {
                if (!isset($query['body']['query'])) {
                    $query['body']['query'] = [];
                }

                if (!isset($query['body']['query']['bool'])) {
                    $query['body']['query']['bool'] = [];
                }

                if (!isset($query['body']['query']['bool']['filter'])) {
                    $query['body']['query']['bool']['filter'] = [];
                }

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
                case 'price':
                    $orderBy = $orderByDir === 'asc' ? 'price_from' : 'price_to';
                    break;

                case 'spu':
                case 'brand':
                case 'ordering':
                case 'sales_volume':
                case 'hits':
                case 'rating_sum':
                case 'rating_count':
                case 'rating_avg':
                case 'publish_time':
                    $orderBy = $params['orderBy'];
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
            $pageSize = 12;
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
            $product = (object)$x['_source'];
            try {
                $product->absolute_url = beUrl('Shop.Product.detail', ['id' => $product->id]);
            } catch (\Throwable $t) {
                continue;
            }

            $rows[] = $this->formatEsProduct($product);
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

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $return, $configCache->products);

        return $return;
    }

    /**
     * 按关銉词搜索 从数据库中搜索
     *
     * @param string $keywords 关銉词
     * @param array $params
     * @param array $with 返回的数据控制
     * @return array
     */
    public function searchFromDb(string $keywords, array $params = [], array $with = []): array
    {
        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:searchFromDb';
        if ($keywords !== '') {
            $cacheKey .= ':' . $keywords;
        }
        $cacheKey .= ':' . md5(serialize($params));

        $results = $cache->get($cacheKey);
        if ($results !== false) {
            return $results;
        }

        $tableProduct = Be::getTable('shop_product');
        $tableProduct->where('is_enable', 1);
        $tableProduct->where('is_delete', 0);

        if ($keywords !== '') {
            $condition = [];
            $condition[] = ['name', 'like', '%' . $keywords . '%'];
            $condition[] = 'OR';
            $condition[] = ['spu', '=', $keywords];

            $tableProductItem = Be::getTable('shop_product_item');
            $tableProductItem->where('sku', $keywords);
            $productIds = $tableProductItem->getValues('product_id');
            if (count($productIds) > 0) {
                $condition[] = 'OR';
                $condition[] = ['id', 'in', $productIds];
            }
            $tableProduct->condition($condition);
        }

        if (isset($params['productIds']) && $params['productIds']) {
            $tableProduct->where('id', 'in', $params['productIds']);
        }

        if (isset($params['categoryId']) && $params['categoryId']) {
            $tableProductCategory = Be::getTable('shop_product_category');
            $tableProductCategory->where('category_id', $params['categoryId']);
            $productIds = $tableProductCategory->getValues('product_id');
            if (count($productIds) > 0) {
                $tableProduct->where('id', 'in', $params['productIds']);
            }
        } elseif (isset($params['categoryIds']) && is_array($params['categoryIds']) && count($params['categoryIds']) > 0) {
            $tableProductCategory = Be::getTable('shop_product_category');
            $tableProductCategory->where('category_id', 'in', $params['categoryIds']);
            $productIds = $tableProductCategory->getValues('product_id');
            if (count($productIds) > 0) {
                $tableProduct->where('id', 'in', $params['productIds']);
            }
        }

        if (isset($params['brand']) && $params['brand']) {
            $tableProduct->where('brand', $params['brand']);
        }

        if (isset($params['priceRange']) && $params['priceRange']) {
            $priceRange = explode('-', $params['priceRange']);
            if (count($priceRange) === 2) {
                $tableProduct->where('price_from', '>=', $priceRange[0]);
                $tableProduct->where('brand', '<=', $priceRange[1]);
            }
        }

        if (isset($params['orderBy']) && $params['orderBy'] && $params['orderBy'] != 'common') {
            $orderByDir = 'desc';
            if (isset($params['orderByDir']) && in_array($params['orderByDir'], ['asc', 'desc'])) {
                $orderByDir = $params['orderByDir'];
            }

            $orderBy = null;
            switch ($params['orderBy']) {
                case 'price':
                    $orderBy = $orderByDir === 'asc' ? 'price_from' : 'price_to';
                    break;

                case 'spu':
                case 'brand':
                case 'ordering':
                case 'sales_volume':
                case 'hits':
                case 'rating_sum':
                case 'rating_count':
                case 'rating_avg':
                case 'publish_time':
                    $orderBy = $params['orderBy'];
                    break;
            }

            if ($orderBy) {
                $tableProduct->orderBy($orderBy, $orderByDir);
            }
        }

        // 分页
        $pageSize = null;
        if (isset($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] > 0) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 12;
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

        $total = $tableProduct->count();

        $minPrice = null;
        $maxPrice = null;
        if (isset($with['priceStep']) && $with['priceStep']) {
            $minPrice = $tableProduct->min('price_from');
            $maxPrice = $tableProduct->min('price_to');
        }

        $tableProduct->limit($pageSize);
        $tableProduct->offset(($page - 1) * $pageSize);

        $productIds = $tableProduct->getValues('id');

        $rows = $this->getProducts($productIds, false);

        $return = [
            'total' => $total,
            'pageSize' => $pageSize,
            'page' => $page,
            'rows' => $rows,
        ];

        if (isset($with['priceStep']) && $with['priceStep'] && is_numeric($with['priceStep'])) {
            $n = (int)$with['priceStep'];
            if ($n > 1) {
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


        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $return, $configCache->products);

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
        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return $this->getSimilarProductsFromDb($productId, $productName, $n);
        }

        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:SimilarProducts:' . $productId . ':' . $n;
        $results = $cache->get($cacheKey);

        if ($results !== false) {
            return $results;
        }

        $query = [
            'index' => $configEs->indexProduct,
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
            $product = (object)$x['_source'];
            try {
                $product->absolute_url = beUrl('Shop.Product.detail', ['id' => $product->id]);
            } catch (\Throwable $t) {
                continue;
            }

            $return[] = $this->formatEsProduct($product);
        }

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $return, $configCache->products);

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
    public function getSimilarProductsFromDb(string $productId, string $productName, int $n = 12): array
    {
        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:SimilarProductsFromDb:' . $productId . ':' . $n;
        $results = $cache->get($cacheKey);
        if ($results !== false) {
            return $results;
        }

        $tableProduct = Be::getTable('shop_product');
        $tableProduct->where('is_enable', 1)
            ->where('is_delete', 0)
            ->where('id', '!=', $productId);

        if ($productName !== '') {
            $tableProduct->where('name', 'like', '%' . $productName . '%');
        }

        $tableProduct->limit($n);

        $productIds = $tableProduct->getValues('id');

        $result = $this->getProducts($productIds, false);

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $result, $configCache->products);

        return $result;
    }

    /**
     * 获取按指定排序的前N个商品
     *
     * @param array $params 查询参数
     * @return array
     */
    public function getTopNProducts(array $params = []): array
    {
        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return $this->getTopNProductsFromDb($params);
        }

        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:TopNProducts:' . md5(serialize($params));
        $results = $cache->get($cacheKey);
        if ($results !== false) {
            return $results;
        }

        $orderBy = $params['orderBy'];

        $orderByDir = 'desc';
        if (isset($params['orderByDir']) && in_array($params['orderByDir'], ['asc', 'desc'])) {
            $orderByDir = $params['orderByDir'];
        }

        // 分页
        if (isset($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] > 0) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 12;
        }

        if ($pageSize > 200) {
            $pageSize = 200;
        }

        $query = [
            'index' => $configEs->indexProduct,
            'body' => [
                'size' => $pageSize,
                'sort' => [
                    $orderBy => [
                        'order' => $orderByDir
                    ]
                ]
            ]
        ];

        if (isset($params['categoryId']) && $params['categoryId'] !== '') {
            $query['body']['query'] = [
                'bool' => [
                    'filter' => [
                        [
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
                            ],
                        ],
                    ],
                ],
            ];
        }

        $es = Be::getEs();
        $results = $es->search($query);

        $return = [];
        if (isset($results['hits']['hits'])) {
            foreach ($results['hits']['hits'] as $x) {
                $product = (object)$x['_source'];
                try {
                    $product->absolute_url = beUrl('Shop.Product.detail', ['id' => $product->id]);
                } catch (\Throwable $t) {
                    continue;
                }

                $return[] = $this->formatEsProduct($product);
            }
        }

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $return, $configCache->products);

        return $return;
    }

    /**
     * 获取按指定排序的前N个商品
     *
     * @param array $params 查询参数
     * @return array
     */
    public function getTopNProductsFromDb(array $params = []): array
    {
        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:TopNProductsFromDb:' . md5(serialize($params));
        $results = $cache->get($cacheKey);
        if ($results !== false) {
            return $results;
        }

        $orderBy = $params['orderBy'];

        $orderByDir = 'desc';
        if (isset($params['orderByDir']) && in_array($params['orderByDir'], ['asc', 'desc'])) {
            $orderByDir = $params['orderByDir'];
        }

        // 分页
        if (isset($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] > 0) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 12;
        }

        if ($pageSize > 200) {
            $pageSize = 200;
        }

        $tableProduct = Be::getTable('shop_product')->where('is_enable', 1)->where('is_delete', 0);

        if (isset($params['categoryId']) && $params['categoryId']) {
            $db = Be::getDb();
            $sql = 'SELECT product_id FROM shop_product_category WHERE category_id = ?';
            $articleIds = $db->getValues($sql, [$params['categoryId']]);
            if (count($articleIds) > 0) {
                $tableProduct->where('id', 'IN', $articleIds);
            } else {
                $tableProduct->where('id', '');
            }
        }

        $productIds = $tableProduct->orderBy($orderBy, $orderByDir)->limit($pageSize)->getValues('id');

        $result = $this->getProducts($productIds, false);

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $result, $configCache->products);

        return $result;
    }

    /**
     * 最新产品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getLatestTopNProducts(int $n = 10): array
    {
        return $this->getTopNProducts([
            'orderBy' => 'publish_time',
            'orderByDir' => 'desc',
            'pageSize' => $n,
        ]);
    }

    /**
     * 热门产品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getHottestTopNProducts(int $n = 10): array
    {
        return $this->getTopNProducts([
            'orderBy' => 'hits',
            'orderByDir' => 'desc',
            'pageSize' => $n,
        ]);
    }

    /**
     * 热销产品
     *
     * @param int $n 结果数量
     * @return array
     */
    public function getTopSalesTopNProducts(int $n = 10): array
    {
        return $this->getTopNProducts([
            'orderBy' => 'sales_volume',
            'orderByDir' => 'desc',
            'pageSize' => $n,
        ]);
    }

    /**
     * 指下究类的最新产品
     *
     * @param string $categoryId 分类ID
     * @param int $n 结果数量
     * @return array
     */
    public function getCategoryLatestTopNProducts(string $categoryId, int $n = 10): array
    {
        return $this->getTopNProducts([
            'categoryId' => $categoryId,
            'orderBy' => 'publish_time',
            'orderByDir' => 'desc',
            'pageSize' => $n,
        ]);
    }

    /**
     * 指下究类的热门产品
     *
     * @param string $categoryId 分类ID
     * @param int $n 结果数量
     * @return array
     */
    public function getCategoryHottestTopNProducts(string $categoryId, int $n = 10): array
    {
        return $this->getTopNProducts([
            'categoryId' => $categoryId,
            'orderBy' => 'hits',
            'orderByDir' => 'desc',
            'pageSize' => $n,
        ]);
    }

    /**
     * 指下究类的热销产品
     *
     * @param string $categoryId 分类ID
     * @param int $n 结果数量
     * @return array
     */
    public function getCategoryTopSalesTopNProducts(string $categoryId, int $n = 10): array
    {
        return $this->getTopNProducts([
            'categoryId' => $categoryId,
            'orderBy' => 'sales_volume',
            'orderByDir' => 'desc',
            'pageSize' => $n,
        ]);
    }

    /**
     * 热搜商品
     *
     * @param array $params 查询参数
     * @return array
     */
    public function getHotSearchProducts(array $params = []): array
    {
        // 分页
        if (isset($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] > 0) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 12;
        }

        if ($pageSize > 200) {
            $pageSize = 200;
        }

        if (isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0) {
            $page = $params['page'];
        } else {
            $page = 1;
        }

        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return [
                'total' => 0,
                'pageSize' => $pageSize,
                'page' => $page,
                'rows' => [],
            ];
        }

        $keywords = $this->getHotSearchKeywords(5);
        if (!$keywords) {
            return [
                'total' => 0,
                'pageSize' => $pageSize,
                'page' => $page,
                'rows' => [],
            ];
        }

        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:HotSearchProducts:' . md5(serialize($params));
        $results = $cache->get($cacheKey);
        if ($results !== false) {
            return $results;
        }


        $query = [
            'index' => $configEs->indexProduct,
            'body' => [
                'size' => $pageSize,
                'from' => ($page - 1) * $pageSize,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'name' => implode(', ', $keywords)
                            ]
                        ],
                    ]
                ]
            ]
        ];

        if (isset($params['categoryId']) && $params['categoryId'] !== '') {
            $query['body']['query']['bool']['filter'] = [
                [
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
                    ],
                ],
            ];
        }

        $es = Be::getEs();
        $results = $es->search($query);

        $total = 0;
        if (isset($results['hits']['total']['value'])) {
            $total = $results['hits']['total']['value'];
        }

        $rows = [];
        foreach ($results['hits']['hits'] as $x) {
            $product = (object)$x['_source'];
            try {
                $product->absolute_url = beUrl('Shop.Product.detail', ['id' => $product->id]);
            } catch (\Throwable $t) {
                continue;
            }

            $rows[] = $this->formatEsProduct($product);
        }

        $return = [
            'total' => $total,
            'pageSize' => $pageSize,
            'page' => $page,
            'rows' => $rows,
        ];

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $return, $configCache->products);

        return $return;
    }

    /**
     * 热搜商品
     *
     * @param int $n Top N 数量
     * @return array
     */
    public function getHotSearchTopNProducts(int $n = 10): array
    {
        $results = $this->getHotSearchProducts([
            'pageSize' => $n,
        ]);

        return $results['rows'];
    }

    /**
     * 指定分类下的热搜商品
     *
     * @param string $categoryId 分类ID
     * @param int $n Top N 数量
     * @return array
     */
    public function getCategoryHotSearchTopNProducts(string $categoryId, int $n = 10): array
    {
        $results = $this->getHotSearchProducts([
            'categoryId' => $categoryId,
            'pageSize' => $n,
        ]);

        return $results['rows'];
    }

    /**
     * 猜你喜欢
     *
     * @param array $params 查询参数
     * @return array
     */
    public function getGuessYouLikeProducts(array $params = []): array
    {
        // 分页
        if (isset($params['pageSize']) && is_numeric($params['pageSize']) && $params['pageSize'] > 0) {
            $pageSize = $params['pageSize'];
        } else {
            $pageSize = 12;
        }

        if ($pageSize > 200) {
            $pageSize = 200;
        }

        if (isset($params['page']) && is_numeric($params['page']) && $params['page'] > 0) {
            $page = $params['page'];
        } else {
            $page = 1;
        }

        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return [
                'total' => 0,
                'pageSize' => $pageSize,
                'page' => $page,
                'rows' => [],
            ];
        }


        $my = Be::getUser();
        $es = Be::getEs();
        $cache = Be::getCache();

        $historyKey = 'App:Shop:Product:history:' . $my->id;
        $history = $cache->get($historyKey);

        $keywords = [];
        if ($history && is_array($history) && count($history) > 0) {
            $keywords = $history;
        }

        if (!$keywords) {
            $keywords = $this->getHotSearchKeywords(10);
        }

        if (!$keywords) {
            return [
                'total' => 0,
                'pageSize' => $pageSize,
                'page' => $page,
                'rows' => [],
            ];
        }

        $query = [
            'index' => $configEs->indexProduct,
            'body' => [
                'size' => $pageSize,
                'from' => ($page - 1) * $pageSize,
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'name' => implode(',', $keywords)
                            ]
                        ],
                    ]
                ]
            ]
        ];

        if (isset($params['excludeProductId']) && $params['excludeProductId'] !== '') {
            $query['body']['query']['bool']['must_not'] = [
                'term' => [
                    '_id' => $params['excludeProductId']
                ]
            ];
        }

        if (isset($params['categoryId']) && $params['categoryId'] !== '') {
            $query['body']['query']['bool']['filter'] = [
                [
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
                    ],
                ],
            ];
        }

        $results = $es->search($query);

        $total = 0;
        if (isset($results['hits']['total']['value'])) {
            $total = $results['hits']['total']['value'];
        }

        $rows = [];
        foreach ($results['hits']['hits'] as $x) {
            $product = (object)$x['_source'];
            try {
                $product->absolute_url = beUrl('Shop.Product.detail', ['id' => $product->id]);
            } catch (\Throwable $t) {
                continue;
            }

            $rows[] = $this->formatEsProduct($product);
        }

        $return = [
            'total' => $total,
            'pageSize' => $pageSize,
            'page' => $page,
            'rows' => $rows,
        ];

        return $return;
    }


    /**
     * 猜你喜欢TopN
     *
     * @param int $n Top N 数量
     * @param string $excludeProductId 要排除的商品ID
     * @return array
     */
    public function getGuessYouLikeTopNProducts(int $n = 40, string $excludeProductId = null): array
    {
        $results = $this->getGuessYouLikeProducts([
            'pageSize' => $n,
            'excludeProductId' => $excludeProductId,
        ]);

        return $results['rows'];
    }

    /**
     * 指定分类下猜你喜欢TopN
     *
     * @param string $categoryId 分类ID
     * @param int $n Top N 数量
     * @param string $excludeProductId 要排除的商品ID
     * @return array
     */
    public function getCategoryGuessYouLikeTopNProducts(string $categoryId, int $n = 40, string $excludeProductId = null): array
    {
        $results = $this->getGuessYouLikeProducts([
            'categoryId' => $categoryId,
            'pageSize' => $n,
            'excludeProductId' => $excludeProductId,
        ]);

        return $results['rows'];
    }


    /**
     * 格式化ES查询出来的商品
     *
     * @param object $product
     * @return object
     */
    private function formatEsProduct(object $product): object
    {
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

        $styles = [];
        if ($product->style === 2) {
            if (is_array($product->styles) && count($product->styles) > 0) {
                foreach ($product->styles as $s) {
                    $obj = (object)$s;
                    $styleItems = [];
                    foreach ($obj->items as $styleItem) {
                        $styleItems[] = (object)$styleItem;
                    }
                    $obj->items = $styleItems;

                    $styles[] = $obj;
                }
            }
        }
        $product->styles = $styles;

        $items = [];
        if (is_array($product->items) && count($product->items) > 0) {
            foreach ($product->items as $item) {
                $obj = (object)$item;
                $images = [];
                foreach ($obj->images as $image) {
                    $images[] = (object)$image;
                }
                $obj->images = $images;
                $items[] = $obj;
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
    public function getHotSearchKeywords(int $n = 6): array
    {
        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return [];
        }

        $cache = Be::getCache();
        $cacheKey = 'App:Shop:Product:HotSearchKeywords';
        $hotSearchKeywords = $cache->get($cacheKey);
        if ($hotSearchKeywords) {
            return $hotSearchKeywords;
        }

        $es = Be::getEs();
        $query = [
            'index' => $configEs->indexProductSearchHistory,
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

        $configCache = Be::getConfig('App.Shop.Cache');
        $cache->set($cacheKey, $hotKeywords, $configCache->hotKeywords);

        return $hotKeywords;
    }


    /**
     * 获取商品伪静态页网址
     *
     * @param array $params
     * @return array
     * @throws ServiceException
     */
    public function getProductUrl(array $params = []): array
    {
        $product = $this->getProduct($params['id']);

        $params1 = ['id' => $params['id']];
        unset($params['id']);

        $config = Be::getConfig('App.Shop.Product');
        if (!$product) {
            return [$config->urlPrefix . 'null' . $config->urlSuffix, $params1, $params];
        }

        return [$config->urlPrefix . $product->url . $config->urlSuffix, $params1, $params];
    }

    /**
     * 获取标签
     *
     * @param int $n
     * @return array
     */
    public function getTopTags(int $n): array
    {
        $cache = Be::getCache();

        $key = 'App:Shop:Product:TopTags:' . $n;
        $tags = $cache->get($key);
        if ($tags === false) {
            try {
                $tags = $this->getTopTagsFromDb($n);
            } catch (\Throwable $t) {
                $tags = [];
            }

            $configCache = Be::getConfig('App.Shop.Cache');
            $cache->set($key, $tags, $configCache->tag);
        }

        return $tags;
    }

    /**
     * 从数据库获取标签
     *
     * @param int $n
     * @return array
     */
    public function getTopTagsFromDb(int $n): array
    {
        $db = Be::getDb();
        $sql = 'SELECT tag FROM (SELECT tag, COUNT(*) AS cnt FROM `shop_product_tag` GROUP BY tag) t ORDER BY cnt DESC LIMIT ' . $n;
        return $db->getValues($sql);
    }

}
