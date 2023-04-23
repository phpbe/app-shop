<?php

namespace Be\App\Shop\Service;

use Be\App\ServiceException;
use Be\Be;

class Category
{

    /**
     * 获取分类列表
     *
     * @param int $n 数量
     * @return array
     */
    public function getCategories(int $n = 0): array
    {
        $cache = Be::getCache();

        $key = 'App:Shop:Categories';
        $categories = $cache->get($key);

        if (!$categories) {
            $table =  Be::getTable('shop_category');
            $table->where('is_delete', 0);
            $table->where('is_enable', 1);
            $table->orderBy('ordering', 'ASC');
            $categories = $table->getObjects();

            $configCache = Be::getConfig('App.Shop.Cache');
            $cache->set($key, $categories, $configCache->categories);
        }

        if ($n > 0 && $n < count($categories)) {
            ;$categories = array_slice($categories, 0, $n);
        }

        return $categories;
    }

    /**
     * 从REDIS 获取分类数据
     *
     * @param string $categoryId 商品ID
     * @return object| mixed
     * @throws ServiceException
     */
    public function getCategory(string $categoryId)
    {
        $cache = Be::getCache();

        $key = 'App:Shop:Category:' . $categoryId;
        $category = $cache->get($key);

        if (!$category) {
            try {
                $category = $this->getCategoryFromDb($categoryId);
            } catch (\Throwable $t) {
                $category = '-1';
            }

            $configCache = Be::getConfig('App.Shop.Cache');
            $cache->set($key, $category, $configCache->category);
        }

        if ($category === '-1') {
            throw new ServiceException('Category #' . $categoryId . ' does not exists！');
        }

        return $category;
    }


    /**
     * 获取分类
     *
     * @param string $pageId 页面ID
     * @return object 分类对象
     */
    public function getCategoryFromDb(string $categoryId): object
    {
        $tupleCategory = Be::getTuple('shop_category');
        try {
            $tupleCategory->load($categoryId);
        } catch (\Throwable $t) {
            throw new ServiceException('Category #' . $categoryId . ' does not exists！');
        }

        return $tupleCategory->toObject();
    }

    /**
     * 获取随机一个分类
     *
     * @return object| mixed
     * @throws ServiceException
     */
    public function getRandCategory()
    {
        $sql = 'SELECT id, `name` FROM shop_category WHERE is_enable = 1 AND is_delete = 0 LIMIT 1';
        $category = Be::getDb()->getObject($sql);
        if ($category) {
            return $category;
        }

        return (object)[
            'id' => '',
            'name' => 'no available categories',
        ];
    }


    /**
     * 获取分类伪静态页网址
     *
     * @param array $params
     * @return array
     * @throws ServiceException
     */
    public function getCategoryUrl(array $params = []): array
    {
        $category = $this->getCategory($params['id']);
        $params1 = ['id' => $params['id']];
        unset($params['id']);

        $config = Be::getConfig('App.Shop.Category');
        return [$config->urlPrefix . $category->url . $config->urlSuffix, $params1, $params];
    }


}
