<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\AdminPlugin\Table\Item\TableItemToggleIcon;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * @BeMenuGroup("商品")
 * @BePermissionGroup("商品")
 */
class CollectProduct extends Auth
{

    /**
     * 采集的商品
     *
     * @BeMenu("采集的商品", icon="el-icon-goods", ordering="3.4")
     * @BePermission("采集的商品", ordering="3.4")
     */
    public function products()
    {
        $configStore = Be::getConfig('App.ShopFai.Store');

        Be::getAdminPlugin('Curd')->setting([
            'label' => '采集的商品',
            'table' => 'shopfai_product',
            'grid' => [
                'title' => '采集的商品管理',

                'filter' => [
                    ['collect_product_id', '!=', ''],
                ],

                'tab' => [
                    'name' => 'status',
                    'value' => Be::getRequest()->request('status', '-1'),
                    'nullValue' => '-1',
                    'keyValues' => [
                        '-1' => '全部',
                        '0' => '未导入',
                        '1' => '已导入',
                    ],
                    'counter' => true,
                    'buildSql' => function ($dbName, $formData) {
                        if (isset($formData['status'])) {
                            if ($formData['status'] === '0') {
                                return ['is_enable', '=', '-1'];
                            } elseif ($formData['status'] === '1') {
                                return ['is_enable', '!=', '-1'];
                            }
                        }
                        return '';
                    },
                ],

                'form' => [
                    'items' => [
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

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量导入',
                            'url' => beAdminUrl('ShopFai.CollectProduct.import'),
                            'drawer' => [
                                'title' => '批量导入',
                                'width' => '80%'
                            ],
                            'ui' => [
                                'icon' => 'el-icon-upload2',
                                'type' => 'success',
                            ],
                        ],
                        [
                            'label' => '批量删除',
                            'url' => beAdminUrl('ShopFai.CollectProduct.delete'),
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
                                'style' => 'max-width: 60px; max-height: 60px',
                                ':disabled' => 'scope.row.is_enable !== \'-1\'',
                            ],
                            'url' => beAdminUrl('ShopFai.CollectProduct.edit'),
                            'target' => 'self',
                        ],
                        [
                            'name' => 'name',
                            'label' => '商品名称',
                            'driver' => TableItemLink::class,
                            'align' => 'left',
                            'url' => beAdminUrl('ShopFai.CollectProduct.edit'),
                            'target' => 'self',
                            'ui' => [
                                ':disabled' => 'scope.row.is_enable !== \'-1\'',
                            ],
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
                            'name' => 'status',
                            'label' => '是否已导入',
                            'driver' => TableItemToggleIcon::class,
                            'width' => '90',
                            'value' => function ($row) {
                                return $row['is_enable'] === '-1' ? '0' : '1';
                            },
                            'exportValue' => function ($row) {
                                return $row['is_enable'] === '-1' ? '未导入' : '已导入';
                            },
                        ],
                        [
                            'name' => 'update_time',
                            'label' => '更新时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],

                    'operation' => [
                        'label' => '操作',
                        'width' => '150',
                        'items' => [
                            [
                                'label' => '',
                                'tooltip' => '导入',
                                'url' => beAdminUrl('ShopFai.CollectProduct.import'),
                                'drawer' => [
                                    'title' => '导入',
                                    'width' => '80%'
                                ],
                                'ui' => [
                                    'type' => 'warning',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                    'icon' => 'el-icon-upload2',
                                    ':disabled' => 'scope.row.is_enable !== \'-1\'',
                                ],
                            ],
                            [
                                'label' => '',
                                'tooltip' => '编辑',
                                'url' => beAdminUrl('ShopFai.CollectProduct.edit'),
                                'target' => 'self',
                                'ui' => [
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                    'icon' => 'el-icon-edit',
                                    ':disabled' => 'scope.row.is_enable !== \'-1\'',
                                ],
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'url' => beAdminUrl('ShopFai.CollectProduct.delete'),
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'ui' => [
                                    'type' => 'danger',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                    'icon' => 'el-icon-delete',
                                ],
                            ],
                        ]
                    ],
                ],
            ],

            'export' => [],

        ])->execute();
    }

    /**
     * 编辑
     *
     * @BePermission("编辑", ordering="3.41")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        if ($request->isAjax()) {
            try {
                Be::getService('App.ShopFai.Admin.Product')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑商品成功！');
                $response->set('redirectUrl', beAdminUrl('ShopFai.CollectProduct.products'));
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
                    $response->redirect(beAdminUrl('ShopFai.CollectProduct.edit', ['id' => $postData['row']['id']]));
                }
            }
        } else {
            $configStore = Be::getConfig('App.ShopFai.Store');
            $response->set('configStore', $configStore);

            $configProduct = Be::getConfig('App.ShopFai.Product');
            $response->set('configProduct', $configProduct);

            $productId = $request->get('id', '');
            $product = Be::getService('App.ShopFai.Admin.Product')->getProduct($productId, [
                'relate' => 1,
                'images' => 1,
                'categories' => 1,
                'tags' => 1,
                'styles' => 1,
                'items' => 1,
            ]);
            $response->set('product', $product);

            if ($product->is_enable !== -1) {
                $response->error('已导入的商品禁止编辑！');
                return;
            }

            $categoryKeyValues = Be::getService('App.ShopFai.Admin.Category')->getCategoryKeyValues();
            $response->set('categoryKeyValues', $categoryKeyValues);

            $response->set('backUrl', beAdminUrl('ShopFai.CollectProduct.products'));
            $response->set('formActionUrl', beAdminUrl('ShopFai.CollectProduct.edit'));

            $response->set('title', '编辑采集的商品');

            $response->display('App.ShopFai.Admin.Product.edit');
        }
    }

    /**
     * 导入
     *
     * @BePermission("导入", ordering="3.42")
     */
    public function import()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $data = $request->post('data', '', '');
        $data = json_decode($data, true);

        $collectProducts = [];
        if (isset($data['row'])) {
            $collectProducts[] = $data['row'];
        } elseif (isset($data['selectedRows'])) {
            $collectProducts = $data['selectedRows'];
        }

        if (count($collectProducts) === 0) {
            $response->error('您未选择采集的商品！');
            return;
        }

        $response->set('title', '导入');
        $response->set('collectProducts', $collectProducts);

        $categoryKeyValues = Be::getService('App.ShopFai.Admin.Category')->getCategoryKeyValues();
        $response->set('categoryKeyValues', $categoryKeyValues);

        $response->display(null, 'Blank');
    }

    /**
     * 导入
     *
     * @BePermission("导入", ordering="3.42")
     */
    public function importSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $formData = $request->json('formData');
            $collectProducts = $formData['collectProducts'];
            Be::getService('App.ShopFai.Admin.CollectProduct')->import($collectProducts);
            $response->set('success', true);
            $response->set('message', '导入成功！');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 删除
     *
     * @BePermission("删除", ordering="3.43")
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
                Be::getService('App.ShopFai.Admin.CollectProduct')->delete($productIds);
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

}
