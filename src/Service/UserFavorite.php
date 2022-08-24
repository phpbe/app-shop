<?php

namespace Be\App\ShopFai\Service;

use Be\Be;

class UserFavorite
{

    /**
     * 获取用户收藏列表
     *
     * @param $userId
     * @return array
     */
    public function getProducts($userId)
    {
        $products = [];

        $redis = Be::getRedis();

        $productIds = null;
        $config = Be::getConfig('App.ShopFai.User');
        if ($config->favoriteDrive === 'redis') {
            $productIds = $redis->sMembers('ShopFai:User:Favorite:' . $userId);
        } else {
            $sql = 'SELECT product_id FROM shopfai_user_favorite WHERE user_id = ? AND is_enable = 1 AND is_delete = 0';
            $productIds = Be::getDb()->getValues($sql, [$userId]);
        }

        foreach ($productIds as $productId) {
            $product = $redis->get('ShopFai:Product:' . $productId);
            if (!$product) {
                continue;
            }

            $product = json_decode($product, true);

            $imageSmall = '';
            $imageMedium = '';
            $imageLarge = '';
            foreach ($product['images'] as $image) {
                if ($image['is_main']) {
                    $imageSmall = $image['small'];
                    $imageMedium = $image['medium'];
                    $imageLarge = $image['large'];
                    break;
                }
            }
            if (!$imageSmall && count($product['images']) > 0) {
                $imageSmall = $product['images'][0]['small'];
                $imageMedium = $product['images'][0]['medium'];
                $imageLarge = $product['images'][0]['large'];
            }

            $products[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'image_small' => $imageSmall,
                'image_medium' => $imageMedium,
                'image_large' => $imageLarge,
                'price' => $product['price'],
                'url' =>  beUrl('ShopFai.Product.detail', ['id' => $product['id']]),
            ];
        }

        return $products;
    }

    /**
     * 添加收藏
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function addFavorite($userId, $productId)
    {
        $config = Be::getConfig('App.ShopFai.User');
        if ($config->favoriteDrive === 'redis') {
            $redis = Be::getRedis();
            $redis->sAdd('ShopFai:User:Favorite:' . $userId, $productId);
        } else {
            $tupleUserFavorite = Be::getTuple('shopfai_user_favorite');
            try {
                $tupleUserFavorite->load([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ]);
            } catch (\Throwable $t) {
                $tupleUserFavorite->user_id = $userId;
                $tupleUserFavorite->product_id = $productId;
                $tupleUserFavorite->is_enable = 1;
                $tupleUserFavorite->is_delete = 0;
                $tupleUserFavorite->create_time = date('Y-m-d H:i:s');
                $tupleUserFavorite->update_time = date('Y-m-d H:i:s');
                $tupleUserFavorite->insert();
            }
        }

        return true;
    }

    /**
     * 用户是否收藏指定商品
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function isFavorite($userId, $productId)
    {
        $config = Be::getConfig('App.ShopFai.User');
        if ($config->favoriteDrive === 'redis') {
            $redis = Be::getRedis();
            return $redis->sIsMember('ShopFai:User:Favorite:' . $userId, $productId);
        } else {
            $sql = 'SELECT COUNT(*) FROM shopfai_user_favorite WHERE user_id = ? AND product_id = ? AND is_enable = 1 AND is_delete = 0';
            return Be::getDb()->getValue($sql, [$userId, $productId]) > 0;
        }
    }

    /**
     * 删除用户收藏
     *
     * @param int $userId
     * @param int $productId
     * @return bool
     */
    public function deleteFavorite($userId, $productId)
    {
        $config = Be::getConfig('App.ShopFai.User');
        if ($config->favoriteDrive === 'redis') {
            $redis = Be::getRedis();
            return $redis->sRem('ShopFai:User:Favorite:' . $userId, $productId);
        } else {
            $tupleUserFavorite = Be::getTuple('shopfai_user_favorite');
            try {
                $tupleUserFavorite->loadBy([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ]);

                $tupleUserFavorite->is_delete = 1;
                $tupleUserFavorite->update_time = date('Y-m-d H:i:s');
                $tupleUserFavorite->update();
            } catch (\Throwable $t) {
            }
        }

        return true;
    }


}
