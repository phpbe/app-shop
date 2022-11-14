<?php

namespace Be\App\Shop\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemInput;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemLink;
use Be\App\System\Controller\Admin\Auth;
use Be\Db\Tuple;
use Be\AdminPlugin\Form\Item\FormItemAutoComplete;
use Be\AdminPlugin\Form\Item\FormItemDateTimePicker;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\Be;


/**
 * @BeMenuGroup("订单", icon="el-icon-s-order", ordering="2")
 * @BePermissionGroup("订单",  ordering="2")
 */
class Order extends Auth
{

    /**
     * 订单管理
     *
     * @BeMenu("订单管理", icon="el-icon-s-order", ordering="2.1")
     * @BePermission("订单管理", ordering="2.1")
     */
    public function orders()
    {
        $statusKeyValues = Be::getService('App.Shop.Admin.Order')->getStatusKeyValues();

        Be::getAdminPlugin('Curd')->setting([
            'label' => '订单',
            'table' => 'shop_order',
            'grid' => [
                'title' => '订单管理',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'tab' => [
                    'name' => 'status',
                    'value' => Be::getRequest()->request('status', 'ALL'),
                    'nullValue' => 'ALL',
                    'keyValues' => array_merge(['ALL' => '全部',], $statusKeyValues),
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'order_sn',
                            'label' => '编号',
                        ],
                        [
                            'name' => 'user_name',
                            'label' => '用户',
                            'driver' => FormItemAutoComplete::class,
                            'remote' => beAdminUrl('Shop.User.autoCompleteSuggestions'),
                            'buildSql' => function ($dbName, $formData) {
                                $db = Be::getDb($dbName);
                                if (isset($formData['user_name']) && $formData['user_name']) {
                                    $sql = 'SELECT id FROM shop_user WHERE `name` LIKE ' . $db->quoteValue('%' . $formData['user_name'] . '%') . '  AND is_enable = 1 AND is_delete = 0';
                                    $id = $db->getValue($sql);
                                    if ($id) {
                                        return 'user_id = ' . $id;
                                    }
                                }
                                return '';
                            }
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
                                    'url' => beAdminUrl('Shop.Order.orders', ['task' => 'export']),
                                    'postData' => [
                                        'driver' => 'csv',
                                    ],
                                    'target' => 'blank',
                                ],
                                [
                                    'label' => 'EXCEL',
                                    'url' => beAdminUrl('Shop.Order.orders', ['task' => 'export']),
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
                            'label' => '删除',
                            'task' => 'fieldEdit',
                            'target' => 'ajax',
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
                            'name' => 'user_name',
                            'label' => '用户',
                            'value' => function ($row) {
                                if ($row['user_id'] > 0) {
                                    $sql = 'SELECT `name` FROM shop_user WHERE id = ?';
                                    $name = Be::getDb()->getValue($sql, [$row['user_id']]);
                                    if ($name) {
                                        return $name;
                                    }
                                }
                                return '';
                            },
                            'width' => '120',
                        ],
                        [
                            'name' => 'order_sn',
                            'label' => '订单编号',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'amount',
                            'label' => '金额',
                            'width' => '80',
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'width' => '150',
                            'keyValues' => $statusKeyValues,
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '180',
                            'sortable' => true,
                        ],
                    ],
                    'exclude' => ['password', 'salt'],

                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '编辑',
                                'task' => 'edit',
                                'target' => 'drawer',
                                'ui' => [
                                    'type' => 'primary'
                                ]
                            ],
                            [
                                'label' => '删除',
                                'task' => 'fieldEdit',
                                'confirm' => '确认要删除么？',
                                'target' => 'ajax',
                                'postData' => [
                                    'field' => 'is_delete',
                                    'value' => 1,
                                ],
                                'ui' => [
                                    'type' => 'danger'
                                ]
                            ],
                        ]
                    ],
                ],
            ],

            'detail' => [
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'id',
                            'label' => 'ID',
                        ],
                        [
                            'name' => 'user_name',
                            'label' => '用户',
                            'value' => function ($row) {
                                if ($row['user_id'] > 0) {
                                    $sql = 'SELECT `name` FROM shop_user WHERE id = ?';
                                    $name = Be::getDb()->getValue($sql, [$row['user_id']]);
                                    if ($name) {
                                        return $name;
                                    }
                                }
                                return '';
                            },
                        ],
                        [
                            'name' => 'order_sn',
                            'label' => '订单编号',
                        ],
                        [
                            'name' => 'product_amount',
                            'label' => '商品总额',
                        ],
                        [
                            'name' => 'shipping_fee',
                            'label' => '运费',
                        ],
                        [
                            'name' => 'amount',
                            'label' => '订单金额',
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'keyValues' => $statusKeyValues,
                        ],
                        [
                            'name' => 'address',
                            'label' => '地址',
                            'value' => function ($row) {
                                return $row['country'] . $row['province'] . $row['city'] . $row['district'] . $row['address'];
                            },
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
                        ],
                        [
                            'name' => 'remarks',
                            'label' => '备注',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'pay_time',
                            'label' => '付款时间',
                        ],
                        [
                            'name' => 'pay_expire_time',
                            'label' => '创建时间',
                        ],
                        [
                            'name' => 'cancel_time',
                            'label' => '取消时间',
                        ],
                        [
                            'name' => 'ship_time',
                            'label' => '发货时间',
                        ],
                        [
                            'name' => 'receive_time',
                            'label' => '收货时间',
                        ],
                    ]
                ],
            ],

            'edit' => [
                'title' => '编辑订单',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'user_id',
                            'label' => '用户',
                            'value' => function ($row) {
                                if ($row['user_id'] > 0) {
                                    $sql = 'SELECT `name` FROM shop_user WHERE id = ?';
                                    $name = Be::getDb()->getValue($sql, [$row['user_id']]);
                                    if ($name) {
                                        return $name;
                                    }
                                }
                                return '';
                            },
                            'disabled' => true,
                        ],
                        [
                            'name' => 'order_sn',
                            'label' => '订单编号',
                            'disabled' => true,
                        ],
                        [
                            'name' => 'product_amount',
                            'label' => '商品总额',
                        ],
                        [
                            'name' => 'shipping_fee',
                            'label' => '运费',
                        ],
                        [
                            'name' => 'amount',
                            'label' => '订单金额',
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'driver' => FormItemSelect::class,
                            'keyValues' => $statusKeyValues,
                        ],
                        [
                            'name' => 'country',
                            'label' => '国家',
                        ],
                        [
                            'name' => 'province',
                            'label' => '省',
                        ],
                        [
                            'name' => 'city',
                            'label' => '市',
                        ],
                        [
                            'name' => 'district',
                            'label' => '区',
                        ],
                        [
                            'name' => 'address',
                            'label' => '地址',
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
                        ],
                        [
                            'name' => 'remarks',
                            'label' => '备注',
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'driver' => FormItemDateTimePicker::class,
                        ],
                        [
                            'name' => 'pay_time',
                            'label' => '付款时间',
                            'driver' => FormItemDateTimePicker::class,
                        ],
                        [
                            'name' => 'pay_expire_time',
                            'label' => '创建时间',
                            'driver' => FormItemDateTimePicker::class,
                        ],
                        [
                            'name' => 'cancel_time',
                            'label' => '取消时间',
                            'driver' => FormItemDateTimePicker::class,
                        ],
                        [
                            'name' => 'ship_time',
                            'label' => '发货时间',
                            'driver' => FormItemDateTimePicker::class,
                        ],
                        [
                            'name' => 'receive_time',
                            'label' => '收货时间',
                            'driver' => FormItemDateTimePicker::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    }
                ]
            ],

            'fieldEdit' => [
                'events' => [
                    'before' => function ($tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'export' => [],

        ])->execute();
    }

}
