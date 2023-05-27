<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("商品")
 */
class Product
{

    /**
     * @BeConfigItem("网址前缀", driver="FormItemInput", description="以 / 开头，谨慎改动。")
     */
    public string $urlPrefix = '/product/';

    /**
     * @BeConfigItem("网址后缀（如.html）", driver="FormItemInput")
     */
    public string $urlSuffix = '';

    /**
     * @BeConfigItem("SPU唯一", driver="FormItemSwitch")
     */
    public int $spuUnique = 0;

    /**
     * @BeConfigItem("SKU唯一", driver="FormItemSwitch")
     */
    public int $skuUnique = 0;

    /**
     * @BeConfigItem("图像默认宽高比", driver = "FormItemInput")
     */
    public string $imageAspectRatio = '5/8';

}
