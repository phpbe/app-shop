<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("商品")
 */
class Product
{

    /**
     * @BeConfigItem("伪静态网址前缀", driver="FormItemInput")
     */
    public string $urlPrefix = 'products';

    /**
     * @BeConfigItem("伪静态网址后缀（如.html）", driver="FormItemInput")
     */
    public string $urlSuffix = '';

}
