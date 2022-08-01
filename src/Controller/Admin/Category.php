<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Detail\Item\DetailItemImage;
use Be\AdminPlugin\Detail\Item\DetailItemToggleIcon;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\App\ControllerException;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("商品")
 * @BePermissionGroup("商品")
 */
class Category extends Auth
{

    /**
     * 商品分类
     *
     * @BeMenu("商品分类", icon="el-icon-folder", ordering="3.2")
     * @BePermission("商品分类", ordering="3.2")
     */
    public function categories()
    {
        Be::getAdminPlugin('Curd')->setting([

            'label' => '商品分类',
            'table' => 'shopfai_category',

            'grid' => [
                'title' => '商品分类',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'orderBy' => 'ordering',
                'orderByDir' => 'ASC',

                'form' => [
                    'items' => [
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => [
                                '1' => '启用',
                                '0' => '禁用',
                            ],
                        ],
                    ],
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建分类分类',
                            'url' => beAdminUrl('ShopFai.Category.create'),
                            'target' => 'self', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ]
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量启用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要启用吗？',
                            'ui' => [
                                'icon' => 'el-icon-check',
                                'type' => 'success',
                            ]
                        ],
                        [
                            'label' => '批量禁用',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '0',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要禁用吗？',
                            'ui' => [
                                'icon' => 'el-icon-close',
                                'type' => 'warning',
                            ]
                        ],
                        [
                            'label' => '批量删除',
                            'url' => beAdminUrl('ShopFai.Category.delete'),
                            'target' => 'ajax',
                            'confirm' => '确认要删除吗？',
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ]
                        ],
                    ]
                ],


                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'image_small',
                            'label' => '封面图片',
                            'width' => '90',
                            'driver' => TableItemImage::class,
                            'ui' => [
                                'style' => 'max-width: 60px; max-height: 60px'
                            ],
                            'value' => function($row) {
                                if ($row['image_small'] === '') {
                                    return Be::getProperty('App.ShopFai')->getWwwUrl() . '/images/category/no-image-s.jpg';
                                }
                                return $row['image_small'];
                            },
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'task' => 'detail',
                            'drawer' => [
                                'width' => '80%'
                            ],
                        ],
                        [
                            'name' => 'product_count',
                            'label' => '商品数量',
                            'align' => 'center',
                            'width' => '120',
                            'driver' => TableItemLink::class,
                            'value' => function ($row) {
                                $sql = 'SELECT COUNT(*) FROM shopfai_product_category WHERE category_id = ?';
                                $count = Be::getDb()->getValue($sql, [$row['id']]);
                                return $count;
                            },
                            'url' => beAdminUrl('ShopFai.Category.goProducts'),
                            'target' => 'self',
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                            'width' => '120',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'fieldEdit',
                            'width' => '90',
                            'exportValue' => function ($row) {
                                return $row['is_enable'] ? '启用' : '禁用';
                            },
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '编辑',
                                'url' => beAdminUrl('ShopFai.Category.edit'),
                                'target' => 'self',
                                'ui' => [
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-edit',
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'url' => beAdminUrl('ShopFai.Category.delete'),
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-delete',
                            ],
                        ]
                    ],
                ],
            ],

            'detail' => [
                'title' => '分类分类详情',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'image_small',
                            'label' => '封面图片',
                            'driver' => DetailItemImage::class,
                            'value' => function($row) {
                                if ($row['image_small'] === '') {
                                    return Be::getProperty('App.ShopFai')->getWwwUrl() . '/images/category/no-image-s.jpg';
                                }
                                return $row['image_small'];
                            },
                            'ui' => [
                                'style' => 'max-width: 128px;',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'description',
                            'label' => '描述',
                            'driver' => DetailItemHtml::class,
                        ],
                        [
                            'name' => 'url',
                            'label' => '网址',
                            'value' => function($row) {
                                return beUrl() . '/category/' . $row['url'];
                            }
                        ],
                        [
                            'name' => 'seo',
                            'label' => 'SEO 独立编辑',
                            'driver' => DetailItemToggleIcon::class,
                        ],
                        [
                            'name' => 'seo_title',
                            'label' => 'SEO 标题',
                        ],
                        [
                            'name' => 'seo_description',
                            'label' => 'SEO 描述',
                        ],
                        [
                            'name' => 'seo_keywords',
                            'label' => 'SEO 关键词',
                        ],
                        [
                            'name' => 'ordering',
                            'label' => '排序',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => DetailItemToggleIcon::class,
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                        ],
                    ]
                ],
            ],

            'fieldEdit' => [
                'events' => [
                    'before' => function ($tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                    'success' => function () {
                        Be::getService('App.System.Task')->trigger('ShopFai.CategorySyncCache');
                    },
                ],
            ],

        ])->execute();
    }

    /**
     * 新建分类
     *
     * @BePermission("新建", ordering="3.21")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                Be::getService('App.ShopFai.Admin.Category')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建分类成功！');
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $configCategory = Be::getConfig('App.ShopFai.Category');
            $response->set('configCategory', $configCategory);

            $response->set('category', false);

            $response->set('title', '新建分类');

            //$response->display();
            $response->display('App.ShopFai.Admin.Category.edit');
        }
    }

    /**
     * 编辑
     *
     * @BePermission("编辑", ordering="3.22")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                Be::getService('App.ShopFai.Admin.Category')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑分类成功！');
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } elseif ($request->isPost()) {
            $postData = $request->post('data', '', '');
            if ($postData) {
                $postData = json_decode($postData, true);
                if (isset($postData['row']['id']) && $postData['row']['id']) {
                    $response->redirect(beAdminUrl('ShopFai.Category.edit', ['id' => $postData['row']['id']]));
                }
            }
        } else {
            $configCategory = Be::getConfig('App.ShopFai.Category');
            $response->set('configCategory', $configCategory);

            $categoryId = $request->get('id', '');
            $category = Be::getService('App.ShopFai.Admin.Category')->getCategory($categoryId);
            $response->set('category', $category);

            $response->set('title', '编辑分类');

            $response->display();
        }
    }

    /**
     * 删除
     *
     * @BePermission("删除", ordering="3.23")
     */
    public function delete()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $postData = $request->json();

            $productIds = [];
            if (isset($postData['selectedRows'])) {
                foreach ($postData['selectedRows'] as $row) {
                    $productIds[] = $row['id'];
                }
            } elseif (isset($postData['row'])) {
                $productIds[] = $postData['row']['id'];
            }

            if (count($productIds) > 0) {
                Be::getService('App.ShopFai.Admin.Category')->delete($productIds);
            }

            $response->set('success', true);
            $response->set('message', '删除成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 指定分类下的商品
     *
     * @BePermission("查看商品", ordering="3.24")
     */
    public function goProducts()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {
                $response->redirect(beAdminUrl('ShopFai.Category.products', ['id' => $postData['row']['id']]));
            }
        }
    }

    /**
     * 指定分类下的商品
     *
     * @BePermission("查看商品", ordering="3.24")
     */
    public function products()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $categoryId = $request->get('id', '');
        $category = Be::getService('App.ShopFai.Admin.Category')->getCategory($categoryId);

        $filter = [
            ['is_delete', '=', '0'],
        ];

        $db = Be::getDb();
        $productIds = $db->getValues('SELECT product_id FROM shopfai_product_category WHERE category_id=?', [$categoryId]);
        if ($productIds) {
            $filter[] = [
                'id', 'IN', $productIds
            ];
        } else {
            $filter[] = [
                'id', '=', ''
            ];
        }

        Be::getAdminPlugin('Curd')->setting([
            'label' => '商品分类 ' . $category->name . ' 下的商品',
            'table' => 'shopfai_product',
            'grid' => [
                'title' => '商品分类 ' . $category->name . ' 下的商品管理',

                'filter' => $filter,

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '返回',
                            'url' => beAdminUrl('ShopFai.Category.categories'),
                            'target' => 'self',
                            'ui' => [
                                'icon' => 'el-icon-back'
                            ]
                        ],
                        [
                            'label' => '添加商品',
                            'url' => beAdminUrl('ShopFai.Category.addProduct', ['id' => $categoryId]),
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'drawer' => [
                                'width' => '60%',
                            ],
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ]
                        ],
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量从此分类中移除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
                            'confirm' => '确认要从此分类中移除吗？',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => '1',
                            ],
                            'ui' => [
                                'icon' => 'el-icon-delete',
                                'type' => 'danger'
                            ]
                        ],
                    ]
                ],

                'table' => [

                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'image',
                            'label' => '商品图片',
                            'width' => '90',
                            'driver' => TableItemImage::class,
                            'value' => function ($row) {
                                $sql = 'SELECT large FROM shopfai_product_image WHERE product_id = ? AND is_main = 1';
                                $image = Be::getDb()->getValue($sql, [$row['id']]);
                                if ($image) {
                                    return $image;
                                } else {
                                    return Be::getProperty('App.ShopFai')->getWwwUrl() . '/images/product/no-image.jpg';
                                }
                            },
                            'ui' => [
                                'style' => 'max-width: 60px; max-height: 60px'
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => '商品名称',
                            'align' => 'left',
                        ],
                    ],

                    'operation' => [
                        'label' => '操作',
                        'width' => '150',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '从此分类中移除',
                                'url' => beAdminUrl('ShopFai.Category.deleteProduct', ['id' => $categoryId]),
                                'confirm' => '确认要从此分类中移除么？',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-delete',
                            ],
                        ]
                    ],
                ],
            ],
        ])->execute();
    }

    /**
     * 指定分类下的商品 - 添加
     *
     * @BePermission("添加商品", ordering="3.25")
     */
    public function addProduct()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $categoryId = $request->get('id', '');
        $category = Be::getService('App.ShopFai.Admin.Category')->getCategory($categoryId);

        $filter = [
            ['is_delete', '=', '0'],
        ];

        $db = Be::getDb();
        $productIds = $db->getValues('SELECT product_id FROM shopfai_product_category WHERE category_id=?', [$categoryId]);
        if ($productIds) {
            $filter[] = [
                'id', 'NOT IN', $productIds
            ];
        }

        Be::getAdminPlugin('Curd')->setting([
            'label' => '向商品分类 ' . $category->name . ' 添加商品',
            'table' => 'shopfai_product',
            'grid' => [
                'title' => '向商品分类 ' . $category->name . ' 添加商品',
                'theme' => 'Blank',

                'filter' => $filter,

                'form' => [
                    'items' => [
                        [
                            'name' => 'is_enable',
                            'value' => Be::getRequest()->request('is_enable', '-1'),
                            'driver' => FormItemSelect::class,
                            'nullValue' => '-1',
                            'keyValues' => [
                                '-1' => '全部',
                                '1' => '已上架',
                                '0' => '已下架',
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                    ],
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '添加到商品分类 ' . $category->name . ' 中',
                            'url' => beAdminUrl('ShopFai.Category.addProductSave', ['id' => $categoryId]),
                            'target' => 'ajax',
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary'
                            ]
                        ],
                    ]
                ],

                'table' => [

                    'items' => [
                        [
                            'driver' => TableItemSelection::class,
                            'width' => '50',
                        ],
                        [
                            'name' => 'image',
                            'label' => '商品图片',
                            'width' => '90',
                            'driver' => TableItemImage::class,
                            'value' => function ($row) {
                                $sql = 'SELECT large FROM shopfai_product_image WHERE product_id = ? AND is_main = 1';
                                $image = Be::getDb()->getValue($sql, [$row['id']]);
                                if ($image) {
                                    return $image;
                                } else {
                                    return Be::getProperty('App.ShopFai')->getWwwUrl() . '/images/product/no-image.jpg';
                                }
                            },
                            'ui' => [
                                'style' => 'max-width: 60px; max-height: 60px'
                            ],
                        ],
                        [
                            'name' => 'name',
                            'label' => '商品名称',
                            'align' => 'left',
                        ],
                    ],
                ],
            ],
        ])->execute();
    }


    /**
     * 指定分类下的商品 - 添加
     *
     * @BePermission("添加商品", ordering="3.25")
     */
    public function addProductSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $categoryId = $request->get('id', '');
            $selectedRows = $request->json('selectedRows');
            if (!is_array($selectedRows) || count($selectedRows) == 0) {
                throw new ControllerException('请选择商品！');
            }

            $productIds = [];
            foreach ($selectedRows as $selectedRow) {
                $productIds[] = $selectedRow['id'];
            }

            Be::getService('App.ShopFai.Admin.Category')->addProduct($categoryId, $productIds);
            $response->set('success', true);
            $response->set('message', '编辑分类成功！');
            $response->set('callback', 'parent.closeDrawerAndReload();');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 指定分类下的商品 - 删除
     *
     * @BePermission("删除商品", ordering="3.26")
     */
    public function deleteProduct()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $categoryId = $request->get('id', '');
            $productIds = [];
            $postData = $request->json();
            if (isset($postData['selectedRows'])) {
                if (is_array($postData['selectedRows']) && count($postData['selectedRows']) > 0) {
                    foreach ($postData['selectedRows'] as $selectedRow) {
                        $productIds[] = $selectedRow['id'];
                    }
                }
            } elseif (isset($postData['row'])) {
                $productIds[] = $postData['row']['id'];
            }

            if (count($productIds) == 0) {
                throw new ControllerException('请选择商品！');
            }

            Be::getService('App.ShopFai.Admin.Category')->deleteProduct($categoryId, $productIds);
            $response->set('success', true);
            $response->set('message', '编辑分类成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 分类选择器
     *
     * @BePermission("*")
     */
    public function picker()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {

            // 是否可以多选
            $multiple = $request->get('multiple', '0');
            $multiple = $multiple ? 1 : 0;

            $pickerSetting = Be::getService('App.ShopFai.Admin.Category')->getCategoryPicker($multiple);
            if ($request->isAjax()) {
                Be::getAdminPlugin('Curd')
                    ->setting($pickerSetting)
                    ->grid();
            } else {

                $response->set('title', $menuPicker['grid']['title'] ?? '');
                $response->set('url', $request->getUrl());
                $response->set('multiple', $multiple);

                $callback = $request->get('callback');
                if (!$callback) {
                    $callback = $multiple === 1 ? 'selectCategories' : 'selectCategory';
                }
                $response->set('callback', $callback);

                $excludeIds = [];
                $postData = $request->post('data', '', '');
                if ($postData) {
                    $postData = json_decode($postData, true);
                    $excludeId = $postData['exclude_ids'] ?? '';
                    if ($excludeId && is_string($excludeId)) {
                        $excludeId = trim($excludeId);
                        if ($excludeId !== '') {
                            $excludeIds = explode(',', $excludeId);
                        }
                    }
                }
                $response->set('excludeIds', $excludeIds);

                if (!isset($pickerSetting['grid']['form']['action'])) {
                    $pickerSetting['grid']['form']['action'] = $request->getUrl();
                }

                if (!isset($pickerSetting['grid']['form']['actions'])) {
                    $pickerSetting['grid']['form']['actions'] = [
                        'submit' => true,
                    ];
                }
                $response->set('setting', $pickerSetting);

                $pageSize = null;
                if (isset($pickerSetting['grid']['pageSize']) &&
                    is_numeric($pickerSetting['grid']['pageSize']) &&
                    $pickerSetting['grid']['pageSize'] > 0
                ) {
                    $pageSize = $pickerSetting['grid']['pageSize'];
                } else {
                    $pageSize = 12;;
                }
                $response->set('pageSize', $pageSize);

                $theme = $pickerSetting['grid']['theme'] ?? 'Blank';

                $response->display('App.ShopFai.Admin.Picker.picker', $theme);
            }

        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

}
