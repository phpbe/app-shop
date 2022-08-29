<?php
namespace Be\App\ShopFai\Config;

/**
 * @BeConfig("用户")
 */
class User
{
    /**
     * @BeConfigItem("头像宽度",
     *     driver="FormItemInputNumberInt",
     *     description="单位：像素，修改后仅对此后上传的头像生效",
     *     ui="return [':min' => 1];")
     */
    public $avatarWidth = 200;

    /**
     * @BeConfigItem("头像高度",
     *     driver="FormItemInputNumberInt",
     *     description="单位：像素，修改后仅对此后上传的头像生效",
     *     ui="return [':min' => 1];")
     */
    public $avatarHeight = 200;

    /**
     * @BeConfigItem("收藏夹存储方式",
     *     driver="FormItemSelect",
     *     keyValues = "return ['redis' => 'Redis 存储', 'db' => '数据库存储'];")
     */
    public $favoriteDrive = 'redis';


}
