<?php
namespace Be\App\Shop\Config;

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
     * @BeConfigItem("接口密钥",
     *     description="密码用于识别已授权的访问，附加到网址中传输，为了系统安全，请妥善保管。",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];"
     * )
     */
    public string $token = '';

}
