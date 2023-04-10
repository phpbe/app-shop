<?php

namespace Be\App\Shop\Service\Admin;

use Be\App\ServiceException;
use Be\Be;
use Be\Util\File\FileSize;
use Be\Util\File\Mime;
use Be\Util\Net\Curl;
use Be\Util\Net\FileUpload;


/**
 * 商品 计划任务
 */
class TaskProduct
{

    /**
     * 同步到 ES
     *
     * @param array $products
     */
    public function syncEs(array $products)
    {
        if (count($products) === 0) return;

        $configSystemEs = Be::getConfig('App.System.Es');
        $configEs = Be::getConfig('App.Shop.Es');
        if ($configSystemEs->enable === 0 || $configEs->enable === 0) {
            return;
        }

        $db = Be::getDb();

        $batch = [];
        foreach ($products as $product) {
            $product->is_enable = (int)$product->is_enable;

            // 采集的商品，不处理
            if ($product->is_enable === -1) {
                continue;
            }

            $batch[] = [
                'index' => [
                    '_index' => $configEs->indexProduct,
                    '_id' => $product->id,
                ]
            ];

            $product->is_delete = (int)$product->is_delete;

            if ($product->is_delete === 1) {
                $batch[] = [
                    'id' => $product->id,
                    'is_delete' => true
                ];
            } else {
                $categories = [];
                $sql = 'SELECT category_id FROM shop_product_category WHERE product_id = ? ORDER BY ordering ASC';
                $categoryIds = $db->getValues($sql, [$product->id]);
                if (count($categoryIds) > 0) {
                    $sql = 'SELECT id, `name` FROM shop_category WHERE id IN (\'' . implode('\',\'', $categoryIds) . '\')';
                    $categories = $db->getObjects($sql);
                }

                $sql = 'SELECT tag FROM shop_product_tag WHERE product_id = ? ORDER BY ordering ASC';
                $tags = $db->getValues($sql, [$product->id]);

                $sql = 'SELECT id,url,is_main,ordering FROM shop_product_image WHERE product_id = ? AND product_item_id = \'\' ORDER BY ordering ASC';
                $images = $db->getObjects($sql, [$product->id]);
                foreach ($images as &$image) {
                    $image->is_main = $image->is_main ? true : false;
                    $image->ordering = (int)$image->ordering;
                }
                unset($image);

                $styles = [];
                if ($product->style === '2') {
                    $sql = 'SELECT id,name,icon_type,ordering FROM shop_product_style WHERE product_id = ? ORDER BY ordering ASC';
                    $styles = $db->getObjects($sql, [$product->id]);

                    foreach ($styles as &$style) {
                        $style->ordering = (int)$style->ordering;

                        $sql = 'SELECT id,value,icon_image,icon_color,ordering FROM shop_product_style_item WHERE product_style_id = ? ORDER BY ordering ASC';
                        $styleItems = $db->getObjects($sql, [$style->id]);
                        foreach ($styleItems as &$styleItem) {
                            $styleItem->ordering = (int)$styleItem->ordering;
                        }
                        unset($styleItem);
                        $style->items = $styleItems;
                    }
                    unset($style);
                }

                $sql = 'SELECT id, sku, barcode, style, style_json, price, original_price, weight, weight_unit, stock FROM shop_product_item WHERE product_id = ? ORDER BY ordering ASC';
                $items = $db->getObjects($sql, [$product->id]);
                foreach ($items as &$item) {

                    $styleJson = null;
                    if ($item->style_json) {
                        $styleJson = json_decode($item->style_json, true);
                    }
                    if (!$styleJson) {
                        $styleJson = [];
                    }
                    $item->style_json = $styleJson;

                    $item->price = (float)$item->price;
                    $item->original_price = (float)$item->original_price;
                    $item->weight = (float)$item->weight;
                    $item->stock = (int)$item->stock;

                    $sql = 'SELECT id,url,is_main,ordering FROM shop_product_image WHERE is_main = 1 AND  product_id = ? AND product_item_id = ? ORDER BY ordering ASC';
                    $itemImages = $db->getObjects($sql, [$product->id, $item->id]);
                    foreach ($itemImages as &$itemImage) {
                        $itemImage->is_main = $itemImage->is_main ? true : false;
                        $itemImage->ordering = (int)$itemImage->ordering;
                    }
                    unset($itemImage);
                    $item->images = $itemImages;
                }
                unset($item);

                $batch[] = [
                    'id' => $product->id,
                    'spu' => $product->spu,
                    'name' => $product->name,
                    'summary' => $product->summary,
                    'url' => $product->url,
                    'categories' => $categories,
                    'brand' => $product->brand,
                    'tags' => $tags,
                    'style' => (int)$product->style,
                    'styles' => $styles,
                    'stock_tracking' => (int)$product->stock_tracking,
                    'stock_out_action' => (int)$product->stock_out_action,
                    'ordering' => (int)$product->ordering,
                    'hits' => (int)$product->hits,
                    'sales_volume' => (int)$product->sales_volume_base + (int)$product->sales_volume,
                    'price_from' => (float)$product->price_from,
                    'price_to' => (float)$product->price_to,
                    'original_price_from' => (float)$product->original_price_from,
                    'original_price_to' => (float)$product->original_price_to,
                    'rating_sum' => (int)$product->rating_sum,
                    'rating_count' => (int)$product->rating_count,
                    'rating_avg' => (float)$product->rating_avg,
                    'is_enable' => $product->is_enable === 1,
                    'is_delete' => $product->is_delete === 1,
                    'create_time' => $product->create_time,
                    'update_time' => $product->update_time,
                    'images' => $images,
                    'items' => $items,
                ];
            }
        }

        if (count($batch) > 0) {
            $es = Be::getEs();
            $response = $es->bulk(['body' => $batch]);
            if ($response['errors'] > 0) {
                $reason = '';
                if (isset($response['items']) && count($response['items']) > 0) {
                    foreach ($response['items'] as $item) {
                        if (isset($item['index']['error']['reason'])) {
                            $reason = $item['index']['error']['reason'];
                            break;
                        }
                    }
                }
                throw new ServiceException('商品同步到ES出错：' . $reason);
            }
        }
    }

    /**
     * 同步到 缓存
     *
     * @param array $products
     */
    public function syncCache(array $products)
    {
        if (count($products) === 0) return;

        $db = Be::getDb();
        $cache = Be::getCache();

        $keyValues = [];
        foreach ($products as $product) {

            $product->is_enable = (int)$product->is_enable;

            // 采集的商品，不处理
            if ($product->is_enable === -1) {
                continue;
            }

            $product->is_delete = (int)$product->is_delete;

            $key = 'Shop:Product:' . $product->id;
            if ($product->is_delete === 1) {
                $cache->delete($key);
            } else {

                $product->url_custom = (int)$product->url_custom;
                $product->seo_title_custom = (int)$product->seo_title_custom;
                $product->seo_description_custom = (int)$product->seo_description_custom;
                $product->style = (int)$product->style;
                $product->stock_tracking = (int)$product->stock_tracking;
                $product->stock_out_action = (int)$product->stock_out_action;
                $product->ordering = (int)$product->ordering;
                $product->hits = (int)$product->hits;
                $product->sales_volume = (int)$product->sales_volume_base + (int)$product->sales_volume;
                $product->rating_sum = (int)$product->rating_sum;
                $product->rating_count = (int)$product->rating_count;
                $product->is_enable = (int)$product->is_enable;

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

                $sql = 'SELECT * FROM shop_product_image WHERE product_id = ? AND product_item_id = \'\' ORDER BY ordering ASC';
                $images = $db->getObjects($sql, [$product->id]);
                foreach ($images as $image) {
                    $image->is_main = (int)$image->is_main;
                    $image->ordering = (int)$image->ordering;
                }
                $product->images = $images;

                $sql = 'SELECT category_id FROM shop_product_category WHERE product_id = ?';
                $categoryIds = $db->getValues($sql, [$product->id]);
                if (count($categoryIds) > 0) {
                    $product->category_ids = $categoryIds;

                    $sql = 'SELECT * FROM shop_category WHERE id IN (\'' . implode('\',\'', $categoryIds) . '\')';
                    $categories = $db->getObjects($sql);
                    foreach ($categories as &$category) {
                        $category->url_custom = (int)$category->url_custom;
                        $category->seo_title_custom = (int)$category->seo_title_custom;
                        $category->seo_description_custom = (int)$category->seo_description_custom;
                        $category->ordering = (int)$category->ordering;
                        $category->is_enable = (int)$category->is_enable;
                        $category->is_delete = (int)$category->is_delete;
                    }

                    unset($category);
                    $product->categories = $categories;
                } else {
                    $product->category_ids = [];
                    $product->categories = [];
                }

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

                $sql = 'SELECT * FROM shop_product_item WHERE product_id = ?';
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

                    $sql = 'SELECT * FROM shop_product_image WHERE product_id = ? AND  product_item_id = ? ORDER BY ordering ASC';
                    $itemImages = $db->getObjects($sql, [$product->id, $item->id]);
                    foreach ($itemImages as &$itemImage) {
                        $itemImage->is_main = (int)$itemImage->is_main;
                        $itemImage->ordering = (int)$itemImage->ordering;
                    }
                    unset($itemImage);
                    $item->images = $itemImages;
                }
                $product->items = $items;

                $keyValues[$key] = $product;
            }
        }

        if (count($keyValues) > 0) {
            $cache->setMany($keyValues);
        }
    }

    /**
     * 下载商品信息中的远程文件
     *
     * @param object $product
     * @return void
     */
    public function downloadRemoteImages(object $product)
    {
        $storage = Be::getStorage();
        $storageRootUrl = $storage->getRootUrl();
        $storageRootUrlLen = strlen($storageRootUrl);

        $hasChange = false;
        $updateObj = new \stdClass();
        $updateObj->id = $product->id;

        $db = Be::getDb();

        $imageKeyValues = [];

        $images = $db->getObjects('SELECT * FROM shop_product_image WHERE product_id=?', [$product->id]);
        foreach ($images as $image) {
            $remoteImage = trim($image->url);
            if ($remoteImage !== '') {
                if (strlen($remoteImage) < $storageRootUrlLen || substr($remoteImage, $storageRootUrlLen) !== $storageRootUrl) {
                    $storageImage = false;
                    try {
                        $storageImage = $this->downloadRemoteImage($product, $remoteImage);
                    } catch (\Throwable $t) {
                        Be::getLog()->error($t);
                    }

                    if ($storageImage) {
                        $imageKeyValues[$image->original] = $storageImage;

                        $obj = new \stdClass();
                        $obj->id = $image->id;
                        $obj->url = $storageImage;
                        $obj->update_time = date('Y-m-d H:i:s');
                        $db->update('shop_product_image', $obj, 'id');

                        $hasChange = true;
                    }
                }
            }
        }

        if ($product->relate_id !== '') {
            $productRelateDetail = $db->getObject('SELECT * FROM shop_product_relate_item WHERE relate_id=? AND product_id=?', [$product->relate_id, $product->id]);
            if ($productRelateDetail) {
                $remoteImage = trim($productRelateDetail->icon_image);
                if ($remoteImage !== '') {
                    if (strlen($remoteImage) < $storageRootUrlLen || substr($remoteImage, $storageRootUrlLen) !== $storageRootUrl) {
                        $storageImage = false;
                        if (isset($imageKeyValues[$remoteImage])) {
                            $storageImage = $imageKeyValues[$remoteImage];
                        } else {
                            try {
                                $storageImage = $this->downloadRemoteImage($product, $remoteImage);
                            } catch (\Throwable $t) {
                                Be::getLog()->error($t);
                            }
                        }

                        if ($storageImage) {
                            $obj = new \stdClass();
                            $obj->id = $productRelateDetail->id;
                            $obj->icon_image = $storageImage;
                            $obj->update_time = date('Y-m-d H:i:s');
                            $db->update('shop_product_relate_item', $obj, 'id');

                            $hasChange = true;
                        }
                    }
                }
            }
        }


        $descriptionHasChange = false;
        $descriptionImages = [];

        $configSystem = Be::getConfig('App.System.System');
        $reg = '/ src=\"([^\"]*\.(' . implode('|', $configSystem->allowUploadImageTypes) . ')[^\"]*)\"/is';
        if (preg_match_all($reg, $product->description, $descriptionImages)) {
            $i = 0;
            foreach ($descriptionImages[1] as $descriptionImage) {
                $descriptionImage = trim($descriptionImage);
                if ($descriptionImage !== '') {
                    if (strlen($descriptionImage) < $storageRootUrlLen || substr($descriptionImage, 0, $storageRootUrlLen) !== $storageRootUrl) {
                        $storageImage = false;
                        if (isset($imageKeyValues[$descriptionImage])) {
                            $storageImage = $imageKeyValues[$descriptionImage];
                        } else {
                            try {
                                $storageImage = $this->downloadRemoteImage($product, $descriptionImage);
                            } catch (\Throwable $t) {
                                Be::getLog()->error($t);
                            }
                        }

                        if ($storageImage) {
                            $imageKeyValues[$descriptionImage] = $storageImage;

                            $replaceFrom = $descriptionImages[0][$i];
                            $replaceTo = str_replace($descriptionImage, $storageImage, $replaceFrom);
                            $product->description = str_replace($replaceFrom, $replaceTo, $product->description);
                            $descriptionHasChange = true;
                        }
                    }
                }

                $i++;
            }

            if ($descriptionHasChange) {
                $updateObj->description = $product->description;
                $hasChange = true;
            }
        }

        if ($hasChange) {
            $updateObj->download_remote_image = 2;
            $updateObj->update_time = date('Y-m-d H:i:s');
            Be::getDb()->update('shop_product', $updateObj, 'id');
        }
    }

    /**
     * 下载远程文件
     *
     * @param object $product 商品
     * @param string $remoteImage 远端文件
     * @return string 上传成功的CDN网址
     * @throws \Be\Runtime\RuntimeException
     */
    public function downloadRemoteImage(object $product, string $remoteImage): string
    {
        $configDownloadRemoteImage = Be::getConfig('App.Shop.DownloadRemoteImage');

        // 示例：https://cdn.shopify.com/s/files/1/0139/8942/products/Womens-Zamora-Jogger-Scrub-Pant_martiniolive-4.jpg
        $remoteImage = trim($remoteImage);

        $name = substr($remoteImage, strrpos($remoteImage, '/') + 1);
        $name = trim($name);

        $originalExt = strrchr($name, '.');
        if ($originalExt && strlen($originalExt) > 1) {
            $originalExt = substr($originalExt, 1);
            $originalExt = strtolower($originalExt);
            $originalExt = trim($originalExt);

            $originalName = substr($name, 0, strrpos($name, '.'));
        } else {
            $originalExt = '';
            $originalName = $name;
        }

        $tmpDir = Be::getRuntime()->getRootPath() . '/data/tmp/';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
            chmod($tmpDir, 0777);
        }
        $tmpFile = $tmpDir . uniqid(date('Ymdhis') . '-' . rand(1, 999999) . '-', true);

        $fileData = null;
        $success = false;
        $n = 0;
        do {
            $n++;
            try {
                $fileData = Curl::get($remoteImage);
                $success = true;
            } catch (\Throwable $t) {
                if ($configDownloadRemoteImage->retryIntervalMin > 0 || $configDownloadRemoteImage->retryIntervalMax) {
                    if (Be::getRuntime()->isSwooleMode()) {
                        \Swoole\Coroutine::sleep(rand($configDownloadRemoteImage->retryIntervalMin, $configDownloadRemoteImage->retryIntervalMax));
                    } else {
                        sleep(rand($configDownloadRemoteImage->retryIntervalMin, $configDownloadRemoteImage->retryIntervalMax));
                    }
                }
            }
        } while ($success === false && $n < $configDownloadRemoteImage->retryTimes);

        if (!$success) {
            throw new ServiceException('获取远程文件（' . $remoteImage . '）失败！');
        }

        file_put_contents($tmpFile, $fileData);

        try {
            $configSystem = Be::getConfig('App.System.System');
            $maxSize = $configSystem->uploadMaxSize;
            $maxSizeInt = FileSize::string2Int($maxSize);
            $size = filesize($tmpFile);
            if ($size > $maxSizeInt) {
                throw new ServiceException('您上传的文件尺寸已超过最大限制：' . $maxSize . '！');
            }

            $ext = Mime::detectExt($tmpFile, $originalExt);

            if (!in_array($ext, $configSystem->allowUploadImageTypes)) {
                throw new ServiceException('禁止上传的图像类型：' . $ext . '！');
            }

            $dirName = '';
            switch ($configDownloadRemoteImage->dirname) {
                case 'id':
                    $dirName = $product->id;
                    break;
                case 'url':
                    $dirName = $product->url;
                    break;
                case 'spu':
                    $dirName = strtolower($product->spu);
                    break;
            };

            $fileName = '';
            switch ($configDownloadRemoteImage->fileName) {
                case 'original':
                    $fileName = $originalName . '.' . $ext;
                    break;
                case 'md5':
                    $fileName = md5_file($tmpFile) . '.' . $ext;
                    break;
                case 'sha1':
                    $fileName = sha1_file($tmpFile) . '.' . $ext;
                    break;
                case 'timestamp':
                    $fileName = uniqid(date('Ymdhis') . '-' . rand(1, 999999) . '-', true) . '.' . $ext;
                    break;
            };

            $storage = Be::getStorage();
            $object = $configDownloadRemoteImage->rootPath . ($dirName !== '' ? ($dirName . '/') : '') . $fileName;

            if ($storage->isFileExist($object)) {
                $url = $storage->getFileUrl($object);
            } else {
                $url = $storage->uploadFile($object, $tmpFile);
            }

        } catch (\Throwable $t) {
            if (file_exists($tmpFile)) {
                unlink($tmpFile);
            }

            Be::getLog()->warning($t);

            throw $t;
        }

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }

        if ($configDownloadRemoteImage->intervalMin > 0 || $configDownloadRemoteImage->intervalMax) {
            if (Be::getRuntime()->isSwooleMode()) {
                \Swoole\Coroutine::sleep(rand($configDownloadRemoteImage->intervalMin, $configDownloadRemoteImage->intervalMax));
            } else {
                sleep(rand($configDownloadRemoteImage->intervalMin, $configDownloadRemoteImage->intervalMax));
            }
        }
        
        return $url;
    }
}
