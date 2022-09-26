<?php

namespace Be\App\ShopFai\Service;

use Be\App\ServiceException;
use Be\Be;

class Category
{


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

        $key = 'ShopFai:Category:' . $categoryId;
        $category = $cache->get($key);

        if (!$category) {
            throw new ServiceException('Category #' . $categoryId . ' does not exists！');
        }

        return $category;
    }

    /**
     * 获取随机一个分类
     *
     * @return object| mixed
     * @throws ServiceException
     */
    public function getRandCategory()
    {
        $sql = 'SELECT id, `name` FROM shopfai_category WHERE is_enable = 1 AND is_delete = 0 LIMIT 1';
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
     * @return string
     * @throws ServiceException
     */
    public function getCategoryUrl(array $params = []): string
    {
        $category = $this->getCategory($params['id']);
        $config = Be::getConfig('App.ShopFai.Category');
        return '/' . $config->urlPrefix . '/' . $category->url . $config->urlSuffix;
    }


}