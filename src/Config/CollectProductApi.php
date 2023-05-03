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
     * @BeConfigItem("唯一键必填", driver="FormItemSwitch")
     */
    public int $uniqueKeyRequired = 0;

    /**
     * @BeConfigItem("名称必填", driver="FormItemSwitch", ui="return [':disabled' => 1]")
     */
    public int $nameRequired = 1;

    /**
     * @BeConfigItem("摘要必填", driver="FormItemSwitch")
     */
    public int $summaryRequired = 0;

    /**
     * @BeConfigItem("描述必填", driver="FormItemSwitch")
     */
    public int $descriptionRequired = 0;

    /**
     * @BeConfigItem("主图必填", driver="FormItemSwitch")
     */
    public int $imagesRequired = 0;

    /**
     * @BeConfigItem("视频必填", driver="FormItemSwitch")
     */
    public int $videosRequired = 0;

    /**
     * @BeConfigItem("SPU必填", driver="FormItemSwitch")
     */
    public int $spuRequired = 0;

    /**
     * @BeConfigItem("品牌必填", driver="FormItemSwitch")
     */
    public int $brandRequired = 0;

    /**
     * @BeConfigItem("单价必填", driver="FormItemSwitch")
     */
    public int $priceRequired = 0;

}
