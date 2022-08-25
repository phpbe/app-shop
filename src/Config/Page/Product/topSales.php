<?php

namespace Be\App\ShopFai\Config\Page\Product;

class topSales
{

    public int $west = 0;
    public int $center = 100;
    public int $east = 0;

    public array $centerSections = [
        [
            'name' => 'be-page-title',
        ],        [
            'name' => 'be-page-content',
        ],
    ];

    /**
     * @BeConfigItem("标题",
     *     description = "为空时不显示",
     *     driver = "FormItemInput"
     * )
     */
    public $title = 'Top Sales';

    /**
     * @BeConfigItem("SEO描述",
     *     driver = "FormItemInput"
     * )
     */
    public $seoDescription = '';

    /**
     * @BeConfigItem("SEO描述关键词",
     *     driver = "FormItemInput"
     * )
     */
    public $seoKeywords = '';

    /**
     * @BeConfigItem("展示多少列?",
     *     description = "仅对电脑端有效",
     *     driver = "FormItemSlider",
     *     ui="return [':min' => 3, ':max' => 6];"
     * )
     */
    public $cols = 4;

    /**
     * @BeConfigItem("鼠标悬停效果",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['none' => '无', 'scale' => '放大', 'rotateScale' => '旋转放大', 'toggleImage' => '切换图片'];"
     * )
     */
    public $hoverEffect = 'toggleImage';

    /**
     * @BeConfigItem("间距（手机端）",
     *     driver = "FormItemInput"
     * )
     */
    public string $spacingMobile = '1.5rem';

    /**
     * @BeConfigItem("间距（平板端）",
     *     driver = "FormItemInput"
     * )
     */
    public string $spacingTablet = '1.75rem';

    /**
     * @BeConfigItem("间距（电脑端）",
     *     driver = "FormItemInput"
     * )
     */
    public string $spacingDesktop = '2rem';


}
