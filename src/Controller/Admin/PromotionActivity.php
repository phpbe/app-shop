<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\AdminPlugin\Detail\Item\DetailItemHtml;
use Be\AdminPlugin\Table\Item\TableItemCustom;
use Be\AdminPlugin\Table\Item\TableItemLink;
use Be\App\ControllerException;
use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemSelection;
use Be\AdminPlugin\Table\Item\TableItemSwitch;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 满减活动
 *
 * @BeMenuGroup("营销", icon="el-icon-discount", ordering="5")
 * @BePermissionGroup("营销",  ordering="5")
 */
class PromotionActivity extends Auth
{

    /**
     * 满减活动
     *
     * @BeMenu("满减活动", icon="el-icon-discount", ordering="5.2")
     * @BePermission("满减活动", ordering="5.2")
     */
    public function activities()
    {
        $serviceStore = Be::getService('App.ShopFai.Admin.Store');

        Be::getAdminPlugin('Curd')->setting([

            'label' => '满减活动',
            'table' => 'shopfai_promotion_activity',

            'grid' => [
                'title' => '满减活动',

                'filter' => [
                    ['is_delete', '=', 0],
                ],

                'tab' => [
                    'name' => 'status',
                    'value' => Be::getRequest()->request('status', '-1'),
                    'nullValue' => '-1',
                    'counter' => true,
                    'keyValues' => [
                        '-1' => '全部',
                        '1' => '生效中',
                        '2' => '待生效',
                        '3' => '已失效',
                    ],
                    'buildSql' => function ($dbName, $formData) {
                        if (isset($formData['status'])) {
                            $now = date('Y-m-d H:i:s');
                            if ($formData['status'] === '1') {
                                return [
                                    ['is_enable', '=', 1],
                                    'AND',
                                    ['start_time', '<', $now],
                                    'AND',
                                    ['end_time', '>', $now],
                                ];
                            } elseif ($formData['status'] === '2') {
                                return [
                                    ['is_enable', '=', 1],
                                    'AND',
                                    ['start_time', '>', $now],
                                ];
                            } elseif ($formData['status'] === '3') {
                                return [
                                    ['is_enable', '=', 0],
                                    'OR',
                                    ['end_time', '<', $now],
                                ];
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
                            'label' => '创建满减活动',
                            'url' => beAdminUrl('ShopFai.PromotionActivity.create'),
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
                            'url' => beAdminUrl('ShopFai.PromotionActivity.delete'),
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
                            'name' => 'discount',
                            'label' => '优惠规则',
                            'align' => 'left',
                            'driver' => TableItemCustom::class,
                            'value' => function ($row) {

                                $configStore = Be::getConfig('App.ShopFai.Store');

                                $discounts = Be::getTable('shopfai_promotion_activity_discount')
                                    ->where('promotion_activity_id', $row['id'])
                                    ->orderBy('ordering', 'ASC')
                                    ->getObjects();

                                $html = '';
                                foreach ($discounts as $discount) {
                                    if ($html === '') {
                                        $html .= '<div>';
                                    } else {
                                        $html .= '<div class="be-mt-50">';
                                    }

                                    if ($row['condition'] === 'min_amount') {
                                        $html .= '满 ' . $configStore->currencySymbol . $discount->min_amount . ' 减 ';
                                    } else {
                                        $html .= '满 ' . $discount->min_quantity . ' 件减 ';
                                    }

                                    if ($row['discount_type'] === 'percent') {
                                        $html .= $discount->discount_percent . '%';
                                    } else {
                                        $html .= $configStore->currencySymbol .$discount->discount_amount;
                                    }

                                    $html .= '</div>';
                                }
                                return $html;
                            },
                        ],
                        [
                            'name' => 'time',
                            'label' => '生效时间',
                            'width' => '180',
                            'driver' => TableItemCustom::class,
                            'value' => function ($row) use ($serviceStore) {
                                if ($row['never_expire'] === '1') {
                                    return $serviceStore->systemTime2StoreTime($row['start_time']);
                                } else {
                                    return $serviceStore->systemTime2StoreTime($row['start_time']) . '<br>' . $serviceStore->systemTime2StoreTime($row['end_time']);
                                }
                            },
                        ],
                        [
                            'name' => 'used_times',
                            'label' => '已使用',
                            'width' => '90',
                            'value' => function ($row) {
                                return Be::getTable('shopfai_order_promotion')
                                    ->where('promotion_type', 'promotion_activity')
                                    ->where('promotion_id', $row['id'])
                                    ->count();
                            },
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'width' => '120',
                            'driver' => TableItemCustom::class,
                            'value' => function ($row) {
                                if ($row['is_enable'] === '0') {
                                    return '<span class="el-tag el-tag--info el-tag--light">已禁用</span>';
                                } else {
                                    $t0 = time();
                                    $t1 = strtotime($row['start_time']);
                                    $t2 = strtotime($row['end_time']);
                                    if ($t2 < $t0) {
                                        return '<span class="el-tag el-tag--info el-tag--light">已失效</span>';
                                    } else if ($t1 > $t0) {
                                        return '<span class="el-tag el-tag--primary el-tag--light">未生效</span>';
                                    } else {
                                        return '<span class="el-tag el-tag--success el-tag--light">生效中</span>';
                                    }
                                }
                            },
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
                                'url' => beAdminUrl('ShopFai.PromotionActivity.edit'),
                                'target' => 'self',
                                'ui' => [
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-edit',
                            ],
                            [
                                'label' => '',
                                'tooltip' => '统计',
                                'url' => beAdminUrl('ShopFai.PromotionActivity.statistics'),
                                'target' => 'self',
                                'ui' => [
                                    'type' => 'success',
                                    ':underline' => 'false',
                                    'style' => 'font-size: 20px;',
                                ],
                                'icon' => 'el-icon-monitor',
                            ],
                            [
                                'label' => '',
                                'tooltip' => '删除',
                                'url' => beAdminUrl('ShopFai.PromotionActivity.delete'),
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
                'title' => '满减活动详情',
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
                            'name' => 'code',
                            'label' => '编码',
                        ],
                        [
                            'name' => 'discount',
                            'label' => '优惠规则',
                            'value' => function ($row) {

                                $configStore = Be::getConfig('App.ShopFai.Store');

                                $discount = '';
                                if ($row['condition'] !== 'none') {
                                    $discount .= '渍 ';
                                    if ($row['condition'] === 'min_amount') {
                                        $discount .= $configStore->currencySymbol . $row['condition_min_amount'];
                                    } elseif ($row['condition'] === 'min_quantity') {
                                        $discount .= $row['condition_min_quantity'] . ' 件';
                                    }
                                    $discount .= ' ';
                                }

                                if ($row['discount_type'] === 'percent') {
                                    $discount .= '减 ' . $row['discount_percent'] . '%';
                                } else {
                                    $discount .= '减 ' . $configStore->currencySymbol . $row['discount_amount'];
                                }

                                return $discount;
                            },
                        ],
                        [
                            'name' => 'scope_product',
                            'label' => '适用商品',
                            'value' => function ($row) {
                                if ($row['scope_product'] === 'all') {
                                    return '所有商品';
                                } elseif ($row['scope_product'] === 'assign') {
                                    return '指定商品';
                                } else {
                                    return '指定分类';
                                }
                            },
                        ],
                        [
                            'name' => 'scope_user',
                            'label' => '适用客户',
                            'value' => function ($row) {
                                if ($row['scope_product'] === 'all') {
                                    return '所有客户';
                                } elseif ($row['scope_product'] === 'assign') {
                                    return '指定客户';
                                } else {
                                    return '指定分组';
                                }
                            },
                        ],
                        [
                            'name' => 'limit_quantity',
                            'label' => '使用限制',
                            'driver' => DetailItemHtml::class,
                            'value' => function ($row) {
                                $html = '';
                                $html .= '总发放量：';
                                $row['limit_quantity'] = (int)$row['limit_quantity'];
                                if ($row['limit_quantity'] === 0) {
                                    $html .= '不限';
                                } else {
                                    $html .= $row['limit_quantity'] . '张';
                                }
                                $html .= '<br>';

                                $html .= '每人可用次数：';
                                $row['limit_times'] = (int)$row['limit_times'];
                                if ($row['limit_times'] === 0) {
                                    $html .= '不限';
                                } else {
                                    $html .= $row['limit_times'] . '次';
                                }

                                return $html;
                            },
                        ],
                        [
                            'name' => 'start_time',
                            'label' => '活动时间',
                            'driver' => DetailItemHtml::class,
                            'value' => function ($row) {
                                $html = '';
                                $html .= '开始时间：' . $row['start_time'];
                                $html .= '<br>';

                                $row['never_expire'] = (int)$row['never_expire'];
                                if ($row['never_expire'] === 1) {
                                    $html .= '永不过期';
                                } else {
                                    $html .= '结束时间：' . $row['end_time'];
                                }

                                return $html;
                            },
                        ],
                        [
                            'name' => 'status',
                            'label' => '状态',
                            'width' => '120',
                            'driver' => DetailItemHtml::class,
                            'value' => function ($row) {
                                if ($row['is_enable'] === '0') {
                                    return '<span class="el-tag el-tag--info el-tag--light">已禁用</span>';
                                } else {
                                    $t0 = time();
                                    $t1 = strtotime($row['start_time']);
                                    $t2 = strtotime($row['end_time']);
                                    if ($t2 < $t0) {
                                        return '<span class="el-tag el-tag--info el-tag--light">已失效</span>';
                                    } else if ($t1 > $t0) {
                                        return '<span class="el-tag el-tag--primary el-tag--light">未生效</span>';
                                    } else {
                                        return '<span class="el-tag el-tag--success el-tag--light">生效中</span>';
                                    }
                                }
                            },
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
                ],
            ],

        ])->execute();
    }

    /**
     * 新建满减活动
     *
     * @BePermission("新建满减活动", ordering="5.21")
     */
    public function create()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                Be::getService('App.ShopFai.Admin.PromotionActivity')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '新建满减活动成功！');
                $response->json();
            } catch (\Throwable $t) {
                $response->set('success', false);
                $response->set('message', $t->getMessage());
                $response->json();
            }
        } else {
            $configStore = Be::getConfig('App.ShopFai.Store');
            $response->set('configStore', $configStore);

            $response->set('promotionActivity', false);

            $response->set('title', '新建满减活动');

            //$response->display();
            $response->display('App.ShopFai.Admin.PromotionActivity.edit');
        }
    }

    /**
     * 编辑满减活动
     *
     * @BePermission("编辑满减活动", ordering="5.22")
     */
    public function edit()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        if ($request->isAjax()) {
            try {
                Be::getService('App.ShopFai.Admin.PromotionActivity')->edit($request->json('formData'));
                $response->set('success', true);
                $response->set('message', '编辑满减活动成功！');
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
                    $response->redirect(beAdminUrl('ShopFai.PromotionActivity.edit', ['id' => $postData['row']['id']]));
                }
            }
        } else {
            $configStore = Be::getConfig('App.ShopFai.Store');
            $response->set('configStore', $configStore);

            $promotionActivityId = $request->get('id', '');

            $service = Be::getService('App.ShopFai.Admin.PromotionActivity');

            $promotionActivity = $service->getPromotionActivity($promotionActivityId);
            $response->set('promotionActivity', $promotionActivity);

            $statistics = $service->getStatisticsSummary($promotionActivityId);
            $response->set('statistics', $statistics);

            $changes = $service->getChanges($promotionActivityId);
            $response->set('changes', $changes);

            $response->set('title', '编辑满减活动');

            $response->display();
        }
    }

    /**
     * 删除
     *
     * @BePermission("删除满减活动", ordering="5.23")
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
                Be::getService('App.ShopFai.Admin.PromotionActivity')->delete($productIds);
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
     * 统计
     *
     * @BePermission("满减活动统计", ordering="5.24")
     */
    public function statistics()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $postData = $request->post('data', '', '');
        if ($postData) {
            $postData = json_decode($postData, true);
            if (isset($postData['row']['id']) && $postData['row']['id']) {

                $configStore = Be::getConfig('App.ShopFai.Store');
                $response->set('configStore', $configStore);

                $promotionActivityId = $postData['row']['id'];

                $service = Be::getService('App.ShopFai.Admin.PromotionActivity');

                $promotionActivity = $service->getPromotionActivity($promotionActivityId);
                $response->set('promotionActivity', $promotionActivity);

                $statistics = $service->getStatistics($promotionActivityId);
                $response->set('statistics', $statistics);

                $response->set('title', '满减活动使用统计');

                $response->display();
            }
        }
    }

}
