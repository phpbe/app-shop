<?php

namespace Be\App\Shop\Service;

use Be\App\ServiceException;
use Be\Be;

class ProductReview
{

    /**
     * 获取商品评论评分一均值
     *
     * @param string $productId
     * @return int
     */
    public function getAverageRating(string $productId)
    {
        $db = Be::getDb();
        $sql = 'SELECT AVG(rating) FROM shop_product_review WHERE product_id = ?';
        return round((float) $db->getValue($sql, [$productId]), 1);
    }

    /**
     * 获取商品评论总数
     *
     * @param string $productId
     * @param array $option
     * @return int
     */
    public function getCount($productId, $option = [])
    {
        $db = Be::getDb();
        $sql = 'SELECT COUNT(*) FROM shop_product_review WHERE product_id = ' . $productId;
        return $db->getValue($sql);
    }

    /**
     * 获取商品评论
     *
     * @param string $productId
     * @param array $option
     * @param array $with
     * @return array
     */
    public function getReviews(string $productId, array $option = [], array $with = []): array
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product_review WHERE product_id = \'' . $productId . '\' ';
        $sql .= ' ORDER BY create_time DESC';

        $pageSize = 10;
        $page = 1;
        if (isset($option['pageSize']) && is_numeric($option['pageSize']) && $option['pageSize'] >= 1 && $option['pageSize'] <= 100) {
            $pageSize = $option['pageSize'];
        }
        if (isset($option['page']) && is_numeric($option['page']) && $option['page'] >= 1) {
            $page = $option['page'];
        }
        $sql .= ' LIMIT ' . ($page - 1) * $pageSize . ',' . $pageSize;

        $reviews = $db->getObjects($sql);

        $productService = Be::getService('App.Shop.Product');
        foreach ($reviews as &$review) {

            if (isset($with['product'])) {
                $review->product = $productService->getProduct($review->product_id);
            }

            if (isset($with['images'])) {
                $sql = 'SELECT * FROM shop_product_review_image WHERE product_review_id = ?';
                $review->images = $db->getObjects($sql, [$review->id]);
            }
        }

        return $reviews;
    }

    /**
     * 获取商品评论
     *
     * @param string $reviewId
     * @return object
     */
    public function getReview(string $reviewId): object
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product_review WHERE id = ?';
        $review =  $db->getObject($sql, [$reviewId]);

        $serviceProduct = Be::getService('App.Shop.Product');
        $review->product = $serviceProduct->getProduct($review->product_id);

        $sql = 'SELECT * FROM shop_product_review_image WHERE product_review_id = ?';
        $review->images = $db->getObjects($sql, [$reviewId]);

        return $review;
    }

    /**
     * 联系
     *
     * @param string $userId 用户ID
     * @param string $productId 商品ID
     * @param int $rating 评分
     * @param string $name 姓名
     * @param string $content 内容
     * @param array $imageInfos 图像
     * @return bool
     * @throws \Throwable
     */
    public function post(string $userId, string $productId, int $rating, string $name, string $content, array $imageInfos = [])
    {
        if ($userId > 0) {
            $tupleUser = Be::getTuple('shop_user');
            try {
                $tupleUser->load($userId);
            } catch (\Throwable $t) {
                throw new ServiceException('User (#' . $userId . ') does not exist!');
            }
            $name = $tupleUser->first_name . ' ' . $tupleUser->last_name;
        } else {
            if (!$name) {
                throw new ServiceException('Please entry name!');
            }
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {

            $tupleOrder = Be::getTuple('shop_product');
            $tupleOrder->load($productId);

            $now = date('Y-m-d H:i:s');
            $tupleProductReview = Be::getTuple('shop_product_review');
            $tupleProductReview->product_id = $productId;
            $tupleProductReview->user_id = $userId;
            $tupleProductReview->name = $name;
            $tupleProductReview->rating = $rating;
            $tupleProductReview->content = $content;
            $tupleProductReview->create_time = $now;
            $tupleProductReview->insert();

            if (count($imageInfos) > 0) {

                $configProduct = Be::getConfig('Shop.Product');

                $i = 0;
                foreach ($imageInfos as $imageInfo) {
                    $imageData = base64_decode($imageInfo['data']);

                    $tmpPath = Be::getRuntime()->getRootPath() . '/data/tmp/Shop/product/review/' . $productId;
                    $dir = dirname($tmpPath);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                        chmod($dir, 0755);
                    }
                    file_put_contents($tmpPath, $imageData);

                    $libImage = Be::getLib('Image');
                    $libImage->open($tmpPath);
                    if ($libImage->isImage()) {
                        unlink($tmpPath);

                        $tupleProductReviewImage = Be::getTuple('shop_product_review_image');
                        $tupleProductReviewImage->product_review_id = $tupleProductReview->id;

                        $imageName = date('ymdHis') . $i . rand(1000, 9999) . '_l.' . $libImage->getType();
                        $imagePath = Be::getRuntime()->getUploadPath() . '/Shop/product/review/' . $productId . '/' . $imageName;
                        $libImage->resize($configProduct->reviewImageLargeWidth, $configProduct->reviewImageLargeHeight);
                        $libImage->save($imagePath);
                        $imageUrl = '/' . Be::getRuntime()->getUploadDir() . '/Shop/product/review/' . $productId . '/' . $imageName;
                        $tupleProductReviewImage->large = $imageUrl;

                        $imageName = date('ymdHis') . $i . rand(1000, 9999) . '_s.' . $libImage->getType();
                        $imagePath = Be::getRuntime()->getUploadPath() . '/Shop/product/review/' . $productId . '/' . $imageName;
                        $libImage->resize($configProduct->reviewImageSmallWidth, $configProduct->reviewImageSmallHeight);
                        $libImage->save($imagePath);
                        $imageUrl = '/' . Be::getRuntime()->getUploadDir() . '/Shop/product/review/' . $productId . '/' . $imageName;
                        $tupleProductReviewImage->small = $imageUrl;

                        $tupleProductReviewImage->insert();
                    }
                    $i++;
                }
            }

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException('Post product review exception (log id: ' . $logId . ') ' );
        }

        return true;
    }


}
