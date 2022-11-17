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

            $data['name'] = $request->post('name', '');
            $data['summary'] = $request->post('summary', '');
            $data['description'] = $request->post('description', '', 'html');

            $data['spu'] = $request->post('spu', '');

            $styles = [];
            if ($style && is_array($style) && count($style) > 0) {
                foreach ($style as $styleName => $styleValue) {
                    $styleName = trim($styleName);
                    $styleValue = trim($styleValue);
                    if (!$styleName | !$styleValue) {
                        continue;
                    }

                    $styleValues = explode('|', $styleValue);
                    $newStyleValues = [];
                    foreach ($styleValues as $v) {
                        $v = trim($v);
                        if (!$v) {
                            continue;
                        }
                        $newStyleValues[] = $v;
                    }

                    if (count($newStyleValues) > 0) {
                        $styles[] = [
                            'name' => $styleName,
                            'values' => $newStyleValues,
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
                        foreach ($tmpStyle['values'] as $tmpStyleValue) {
                            $styleItems[] = [[
                                'name' => $tmpStyle['name'],
                                'value' => $tmpStyleValue,
                            ]];
                        }
                    } else {
                        $newStyleItems = [];
                        foreach ($styleItems as $styleItem) {
                            foreach ($tmpStyle['values'] as $tmpStyleValue) {
                                $tmpStyleItem = $styleItem;
                                $tmpStyleItem[] = [
                                    'name' => $tmpStyle['name'],
                                    'value' => $tmpStyleValue,
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
                        'details' => [[
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

                    $details = Be::getTable('shop_product_relate_detail')
                        ->where('relate_id', $relateId)
                        ->getArrays();

                    $isProductRelatedDetailExist = false;
                    if ($collectProductExist) {
                        foreach ($details as &$detail) {
                            if ($detail['product_id'] === $tupleCollectProduct->product_id) {
                                $detail['value'] = $relateValue;
                                $detail['icon_image'] = $relateIconImage;
                                $detail['icon_color'] = $relateIconColor;
                                $isProductRelatedDetailExist = true;
                                break;
                            }
                        }
                        unset($detail);
                    }

                    if (!$isProductRelatedDetailExist) {
                        $details[] = [
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
                        'details' => $details,
                    ];
                }
            }

            $categories = $request->post('categories', '');
            if ($categories && is_string($categories)) {
                $categories = explode('|', $categories);
                $categoryIds = [];

                $serviceCategory = Be::getService('App.ShopAdmin.Category');
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


}
