<?php

namespace Be\App\Shop\Service;


use Be\App\ServiceException;
use Be\Util\Crypt\Random;
use Be\Util\Validator;
use Be\Be;

class UserProductReview
{

    /**
     * 获取订单总数
     *
     * @param int $userId
     * @param array $option
     * @return int
     */
    public function getCount($userId, $option = [])
    {
        $db = Be::getDb();
        $sql = 'SELECT COUNT(*) FROM shop_product_review WHERE user_id = ' . $db->quoteValue($userId);
        return $db->getValue($sql);
    }

    /**
     * 获取用户的商品评论列表
     *
     * @param $userId
     * @param $option
     * @param $with
     * @return array
     */
    public function getReviews($userId, $option = [], $with = [])
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product_review WHERE user_id = ' . $db->quoteValue($userId);

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

        foreach ($reviews as &$review) {
            $review->product = $this->getReviewProduct($review->product_id);

            if (isset($with['images'])) {
                $sql = 'SELECT * FROM shop_product_review_image WHERE product_review_id = ?';
                $review->images = $db->getObjects($sql, [$review->id]);
            }
        }

        return $reviews;
    }

    /**
     * 获取用户的商品评论
     *
     * @param string $reviewId
     * @return object
     */
    public function getReview(string $reviewId): object
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM shop_product_review WHERE id = ?';
        $review =  $db->getObject($sql, [$reviewId]);

        $sql = 'SELECT * FROM shop_product_review_image WHERE product_review_id = ?';
        $review->images = $db->getObjects($sql, [$reviewId]);

        $review->product = $this->getReviewProduct($review->product_id);

        return $review;
    }

    private function getReviewProduct($productId) {
        $product = Be::getService('App.Shop.Product')->getProduct($productId);

        $imageSmall = '';
        $imageMedium = '';
        $imageLarge = '';
        foreach ($product->images as $image) {
            if ($image->is_main) {
                $imageSmall = $image->small;
                $imageMedium = $image->medium;
                $imageLarge = $image->large;
                break;
            }
        }
        if (!$imageSmall && count($product->images) > 0) {
            $imageSmall = $product->images[0]->small;
            $imageMedium = $product->images[0]->medium;
            $imageLarge = $product->images[0]->large;
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'image_small' => $imageSmall,
            'image_medium' => $imageMedium,
            'image_large' => $imageLarge,
            'price' => $product->price,
            'url' =>  beUrl('Shop.Product.detail', ['id' => $product->id]),
        ];
    }

}
