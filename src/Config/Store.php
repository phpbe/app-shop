<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("店铺")
 */
class Store
{

    /**
     * @BeConfigItem("名称", driver="FormItemInput")
     */
    public string $name = '店铺名称';

    /**
     * @BeConfigItem("币种", driver="FormItemInput")
     */
    public string $currency = 'USD';

    /**
     * @BeConfigItem("币种符号", driver="FormItemInput")
     */
    public string $currencySymbol = '$';

    /**
     * @BeConfigItem("时区", description="支持的时区列表：https://www.phpbe.com/doc/help/v2/timezones", driver="FormItemInput")
     */
    public string $timezone = 'Asia/Shanghai';

    /**
     * 是否已完成基础设置
     */
    public int $setUp = 0;

}
