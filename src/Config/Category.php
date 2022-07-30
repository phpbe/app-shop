<?php
namespace Be\App\ShopFai\Config;

/**
 * @BeConfig("分类")
 */
class Category
{
    
    /**
     * @BeConfigItem("伪静态网址前缀", driver="FormItemInput")
     */
    public string $urlPrefix = 'collections';

    /**
     * @BeConfigItem("伪静态网址后缀（如.html）", driver="FormItemInput")
     */
    public string $urlSuffix = '';

}
