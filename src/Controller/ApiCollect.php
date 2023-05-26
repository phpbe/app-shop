<?php

namespace Be\App\Shop\Controller;

use Be\App\ControllerException;
use Be\App\ServiceException;
use Be\Be;

/**
 * 采集接口
 */
class ApiCollect
{

    /**
     * 商品采集接口
     *
     * @BeRoute("/api/collect/product")
     */
    public function product()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $serviceCollectProductApi = Be::getService('App.Shop.Admin.CollectProductApi');
            $collectProductApiConfig = $serviceCollectProductApi->getConfig();

            if ($collectProductApiConfig->enable === 0) {
                throw new ControllerException('商品采集接口未启用！');
            }

            $token = $request->get('token', '');
            if ($collectProductApiConfig->token !== $token) {
                throw new ControllerException('Token 无效！');
            }

            $uniqueKey = $request->post('unique_key', '');
            if ($collectProductApiConfig->uniqueKeyRequired && $uniqueKey === '') {
                throw new ServiceException('唯一值（unique_key）为必填项！');
            }

            $data = [];

            $tupleCollectProduct = Be::getTuple('shop_collect_product');

            $collectProductExist = false;
            if ($uniqueKey !== '') {
                if (strlen($uniqueKey) > 200) {
                    throw new ServiceException('唯一值（unique_key）不得超过200个字符！');
                }

                try {
                    $tupleCollectProduct->loadBy([
                        'unique_key' => $uniqueKey,
                    ]);

                    $collectProductExist = true;
                } catch (\Throwable $t) {
                }

                if ($collectProductExist) {
                    $tupleProduct = Be::getTuple('shop_product');
                    try {
                        $tupleProduct->load($tupleCollectProduct->product_id);

                        $data['id'] = $tupleCollectProduct->product_id;
                    } catch (\Throwable $t) {
                        throw new ServiceException('唯一键值（unique_key=' . $uniqueKey . '）对应的商品异常！');
                    }

                    if ($tupleProduct->is_enable !== -1) {
                        throw new ServiceException('唯一键值（unique_key=' . $uniqueKey . '）对应的商品已导入过！');
                    }
                }
            }

            $relateKey = $request->post('relate_key', '');
            if (strlen($relateKey) > 120) {
                throw new ServiceException('商品关联唯一值（relate_key）不得超过120个字符！');
            }

            $relateName = $request->post('relate_name', '');
            if (strlen($relateName) > 120) {
                throw new ServiceException('商品关联名称（relate_name）不得超过120个字符！');
            }

            $relateValue = $request->post('relate_value', '');
            if (strlen($relateValue) > 120) {
                throw new ServiceException('商品关联唯一值（relate_value）不得超过120个字符！');
            }

            $style = $request->post('style', []);

            $now = date('Y-m-d H:i:s');
            $tupleCollectProduct->update_time = $now;
            if ($collectProductExist) {
                if ($relateName && $relateValue && $relateKey && $tupleCollectProduct->relate_key !== $relateKey) {
                    $tupleCollectProduct->relate_key = $relateKey;
                    $tupleCollectProduct->relate_id = '';
                }
                $tupleCollectProduct->update();
            } else {
                $tupleCollectProduct->unique_key = $uniqueKey;
                $tupleCollectProduct->product_id = '';
                if ($relateName && $relateValue && $relateKey) {
                    $tupleCollectProduct->relate_key = $relateKey;
                } else {
                    $tupleCollectProduct->relate_key = '';
                }
                $tupleCollectProduct->relate_id = '';
                $tupleCollectProduct->create_time = $now;
                $tupleCollectProduct->insert();
            }
            $data['collect_product_id'] = $tupleCollectProduct->id;

            $data['name'] = $request->post('name', '', '');
            if ($collectProductApiConfig->nameRequired && $data['name'] === '') {
                throw new ServiceException('名称（name）为必填项！');
            }

            $data['summary'] = $request->post('summary', '');
            if ($collectProductApiConfig->summaryRequired && $data['summary'] === '') {
                throw new ServiceException('摘要（summary）为必填项！');
            }

            $data['description'] = $request->post('description', '', '');
            if ($collectProductApiConfig->descriptionRequired && $data['description'] === '') {
                throw new ServiceException('描述（description）为必填项！');
            }

            $data['spu'] = $request->post('spu', '');
            if ($collectProductApiConfig->spuRequired && $data['spu'] === '') {
                throw new ServiceException('SPU（spu）为必填项！');
            }

            $data['brand'] = $request->post('brand', '');
            if ($collectProductApiConfig->brandRequired && !$data['brand']) {
                throw new ServiceException('品牌（brand）为必填项！');
            }

            if (is_array($data['brand'])) {
                $data['brand'] = implode('', $data['brand']);
            }

            $price = $request->post('price', '');
            if ($collectProductApiConfig->priceRequired && $price === '') {
                throw new ServiceException('单价（price）为必填项！');
            }

            $data['stock_tracking'] = $request->post('stock_tracking', 0, 'int');
            $stock = $request->post('stock', false);
            if ($stock !== false && is_numeric($stock)) {
                // 有传库存参数，开启库存跟踪
                $data['stock_tracking'] = 1;
            }
            if (!in_array($data['stock_tracking'], [0, 1])) {
                $data['stock_tracking'] = 0;
            }


            $styles = [];
            if ($style && is_array($style) && count($style) > 0) {
                foreach ($style as $styleName => $styleValue) {
                    $styleName = trim($styleName);
                    $styleValue = trim($styleValue);
                    if (!$styleName || !$styleValue) {
                        continue;
                    }

                    $styleValues = explode('|', $styleValue);

                    $tmpItems = [];
                    foreach ($styleValues as $v) {
                        $v = trim($v);
                        if (!$v) {
                            continue;
                        }

                        $tmpItems[] = [
                            'value' => $v
                        ];
                    }

                    if (count($tmpItems) > 0) {
                        $styles[] = [
                            'name' => $styleName,
                            'icon_type' => 'text',
                            'items' => $tmpItems,
                        ];
                    }
                }
            }

            if (count($styles) > 0) {
                $data['style'] = 2; // 多款式
                $data['styles'] = $styles;

                $styleItems = [];
                foreach ($styles as $tmpStyle) {
                    $styleItemsCount = count($styleItems);
                    if ($styleItemsCount === 0) {
                        foreach ($tmpStyle['items'] as $tmpItem) {
                            $styleItems[] = [[
                                'name' => $tmpStyle['name'],
                                'value' => $tmpItem['value'],
                            ]];
                        }
                    } else {
                        $newStyleItems = [];
                        foreach ($styleItems as $styleItem) {
                            foreach ($tmpStyle['items'] as $tmpItem) {
                                $tmpStyleItem = $styleItem;
                                $tmpStyleItem[] = [
                                    'name' => $tmpStyle['name'],
                                    'value' => $tmpItem['value'],
                                ];

                                $newStyleItems[] = $tmpStyleItem;
                            }
                        }
                        $styleItems = $newStyleItems;
                    }
                }

                $items = [];
                foreach ($styleItems as $styleItem) {
                    $styleStrings = [];
                    foreach ($styleItem as $x) {
                        $styleStrings[] = $x['value'];
                    }

                    $items[] = [
                        'id' => '',
                        'sku' => $request->post('sku', ''),
                        'barcode' => $request->post('barcode', ''),
                        'style' => implode(' ', $styleStrings),
                        'style_json' => $styleItem,
                        'price' => $request->post('price', '0.00'),
                        'original_price' => $request->post('original_price', '0.00'),
                        'weight' => $request->post('weight', '0.000'),
                        'weight_unit' => $request->post('weight_unit', 'kg'),
                        'stock' => $request->post('stock', 0, 'int'),
                    ];
                }
                $data['items'] = $items;
            } else {
                $data['style'] = 1; // 单一款式
                $data['items'] = [
                    [
                        'id' => '',
                        'sku' => $request->post('sku', ''),
                        'barcode' => $request->post('barcode', ''),
                        'style' => '',
                        'style_json' => '',
                        'price' => $request->post('price', '0.00'),
                        'original_price' => $request->post('original_price', '0.00'),
                        'weight' => $request->post('weight', '0.000'),
                        'weight_unit' => $request->post('weight_unit', 'kg'),
                        'stock' => $request->post('stock', 0, 'int'),
                    ]
                ];
            }

            $images = $request->post('images', '');
            if ($collectProductApiConfig->imagesRequired && $images === '') {
                throw new ServiceException('主图（images）为必填项！');
            }

            if ($images) {
                $images = explode('|', $images);
                $imagesData = [];
                foreach ($images as $image) {
                    $imagesData[] = [
                        'id' => '',
                        'url' => $image,
                    ];
                }
                $data['images'] = $imagesData;
            } else {
                $data['images'] = [];
            }


            $videos = $request->post('videos', '');
            if ($collectProductApiConfig->videosRequired && $videos === '') {
                throw new ServiceException('视频（videos）为必填项！');
            }

            if ($videos) {
                $videos = explode('|', $videos);
                $videosData = [];
                foreach ($videos as $video) {
                    $videosData[] = [
                        'id' => '',
                        'url' => $video,
                        'preview_url' => '',
                    ];
                }
                $data['videos'] = $videosData;
            } else {
                $data['videos'] = [];
            }

            $relateIconImage = $request->post('relate_icon_image', '');
            $relateIconColor = $request->post('relate_icon_color', '');
            if ($relateName && $relateValue) {
                $relateId = '';
                if ($relateKey) {
                    $existRelateId = Be::getTable('shop_collect_product')
                        ->where('relate_key', $relateKey)
                        ->where('relate_id', '!=', '')
                        ->getValue('relate_id');
                    if ($existRelateId) {
                        $relateId = $existRelateId;
                    }
                }

                $data['related'] = 1;
                if ($relateId === '') {

                    $iconType = 'text';
                    if ($relateIconImage) {
                        $iconType = 'image';
                    } elseif ($relateIconColor) {
                        $iconType = 'color';
                    }

                    $data['relate'] = [
                        'id' => '',
                        'name' => $relateName,
                        'icon_type' => $iconType,
                        'items' => [[
                            'id' => '',
                            'product_id' => '',
                            'value' => $relateValue,
                            'icon_image' => $relateIconImage,
                            'icon_color' => $relateIconColor,
                        ]]
                    ];
                } else {
                    $tProductRelate = Be::getTuple('shop_product_relate');
                    try {
                        $tProductRelate->load($relateId);
                    } catch (\Throwable $t) {
                        throw new ServiceException('商品关联唯一值（relate_key=' . $relateKey . '）对应的历史商品关联（#' . $relateId . '）不存在！');
                    }

                    $relateItems = Be::getTable('shop_product_relate_item')
                        ->where('relate_id', $relateId)
                        ->getArrays();

                    $isProductRelatedDetailExist = false;
                    if ($collectProductExist) {
                        foreach ($relateItems as &$relateItem) {
                            if ($relateItem['product_id'] === $tupleCollectProduct->product_id) {
                                $relateItem['value'] = $relateValue;
                                $relateItem['icon_image'] = $relateIconImage;
                                $relateItem['icon_color'] = $relateIconColor;
                                $isProductRelatedDetailExist = true;
                                break;
                            }
                        }
                        unset($relateItem);
                    }

                    if (!$isProductRelatedDetailExist) {
                        $relateItems[] = [
                            'id' => '',
                            'product_id' => $collectProductExist ? $tupleCollectProduct->product_id : '',
                            'value' => $relateValue,
                            'icon_image' => $relateIconImage,
                            'icon_color' => $relateIconColor,
                        ];
                    }

                    $data['relate'] = [
                        'id' => $relateId,
                        'name' => $tProductRelate->name,
                        'icon_type' => $tProductRelate->icon_type,
                        'items' => $relateItems,
                    ];
                }
            }

            $categories = $request->post('categories', '');
            if ($categories && is_string($categories)) {
                $categories = explode('|', $categories);
                $categoryIds = [];

                $serviceCategory = Be::getService('App.Shop.Admin.Category');
                foreach ($categories as $categoryName) {
                    $categoryName = trim($categoryName);
                    if (!$categoryName || strlen($categoryName) > 120) {
                        continue;
                    }

                    $tupleCategory = Be::getTuple('shop_category');
                    try {
                        $tupleCategory->loadBy([
                            'name' => $categoryName,
                            'is_delete' => 0
                        ]);
                    } catch (\Throwable $t) {
                    }

                    if (!$tupleCategory->isLoaded()) {
                        // 创建分类
                        $tupleCategory = $serviceCategory->edit([
                            'name' => $categoryName
                        ]);
                    }

                    $categoryIds[] = $tupleCategory->id;
                }
                $data['category_ids'] = $categoryIds;
            } else {
                $data['category_ids'] = [];
            }

            $tags = $request->post('tags', '');
            if ($tags && is_string($tags)) {
                $tags = explode('|', $tags);
                $newTags = [];
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    if (!$tag || strlen($tag) > 60) {
                        continue;
                    }
                    $newTags[] = $tag;
                }
                $data['tags'] = $newTags;
            } else {
                $data['tags'] = [];
            }

            $data['is_enable'] = -1; // 采集的商品标记

            $tProduct = Be::getService('App.Shop.Admin.Product')->edit($data);

            if (!$collectProductExist || $tProduct->relate_id !== '') {
                $tupleCollectProduct->product_id = $tProduct->id;
                $tupleCollectProduct->relate_id = $tProduct->relate_id;
                $tupleCollectProduct->update_time = date('Y-m-d H:i:s');
                $tupleCollectProduct->update();
            }

            $db->commit();

            $response->end('[OK] 导入成功！');
        } catch (\Throwable $t) {
            $db->rollback();

            $response->end('[ERROR] ' . $t->getMessage());
        }
    }


    /**
     * 商品评论接口
     *
     * @BeRoute("/api/collect/product/review")
     */
    public function productReview()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $serviceCollectProductApi = Be::getService('App.Shop.Admin.CollectProductApi');
            $collectProductApiConfig = $serviceCollectProductApi->getConfig();

            if ($collectProductApiConfig->enable === 0) {
                throw new ControllerException('商品采集接口未启用！');
            }

            $token = $request->get('token', '');
            if ($collectProductApiConfig->token !== $token) {
                throw new ControllerException('Token 无效！');
            }

            $data['spu'] = $request->post('spu', '');
            if ($data['spu'] === '') {
                throw new ServiceException('SPU（spu）为必填项！');
            }

            $tupleProduct = Be::getTuple('shop_product');
            try {
                $tupleProduct->loadBy('spu', $data['spu']);
            } catch (\Throwable $t) {
                throw new ServiceException('SPU（' . $data['spu'] . '）对应的商品不存在！');
            }

            $data['name'] = $request->post('name', '');
            if ($data['name'] === '') {
                throw new ServiceException('名称（name）为必填项！');
            }

            $data['content'] = $request->post('content', '', '');
            if ($data['content'] === '') {
                throw new ServiceException('评论内容（content）为必填项！');
            }

            $tupleProductReview = Be::getTuple('shop_product_review');
            $tupleProductReview->product_id = $tupleProduct->id;
            $tupleProductReview->style = $request->post('style', '');
            $tupleProductReview->user_id = '';
            $tupleProductReview->name = $data['name'];
            $tupleProductReview->content = $data['content'];
            $tupleProductReview->rating = $request->post('rating', '');
            $tupleProductReview->publish_time = date('Y-m-d H:i:s');
            $tupleProductReview->is_enable = 1;
            $tupleProductReview->is_delete = 0;
            $tupleProductReview->create_time = date('Y-m-d H:i:s');
            $tupleProductReview->update_time = date('Y-m-d H:i:s');
            $tupleProductReview->insert();

            $response->end('[OK] 导入成功！');
        } catch (\Throwable $t) {

            $response->end('[ERROR] ' . $t->getMessage());
        }
    }

}
