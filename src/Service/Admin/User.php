<?php

namespace Be\App\Shop\Service\Admin;

use Be\AdminPlugin\Form\Item\FormItemSelect;
use Be\AdminPlugin\Table\Item\TableItemImage;
use Be\App\ServiceException;
use Be\App\Shop\Admin\ShopAdmin;
use Be\Be;
use Be\Db\Tuple;

class User
{


    /**
     * 获取选择器
     *
     * @return array
     */
    public function getUserPicker(int $multiple = 0): array
    {
        return [
            'table' => 'shop_user',
            'grid' => [
                'title' => $multiple === 1 ? '选择用户' : '选择一个用户',

                'filter' => [
                    ['is_enable', '=', '1'],
                    ['is_delete', '=', '0'],
                ],

                'form' => [
                    'items' => [
                        [
                            'name' => 'email',
                            'label' => '邮箱',
                        ],
                    ],
                ],

                'table' => [

                    // 未指定时取表的所有字段
                    'items' => [
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
