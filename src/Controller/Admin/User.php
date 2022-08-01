<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\AdminPlugin\Toolbar\Item\ToolbarItemDropDown;
use Be\App\System\Controller\Admin\Auth;
use Be\Db\Tuple;
use Be\Util\Crypt\Random;
use Be\AdminPlugin\Toolbar\Item\ToolbarItemButtonDropDown;
use Be\AdminPlugin\Detail\Item\DetailItemSwitch;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Form\Item\FormItemSwitch;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\Be;


/**
 * @BeMenuGroup("客户", icon="el-icon-user", ordering="4")
 * @BePermissionGroup("客户",  ordering="4")
 */
class User extends Auth
{

    /**
     * 客户管理
     *
     * @BeMenu("客户管理", icon="el-icon-user", ordering="4.1")
     * @BePermission("客户管理", ordering="4.1")
     */
    public function users()
    {
        Be::getAdminPlugin('Curd')->setting([
            'label' => '客户管理',
            'table' => 'shopfai_user',
            'grid' => [
                'title' => '客户管理',

                'filter' => [
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'email',
                            'label' => '邮箱',
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
                        [
                            'label' => '新建客户',
                            'task' => 'create',
                            'target' => 'drawer', // 'ajax - ajax请求 / dialog - 对话框窗口 / drawer - 抽屉 / self - 当前页面 / blank - 新页面'
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
                            'task' => 'fieldEdit',
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
                            'name' => 'email',
                            'label' => '邮箱',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'value' => function($row) {
                                return $row['first_name'] . ' ' . $row['last_name'];
                            }
                        ],
                        [
                            'name' => 'create_time',
                            'label' => '创建时间',
                            'width' => '150',
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
                            'name' => 'email',
                            'label' => '邮箱',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'value' => function($row) {
                                return $row['first_name'] . ' ' . $row['last_name'];
                            }
                        ],
                        [
                            'name' => 'mobile',
                            'label' => '手机',
                        ],
                        [
                            'name' => 'gender',
                            'label' => '性别',
                            'value' => function ($row) {
                                switch ($row['gender']) {
                                    case '-1':
                                        return '保密';
                                    case '0':
                                        return '女';
                                    case '1':
                                        return '男';
                                }
                                return '';
                            },
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
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
                        [
                            'name' => 'last_login_time',
                            'label' => '上次登陆时间',
                        ],
                        [
                            'name' => 'last_login_ip',
                            'label' => '上次登录的IP',
                        ],
                        [
                            'name' => 'this_login_time',
                            'label' => '本次登陆时间',
                        ],
                        [
                            'name' => 'this_login_ip',
                            'label' => '本次登录的IP',
                        ],
                    ]
                ],
            ],

            'create' => [
                'title' => '新建客户',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'mobile',
                            'label' => '手机',
                            'unique' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'password',
                            'label' => '密码',
                            'required' => true,
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                            'unique' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
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
                        $tuple->salt = Random::complex(32);
                        $tuple->password = Be::getService('System.User')->encryptPassword($tuple->password, $tuple->salt);
                        $tuple->create_time = date('Y-m-d H:i:s');
                        $tuple->update_time = date('Y-m-d H:i:s');
                    },
                ],
            ],

            'edit' => [
                'title' => '编辑客户',
                'theme' => 'Blank',
                'form' => [
                    'items' => [
                        [
                            'name' => 'mobile',
                            'label' => '手机号',
                            'disabled' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'password',
                            'label' => '密码',
                            'value' => '',
                        ],
                        [
                            'name' => 'name',
                            'label' => '名称',
                            'required' => true,
                        ],
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                            'disabled' => true,
                            'required' => true,
                        ],
                        [
                            'name' => 'phone',
                            'label' => '电话',
                        ],
                        [
                            'name' => 'is_enable',
                            'label' => '启用/禁用',
                            'driver' => FormItemSwitch::class,
                        ],
                    ]
                ],
                'events' => [
                    'before' => function (Tuple &$tuple) {
                        if ($tuple->password != '') {
                            $tuple->salt = Random::complex(32);
                            $tuple->password = Be::getService('System.User')->encryptPassword($tuple->password, $tuple->salt);
                        } else {
                            unset($tuple->password);
                        }
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

    /**
     * 自动完成
     *
     * @BePermission("*")
     */
    public function autoCompleteSuggestions() {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $db = Be::getDb();

        $keywords = $request->json('keywords');
        $sql = 'SELECT `name` AS `value` FROM `shopfai_user` WHERE `name` LIKE '. $db->quoteValue('%'.$keywords.'%') . ' AND is_enable = 1 AND is_delete = 0 LIMIT 20';
        $suggestions = $db->getObjects($sql);

        $response->set('success', true);
        $response->set('data', ['suggestions' => $suggestions]);
        $response->json();
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

            $pickerSetting = Be::getService('App.ShopFai.Admin.User')->getUserPicker($multiple);
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
                    $callback = $multiple === 1 ? 'selectUsers' : 'selectUser';
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
