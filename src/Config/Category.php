<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("分类")
 */
class Category
{

    /**
     * @BeConfigItem("网址前缀", driver="FormItemInput", description="以 / 开头，谨慎改动。")
     */
    public string $urlPrefix = '/category/';

    /**
     * @BeConfigItem("网址后缀（如.html）", driver="FormItemInput")
     */
    public string $urlSuffix = '';

}
