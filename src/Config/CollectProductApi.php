<?php
namespace Be\App\ShopFai\Config;

/**
 * @BeConfig("商品采集接口")
 */
class CollectProductApi
{

    /**
     * @BeConfigItem("是否启用商品采集接口",
     *     description="启用后，将可以通过API的方式导入商品",
     *     driver="FormItemSwitch"
     * )
     */
    public int $enable = 0;

    /**
     * @BeConfigItem("存储文章的索引名",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];"
     * )
     */
    public string $token = '';


}
