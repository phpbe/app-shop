<?php

namespace Be\App\Shop\Config\Page\Product;

/**
 * @BeConfig("热销商品")
 */
class topSales
{

    public int $west = 25;
    public int $center = 75;
    public int $east = 0;

    public array $westSections = [
        [
            'name' => 'App.Shop.Category.TopNSide',
        ],
        [
            'name' => 'App.Shop.Product.LatestTopNSide',
        ],
        [
            'name' => 'App.Shop.Product.HottestTopNSide',
        ],
    ];

    public array $centerSections = [
        [
            'name' => 'Theme.System.PageTitle',
        ],
        [
            'name' => 'App.Shop.Product.TopSales',
        ],
    ];

    /**
     * @BeConfigItem("HEAD头标题",
     *     description="HEAD头标题，用于SEO",
     *     driver = "FormItemInput"
     * )
     */
    public string $title = 'Top Sales';

    /**
     * @BeConfigItem("Meta描述",
     *     description="填写页面内容的简单描述，用于SEO",
     *     driver = "FormItemInput"
     * )
     */
    public string $metaDescription = 'Top Sales';

    /**
     * @BeConfigItem("Meta关键词",
     *     description="填写页面内容的关键词，用于SEO",
     *     driver = "FormItemInput"
     * )
     */
    public string $metaKeywords = 'Top Sales';

    /**
     * @BeConfigItem("页面标题",
     *     description="展示在页面内容中的标题，一般与HEAD头标题一致，两者相同时可不填写此项",
     *     driver = "FormItemInput"
     * )
     */
    public string $pageTitle = '';


}
