<?php
namespace Be\App\ShopFai\Config;

/**
 * @BeConfig("店铺")
 */
class Store
{

    /**
     * @BeConfigItem("名称", driver="FormItemInput")
     */
    public string $name = 'products';

    /**
     * @BeConfigItem("币种", driver="FormItemInput")
     */
    public string $currency = 'USD';

    /**
     * @BeConfigItem("币种符号", driver="FormItemInput")
     */
    public string $currencySymbol = '$';

    /**
     * @BeConfigItem("时区", driver="FormItemInput")
     */
    public string $timezone = 'Asia/Shanghai';

    /**
     * 是否已完成基础设置
     */
    public int $setUp = 0;

}
