<?php
$db = \Be\Be::getDb();
$tableNames = $db->getTableNames();
if (in_array('shop_category', $tableNames)) {
    if (in_array('shop_user_token', $tableNames)) {
        return;
    } else {
        throw new \Be\Runtime\RuntimeException('剑测到部分数据表已存在，请检查数据库！');
    }
}

$sql = file_get_contents(__DIR__ . '/install.sql');
$sqls = preg_split('/; *[\r\n]+/', $sql);
foreach ($sqls as $sql) {
    $sql = trim($sql);
    if ($sql) {
        $db->query($sql);
    }
}


if (\Be\Be::getTable('system_menu')->where('name', 'UserCenter')->count() === 0) {
    $tupleMenu = \Be\Be::getTuple('system_menu');
    $tupleMenu->name = 'UserCenter';
    $tupleMenu->label = '用户中心';
    $tupleMenu->is_system = 0;
    $tupleMenu->create_time = date('Y-m-d H:i:s');
    $tupleMenu->update_time = date('Y-m-d H:i:s');
    $tupleMenu->insert();

    $ordering = 1;
    foreach ([
                 [
                     'name' => 'My Orders',
                     'description' => '店熵商城: 用户-订单列表',
                     'route' => 'Shop.Order.orders',
                     'params' => [],
                 ],
                 [
                     'name' => 'Wish List',
                     'description' => '店熵商城: 用户-收藏夹',
                     'route' => 'Shop.UserFavorite.favorites',
                     'params' => [],
                 ],
                 [
                     'name' => 'Address Book',
                     'description' => '店熵商城: 用户-收货地址',
                     'route' => 'Shop.UserAddress.addresses',
                     'params' => [],
                 ],
                 [
                     'name' => 'Setting',
                     'description' => '店熵商城: 用户-账号设置',
                     'route' => 'Shop.UserCenter.setting',
                     'params' => [],
                 ],
             ] as $item) {

        $tupleMenuItem = \Be\Be::getTuple('system_menu_item');
        $tupleMenuItem->menu_name = 'UserCenter';
        $tupleMenuItem->parent_id = '';
        $tupleMenuItem->name = $item['name'];
        $tupleMenuItem->route = $item['route'];
        $tupleMenuItem->params = json_encode($item['params']);
        $tupleMenuItem->url = beUrl($item['route'], $item['params']);
        $tupleMenuItem->description = $item['description'];
        $tupleMenuItem->target = '_self';;
        $tupleMenuItem->is_enable = 1;
        $tupleMenuItem->ordering = $ordering;
        $tupleMenuItem->create_time = date('Y-m-d H:i:s');
        $tupleMenuItem->update_time = date('Y-m-d H:i:s');
        $tupleMenuItem->save();

        $ordering++;
    }
}