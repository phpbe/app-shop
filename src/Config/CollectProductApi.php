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

    /**
     * @BeConfigItem("下载远程文件时重命名",
     *     driver="FormItemInput",
     *     ui="return ['form-item' => ['v-show' => 'formData.enable === 1']];",
     *     driver="FormItemSelect",
     *     keyValues = "return ['orginal' => '保留原始文件名', 'md5' => 'HASH命名（MD5）',, 'sha1' => 'HASH命名（SHA1）', 'timestamp' => '时间戳命名'];")
     * )
     */
    public string $downloadRemoteFileRename = 'md5';

}
