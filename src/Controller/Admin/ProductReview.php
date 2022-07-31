<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\AdminPlugin\Form\Item\FormItemTinymce;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\App\System\Controller\Admin\Auth;
use Be\Db\Tuple;
use Be\AdminPlugin\Form\Item\FormItemInputNumberInt;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemButtonDropDown;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\Be;


/**
 * @BeMenuGroup("商品")
 * @BePermissionGroup("商品")
 */
class ProductReview extends Auth
{

    /**
     * 商品评论
     *
     * @BeMenu("商品评论", icon="el-icon-chat-line-square", ordering="3.3")
     * @BePermission("商品评论", ordering="3.3")
     */
    public function reviews()
    {
        Be::getAdminPlugin('Curd')->setting([

            'label' => '商品评论',
            'table' => 'shopfai_product_review',

            'grid' => [
                'title' => '商品评论',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

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
                        /*
                        [
                            'label' => '新建商品评论',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
                            'ui' => [
                                'icon' => 'el-icon-plus',
                                'type' => 'primary',
                            ]
                        ],
                        */
                    ]
                ],

                'tableToolbar' => [
                    'items' => [
                        [
                            'label' => '批量公开',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '1',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要公开吗？',
                            'ui' => [
                                'icon' => 'el-icon-fa fa-check',
                                'type' => 'success',
                            ]
                        ],
                        [
                            'label' => '批量隐藏',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_enable',
                                'value' => '0',
                            ],
                            'target' => 'ajax',
                            'confirm' => '确认要隐藏吗？',
                            'ui' => [
                                'icon' => 'el-icon-fa fa-lock',
                                'type' => 'warning',
                            ]
                        ],
                        [
                            'label' => '批量删除',
                            'task' => 'fieldEdit',
                            'postData' => [
                                'field' => 'is_delete',
                                'value' => '1',
                            ],
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
                            'name' => 'id',
                            'label' => 'ID',
                            'width' => '60',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'rating',
                            'label' => '评分',
                            'width' => '80',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '150',
                            'sortable' => true,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '公开/屏蔽',
                            'driver' => TableItemSwitch::class,
                            'target' => 'ajax',
                            'task' => 'fieldEdit',
                            'width' => '90',
                            'exportValue' => function ($row) {
                                return $row['is_enable'] ? '公开' : '屏蔽';
                            },
                        ],
                    ],
                    'operation' => [
                        'label' => '操作',
                        'width' => '120',
                        'items' => [
                            [
                                'label' => '编辑',
                                'task' => 'edit',
                                'target' => 'drawer',
                                'ui' => [
                                    'type' => 'success'
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
                            'name' => 'name',
                            'label' => '名称',
                        ],
                        [
                            'name' => 'rating',
                            'label' => '评分',
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => DetailItemSwitch::class,
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

            'create' => [
                'title' => '新建商品评论',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'product_id',
                            'label' => '商品',
                            'required' => true,
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'rating',
                            'label' => '评分',
                            'required' => true,
                            'driver' => FormItemInputNumberInt::class,
                            'ui' => [
                                ':min' => '1',
                                ':max' => '5'
                            ],
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                            'required' => true,
                            'driver' => FormItemTinymce::class,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'value' => 1,
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->create_time = date('Y-m-d H:i:s');
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'edit' => [
                'title' => '编辑商品评论',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'product_id',
                            'label' => '商品',
                            'required' => true,
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'rating',
                            'label' => '评分',
                            'required' => true,
                            'driver' => FormItemInputNumberInt::class,
                            'ui' => [
                                ':min' => '1',
                                ':max' => '5'
                            ],
                        ],
                        [
                            'name' => 'content',
                            'label' => '内容',
                            'required' => true,
                            'driver' => FormItemTinymce::class,
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '公开/屏蔽',
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
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
