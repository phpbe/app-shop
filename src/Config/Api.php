<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("API接口")
 */
class Api
{

    /**
     * @BeConfigItem("是否启用API接口",
     *     description="启用后，将可以通过API的方式操作商城数据",
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
