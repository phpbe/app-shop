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

