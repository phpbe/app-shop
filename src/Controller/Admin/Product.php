<?php

namespace Be\App\Shop\Controller\Admin;

use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\App\ControllerException;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;


/**
 * @BeMenuGroup("商品", icon="el-icon-goods", ordering="3")
 * @BePermissionGroup("商品",  ordering="3")
 */
class Product extends Auth
{

    /**
     * 商品管理
     *
     * @BeMenu("商品管理", icon="el-icon-goods", ordering="3.1")
     * @BePermission("商品管理", ordering="3.1")
     */
    public function products()
    {
        $configStore = Be::getConfig('App.Shop.Store');

        $categoryKeyValues = Be::getService('App.Shop.Admin.Category')->getCategoryKeyValues();

        Be::getAdminPlugin('Curd')->setting([
            'label' => '商品',
            'table' => 'shop_product',
            'grid' => [
                'title' => '商品管理',

                'filter' => [
                    ['is_delete', '=', '0'],
                    ['is_enable', '!=', '-1'],
                ],

                'tab' => [
                    'name' => 'is_enable',
                    'value' => Be::getRequest()->request('is_enable', '-1'),
                    'nullValue' => '-1',
                    'counter' => true,
                    'keyValues' => [
                        '-1' => '全部',
                        '1' => '已上架',
                        '0' => '已下架',
                    ],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'category_id',
                            'label' => '分类',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $categoryKeyValues,
                            'buildSql' => function ($dbName, $formData) {
                                if (isset($formData['category_id']) && $formData['category_id']) {
                                    $productIds = Be::getTable('shop_product_category', $dbName)
                                        ->where('category_id', $formData['category_id'])
                                        ->getValues('product_id');
                                    if (count($productIds) > 0) {
                                        return ['id', 'IN', $productIds];
                                    } else {
                                        return ['id', '=', ''];
                                    }
                                }
                                return '';
                            },
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                    ],
                ],

                'titleToolbar' => [

                    'items' => [
                        [
                            'label' => '导出',
                            'driver' => ToolbarItemDropDown::class,
                            'ui' => [
                                'icon' => 'el-icon-download',
                            ],
                            'menus' => [
                                [
                                    'label' => 'CSV',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'task' => 'export',
                                    'postData' => [
                                        'driver' => 'excel',
                                    ],
                                    'target' => 'blank',
                                ],
                            ]
                        ],
                    ]
                ],

                'titleRightToolbar' => [
                    'items' => [
                        [
                            'label' => '新建商品',
                            'url' => beAdminUrl('Shop.Product.create'),
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
                            'label' => '批量上架',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要上架吗？',
                            'ui' => [
                                'icon' => 'el-icon-check',
                                'type' => 'success',
                            ]
                        ],
                        [
                            'label' => '批量下架',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '0',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要下架吗？',
                            'ui' => [
                                'icon' => 'el-icon-close',
                                'type' => 'warning',
                            ]
                        ],
                        [
                            'label' => '批量删除',
                            'url' => beAdminUrl('Shop.Product.delete'),
                            'target' => 'ajax',
                            'confirm' => '确认要删除吗？',
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

                    // 未指定时取表的所有字段
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
                                $sql = 'SELECT url FROM shop_product_image WHERE product_id = ? AND product_item_id = \'\' AND is_main = 1';
                                $image = Be::getDb()->getValue($sql, [$row['id']]);
                                if ($image) {
                                    return $image;
                                } else {
                                    return Be::getProperty('App.Shop')->getWwwUrl() . '/images/product/no-image.jpg';
                                }
                            },
                            'ui' => [
                                'style' => 'max-width: 60px; max-height: 60px'
                            ],
                            'url' => beAdminUrl('Shop.Product.edit'),
                            'target' => 'self',
                        ],
                        [
                            'name' => 'name',
                            'label' => '商品名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'url' => beAdminUrl('Shop.Product.edit'),
                            'target' => 'self',
                        ],
                        [
                            'name' => 'spu',
                            'label' => 'SPU',
                            'width' => '120',
                            'align' => 'left',
                        ],
                        [
                            'name' => 'price_from',
                            'label' => '单价（' . $configStore->currencySymbol . '）',
                            'width' => '150',
                            'sortable' => true,
                            'value' => function ($row) {
                                if ($row['price_from'] === $row['price_to']) {
                                    return $row['price_from'];
                                } else {
                                    return $row['price_from'] . '~' . $row['price_to'];
                                }
                            },
                        ],
                        [
                            'name' => 'original_price_from',
                            'label' => '原价（' . $configStore->currencySymbol . '）',
                            'width' => '150',
                            'sortable' => true,
                            'value' => function ($row) {
                                if ($row['original_price_from'] === $row['original_price_to']) {
                                    return $row['original_price_from'];
                                } else {
                                    return $row['original_price_from'] . '~' . $row['original_price_to'];
                                }
                            },
                        ],
                        [
                            'name' => 'stock',
                            'label' => '库存',
                            'width' => '160',
                            'value' => function ($row) {
                                if ($row['stock_tracking'] === '0') {
                                    return '未跟踪库存';
                                } else {
                                    if ($row['style'] === '1') {
                                        $sql = 'SELECT SUM(stock) AS total, COUNT(*) AS n FROM shop_product_item WHERE product_id = ?';
                                        return Be::getDb()->getValue($sql, [$row['id']]);
                                    } else {
                                        $sql = 'SELECT SUM(stock) AS total, COUNT(*) AS n FROM shop_product_item WHERE product_id = ?';
                                        $result = Be::getDb()->getArray($sql, [$row['id']]);
                                        return $result['n'] . '款，共 ' . $result['total'] . ' 件';
                                    }
                                }
                            },
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '上架',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'fieldEdit',
                            'width' => '90',
                            'exportValue' => function ($row) {
                                return $row['is_enable'] ? '上架' : '下架';
                            },
                        ],
                    ],

                    'operation' => [
                        'label' => '操作',
                        'width' => '150',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '预览',
                                'url' => beAdminUrl('Shop.Product.preview'),
                                'target' => '_blank',
                                'ui' => [
                                    'type' => 'success',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-view',
                            ],
                            [
                                'label' => '',
                                'tooltip' => '编辑',
                                'url' => beAdminUrl('Shop.Product.edit'),
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
                                'url' => beAdminUrl('Shop.Product.delete'),
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'postData' => [
                                    'field' => 'is_delete',
                                    'value' => 1,
                                ],
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

            'fieldEdit' => [
                'events' => [
                    'before' => function ($tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                    'success' => function () {
                        Be::getService('App.System.Task')->trigger('Shop.ProductSyncEsAndCache');
                    },
                ],
            ],

            'export' => [],

        ])->execute();
    }

    /**
     * 新建商品
     *
     * @BePermission("新建", ordering="3.11")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                Be::getService('App.Shop.Admin.Product')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建商品成功！');
                $response->set('redirectUrl', beAdminUrl('Shop.Product.products'));
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $configStore = Be::getConfig('App.Shop.Store');
            $response->set('configStore', $configStore);

            $configProduct = Be::getConfig('App.Shop.Product');
            $response->set('configProduct', $configProduct);

            $response->set('product', false);

            $categoryKeyValues = Be::getService('App.Shop.Admin.Category')->getCategoryKeyValues();
            $response->set('categoryKeyValues', $categoryKeyValues);

            $response->set('backUrl', beAdminUrl('Shop.Product.products'));
            $response->set('formActionUrl', beAdminUrl('Shop.Product.create'));

            $response->set('title', '新建商品');

            //$response->display();
            $response->display('App.Shop.Admin.Product.edit');
        }
    }

    /**
     * 编辑
     *
     * @BePermission("编辑", ordering="3.12")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            try {
                Be::getService('App.Shop.Admin.Product')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑商品成功！');
                $response->set('redirectUrl', beAdminUrl('Shop.Product.products'));
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
                    $response->redirect(beAdminUrl('Shop.Product.edit', ['id' => $postData['row']['id']]));
                }
            }
        } else {
            $configStore = Be::getConfig('App.Shop.Store');
            $response->set('configStore', $configStore);

            $configProduct = Be::getConfig('App.Shop.Product');
            $response->set('configProduct', $configProduct);

            $productId = $request->get('id', '');
            $product = Be::getService('App.Shop.Admin.Product')->getProduct($productId, [
                'relate' => 1,
                'images' => 1,
                'categories' => 1,
                'tags' => 1,
                'styles' => 1,
                'items' => 1,
            ]);

            $response->set('product', $product);

            $categoryKeyValues = Be::getService('App.Shop.Admin.Category')->getCategoryKeyValues();
            $response->set('categoryKeyValues', $categoryKeyValues);

            $response->set('backUrl', beAdminUrl('Shop.Product.products'));
            $response->set('formActionUrl', beAdminUrl('Shop.Product.edit'));

            $response->set('title', '编辑商品');

            $response->display();
        }
    }

    /**
     * 删除
     *
     * @BePermission("删除", ordering="3.13")
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
                Be::getService('App.Shop.Admin.Product')->delete($productIds);
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
     * 添加商品关联
     *
     * @BePermission("添加关联", ordering="3.14")
     */
    public function relate()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            $postData = $request->json();
            $formData = $postData['formData'];

            $excludeIds = $formData['exclude_product_ids'] ?? '';
            $related = $formData['related'] ?? 0;

            $db = Be::getDb();

            $table = Be::getTable('shop_product');
            $table->where('is_delete', 0);

            if ($excludeIds !== '') {
                $excludeIds = explode(',', $excludeIds);
                $table->where('id', 'NOT IN', $excludeIds);
            }

            if (isset($formData['is_enable']) && in_array($formData['is_enable'], ['1', '0'])) {
                $table->where('is_enable', $formData['is_enable']);
            }

            if (isset($formData['name']) && is_string($formData['name'])) {
                $table->where('name', 'LIKE', $formData['name'] . '%');
            }

            if ($related) {
                $table->where('relate_id', '!=', '');
            } else {
                $table->where('relate_id', '=', '');
            }

            $total = $table->count();

            $page = $postData['page'];
            $pageSize = $postData['pageSize'];
            $table->offset(($page - 1) * $pageSize)->limit($pageSize);

            $rows = $table->getArrays();

            $formattedRows = [];
            foreach ($rows as $row) {
                $image = '';
                try {
                    $tuple = Be::getTuple('shop_product_image');
                    $tuple->loadBy([
                        'product_id' => $row['id'],
                        'product_item_id' => '',
                        'is_main' => 1
                    ]);
                    $image = $tuple->url;
                } catch (\Throwable $t) {
                }

                $relate = [];
                if ($row['relate_id'] !== '') {
                    $tupleRelate = Be::getTuple('shop_product_relate');
                    $tupleRelate->load($row['relate_id']);
                    $relate = $tupleRelate->toArray();

                    $tableRelateItem = Be::getTable('shop_product_relate_item');
                    $tableRelateItem->where('relate_id', $row['relate_id']);
                    $relateItems = $tableRelateItem->getArrays('product_id, value, icon_image, icon_color');
                    foreach ($relateItems as &$relateItem) {
                        $relateItem['product_name'] = $db->getValue('SELECT `name` FROM shop_product WHERE id=?', [$relateItem['product_id']]);
                    }
                    unset($relateItem);
                    $relate['items'] = $relateItems;
                }

                $formattedRow = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'is_enable' => $row['is_enable'],
                    'image' => $image ?? '',
                    'relate_id' => $row['relate_id'],
                    'relate' => $relate,
                ];
                $formattedRows[] = $formattedRow;
            }

            $response->set('success', true);
            $response->set('data', [
                'total' => $total,
                'gridData' => $formattedRows,
            ]);
            $response->json();
        } else {
            $response->set('url', $request->getUrl());
            $response->set('pageSize', 10);

            $response->set('excludeIds', $request->get('exclude_product_ids', ''));

            $response->display(null, 'Blank');
        }
    }

    /**
     * 预览
     *
     * @BePermission("预览", ordering="3.15")
     */
    public function preview()
    {
        $request = Be::getRequest();
        $data = $request->post('data', '', '');
        $data = json_decode($data, true);
        Be::getResponse()->redirect(beUrl('Shop.Product.detail', ['id' => $data['row']['id']]));
    }

    /**
     * 商品选择器
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

            $pickerSetting = Be::getService('App.Shop.Admin.Product')->getProductPicker($multiple);
            if ($request->isAjax()) {
                Be::getAdminPlugin('Curd')
                    ->setting($pickerSetting)
                    ->grid();
            } else {

                $response->set('title', $pickerSetting['grid']['title'] ?? '');
                $response->set('url', $request->getUrl());
                $response->set('multiple', $multiple);

                $callback = $request->get('callback');
                if (!$callback) {
                    $callback = $multiple === 1 ? 'selectProducts' : 'selectProduct';
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

                $response->display('App.Shop.Admin.Picker.picker', $theme);
            }

        } catch (\Throwable $t) {
            $response->error($t->getMessage());
        }
    }

}
