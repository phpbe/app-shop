<?php

namespace Be\App\Shop\Service\Admin;

use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\App\ServiceException;
use Be\Be;
use Be\Db\Tuple;

class Category
{

    /**
     * 获取分类列表
     *
     * @return array
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getCategories()
    {
        $sql = 'SELECT * FROM shop_category WHERE is_delete = 0 ORDER BY ordering ASC';
        $categories = Be::getDb()->getObjects($sql);
        return $categories;
    }

    /**
     * 获取分类
     *
     * @param $categoryId
     * @return object
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getCategory($categoryId)
    {
        $sql = 'SELECT * FROM shop_category WHERE id=?';
        $category = Be::getDb()->getObject($sql, [$categoryId]);

        $category->url_custom = (int)$category->url_custom;
        $category->seo_title_custom = (int)$category->seo_title_custom;
        $category->seo_description_custom = (int)$category->seo_description_custom;
        $category->ordering = (int)$category->ordering;
        $category->is_enable = (int)$category->is_enable;
        $category->is_delete = (int)$category->is_delete;

        return $category;
    }

    /**
     * 获取分类键值对
     *
     * @return array
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getCategoryKeyValues()
    {
        $sql = 'SELECT id, `name` FROM shop_category WHERE is_delete = 0 ORDER BY ordering ASC';
        return Be::getDb()->getKeyValues($sql);
    }

    /**
     * 编辑分类
     *
     * @param array $data 分类数据
     * @return Tuple
     * @throws \Throwable
     */
    public function edit($data)
    {
        $db = Be::getDb();

        $isNew = true;
        $categoryId = null;
        if (isset($data['id']) && is_string($data['id']) && $data['id'] !== '') {
            $isNew = false;
            $categoryId = $data['id'];
        }

        $tupleCategory = Be::getTuple('shop_category',);
        if (!$isNew) {
            try {
                $tupleCategory->load($categoryId);
            } catch (\Throwable $t) {
                throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
            }
        }

        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new ServiceException('分类名称未填写！');
        }
        $name = $data['name'];

        if (!isset($data['description']) || !is_string($data['description'])) {
            $data['description'] = '';
        }

        if (!isset($data['url_custom']) || $data['url_custom'] !== 1) {
            $data['url_custom'] = 0;
        }

        $url = null;
        if (!isset($data['url']) || !is_string($data['url'])) {
            $url = strtolower($name);
            $url = preg_replace('/[^a-z0-9]/', '-', $url);
            $url = str_replace(' ', '-', $url);
            while (strpos($url, '--') !== false) {
                $url = str_replace('--', '-', $url);
            }
            $data['url_custom'] = 0;
        } else {
            $url = $data['url'];
        }
        $urlUnique = $url;
        $urlIndex = 0;
        $urlExist = null;
        do {
            if ($isNew) {
                $urlExist = Be::getTable('shop_category',)
                        ->where('url', $urlUnique)
                        ->getValue('COUNT(*)') > 0;
            } else {
                $urlExist = Be::getTable('shop_category',)
                        ->where('url', $urlUnique)
                        ->where('id', '!=', $categoryId)
                        ->getValue('COUNT(*)') > 0;
            }

            if ($urlExist) {
                $urlIndex++;
                $urlUnique = $url . '-' . $urlIndex;
            }
        } while ($urlExist);
        $url = $urlUnique;

        if (!isset($data['image']) || !is_string($data['image'])) {
            $data['image'] = '';
        }

        if (!isset($data['seo_title']) || !is_string($data['seo_title'])) {
            $data['seo_title'] = $name;
        }

        if (!isset($data['seo_title_custom']) || !is_numeric($data['seo_title_custom']) || $data['seo_title_custom'] !== 1) {
            $data['seo_title_custom'] = 0;
        }

        if (!isset($data['seo_description']) || !is_string($data['seo_description'])) {
            $data['seo_description'] = $data['description'];
        }

        if (!isset($data['seo_description_custom']) || !is_numeric($data['seo_description_custom']) || $data['seo_description_custom'] !== 1) {
            $data['seo_description_custom'] = 0;
        }

        if (!isset($data['seo_keywords']) || !is_string($data['seo_keywords'])) {
            $data['seo_keywords'] = '';
        }

        if (!isset($data['is_enable']) || $data['is_enable'] !== 1) {
            $data['is_enable'] = 0;
        }

        if (!isset($data['ordering']) || !is_numeric($data['ordering'])) {
            $data['ordering'] = 0;
        }

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tupleCategory->name = $name;
            $tupleCategory->description = $data['description'];
            $tupleCategory->url = $url;
            $tupleCategory->url_custom = $data['url_custom'];
            $tupleCategory->image = $data['image'];
            $tupleCategory->seo_title = $data['seo_title'];
            $tupleCategory->seo_title_custom = $data['seo_title_custom'];
            $tupleCategory->seo_description = $data['seo_description'];
            $tupleCategory->seo_description_custom = $data['seo_description_custom'];
            $tupleCategory->seo_keywords = $data['seo_keywords'];
            $tupleCategory->ordering = $data['ordering'];
            $tupleCategory->is_enable = $data['is_enable'];
            $tupleCategory->update_time = $now;
            if ($isNew) {
                $tupleCategory->is_delete = 0;
                $tupleCategory->create_time = $now;
                $tupleCategory->insert();
            } else {
                $tupleCategory->update();
            }

            $productIds = Be::getTable('shop_product_category')
                ->where('category_id', '=', $tupleCategory->id)
                ->getValues('product_id');
            if (count($productIds) > 0) {
                Be::getTable('shop_product')
                    ->where('id', 'IN', $productIds)
                    ->update(['update_time' =>  $now]);
            }

            $db->commit();

            Be::getService('App.System.Task')->trigger('Shop.CategorySyncCache');

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException(($isNew ? '新建' : '编辑') . '分类发生异常！');
        }

        return $tupleCategory;
    }

    /**
     * 删除分类
     *
     * @param array $categoryIds
     * @return void
     * @throws ServiceException
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function delete(array $categoryIds)
    {
        if (count($categoryIds) === 0) return;

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            foreach ($categoryIds as $categoryId) {

                $tupleCategory = Be::getTuple('shop_category',);
                try {
                    $tupleCategory->loadBy([
                        'id' => $categoryId,
                        'is_delete' => 0
                    ]);
                } catch (\Throwable $t) {
                    throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
                }

                $productIds = Be::getTable('shop_product_category')
                    ->where('category_id', '=', $categoryId)
                    ->getValues('product_id');
                if (count($productIds) > 0) {
                    Be::getTable('shop_product')
                        ->where('id', 'IN', $productIds)
                        ->update(['update_time' =>  $now]);

                    Be::getTable('shop_product_category')
                        ->where('category_id', '=', $categoryId)
                        ->delete();
                }

                $tupleCategory->url = $categoryId;
                $tupleCategory->is_delete = 1;
                $tupleCategory->update_time = $now;
                $tupleCategory->update();
            }

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('删除分类发生异常！');
        }
    }

    /**
     * 在分类下添加商品
     *
     * @param string $categoryId 分类ID
     * @param array $productIds 商品ID列表
     */
    public function addProduct(string $categoryId, array $productIds)
    {
        try {
            Be::getTuple('shop_category',)
                ->loadBy([
                    'id' => $categoryId,
                    'is_delete' => 0
                ]);
        } catch (\Throwable $t) {
            throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
        }

        $existProductIds = Be::getTable('shop_product_category')
            ->where('category_id', $categoryId)
            ->getValues('product_id');
        if (count($existProductIds) > 0) {
            $productIds = array_diff($productIds, $existProductIds);
        }

        if (count($productIds) > 0) {

            $existProductIds = Be::getTable('shop_product')
                ->where('id', 'IN', $productIds)
                ->getValues('id');

            if ($existProductIds === false) {
                $existProductIds = [];
            }

            if (count($existProductIds) != count($productIds)) {
                $diffProductIds = array_diff($productIds, $existProductIds);
                throw new ServiceException('商吕（#' . implode(', #', $diffProductIds) . '）不存在！');
            }

            $db = Be::getDb();
            $db->startTransaction();
            try {
                foreach ($productIds as $productId) {
                    $tupleProductCategory = Be::getTuple('shop_product_category');
                    $tupleProductCategory->product_id = $productId;
                    $tupleProductCategory->category_id = $categoryId;
                    $tupleProductCategory->insert();
                }

                Be::getTable('shop_product')
                    ->where('id', 'IN', $productIds)
                    ->update(['update_time' => date('Y-m-d H:i:s')]);

                $db->commit();

                Be::getService('App.System.Task')->trigger('Shop.ProductSyncEsAndCache');

            } catch (\Throwable $t) {
                $db->rollback();
                Be::getLog()->error($t);

                throw new ServiceException('在分类下添加商品发生异常！');
            }
        }
    }

    /**
     * 将商品从分类中删除
     *
     * @param string $categoryId 分类ID
     * @param array $productIds 商品ID列表
     */
    public function deleteProduct(string $categoryId, array $productIds)
    {
        try {
            Be::getTuple('shop_category',)
                ->loadBy([
                    'id' => $categoryId,
                    'is_delete' => 0
                ]);
        } catch (\Throwable $t) {
            throw new ServiceException('分类（# ' . $categoryId . '）不存在！');
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {
            Be::getTable('shop_product_category')
                ->where('category_id', $categoryId)
                ->where('product_id', 'IN', $productIds)
                ->delete();

            Be::getTable('shop_product')
                ->where('id', 'IN', $productIds)
                ->update(['update_time' => date('Y-m-d H:i:s')]);

            $db->commit();

            Be::getService('App.System.Task')->trigger('Shop.ProductSyncEsAndCache');

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('删除分类发生异常！');
        }
    }


    /**
     * 获取选择器
     *
     * @return array
     */
    public function getCategoryPicker(int $multiple = 0): array
    {
        return [
            'table' => 'shop_category',
            'grid' => [
                'title' => $multiple === 1 ? '选择分类' : '选择一个分类',

                'filter' => [
                    ['is_enable', '=', '1'],
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '分类名称',
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'name' => 'image',
                            'label' => '封面图片',
                            'width' => '90',
                            'driver' => TableItemImage::class,
                            'ui' => [
                                'style' => 'max-width: 60px; max-height: 60px'
                            ],
                            'value' => function($row) {
                                if ($row['imagel'] === '') {
                                    return Be::getProperty('App.Shop')->getWwwUrl() . '/images/category/no-image.jpg';
                                }
                                return $row['image'];
                            },
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * 获取菜单参数选择器
     *
     * @return array
     */
    public function getCategoryMenuPicker(): array
    {
        return [
            'name' => 'id',
            'value' => '分类：{name}',
            'table' => 'shop_category',
            'grid' => [
                'title' => '选择一个分类',

                'filter' => [
                    ['is_enable', '=', '1'],
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '分类名称',
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'name' => 'image',
                            'label' => '封面图片',
                            'width' => '90',
                            'driver' => TableItemImage::class,
                            'ui' => [
                                'style' => 'max-width: 60px; max-height: 60px'
                            ],
                            'value' => function($row) {
                                if ($row['image'] === '') {
                                    return Be::getProperty('App.Shop')->getWwwUrl() . '/images/category/no-image.jpg';
                                }
                                return $row['image'];
                            },
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                ],
            ]
        ];
    }

}
