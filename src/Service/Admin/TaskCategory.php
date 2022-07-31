<?php

namespace Be\App\ShopFai\Service\Admin;


use Be\Be;

class TaskCategory
{

    /**
     * 同步到 Redis
     *
     * @param array $categories
     */
    public function syncCache(array $categories)
    {
        if (count($categories) === 0) return;

        $redis = Be::getCache();

        $keyValues = [];
        foreach ($categories as $category) {

            $category->is_delete = (int)$category->is_delete;

            $key = 'ShopFai:Category:' . $category->id;

            if ($category->is_delete === 1) {
                $redis->delete($key);
            } else {

                $category->url_custom = (int)$category->url_custom;
                $category->seo_title_custom = (int)$category->seo_title_custom;
                $category->seo_description_custom = (int)$category->seo_description_custom;
                $category->ordering = (int)$category->ordering;
                $category->is_enable = (int)$category->is_enable;

                $keyValues[$key] = $category;
            }
        }

        if (count($keyValues) > 0) {
            $redis->setMany($keyValues);
        }
    }

}
