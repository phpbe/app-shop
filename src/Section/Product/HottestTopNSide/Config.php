<?php

namespace Be\App\Shop\Section\Product\HottestTopNSide;

/**
 * @BeConfig("热门商品TopN边栏", icon="bi-bag-heart", ordering="1006")
 */
class Config
{

    /**
     * @BeConfigItem("最新",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("背景颜色",
     *     driver = "FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '';

    /**
     * @BeConfigItem("标题",
     *     driver = "FormItemInput"
     * )
     */
    public string $title = 'Hottest';

    /**
     * @BeConfigItem("展示多少个商品?",
     *     driver = "FormItemSlider",
     *     ui="return [':min' => 1, ':max' => 100];"
     * )
     */
    public int $quantity = 6;

    /**
     * @BeConfigItem("查看更多链接",
     *     driver = "FormItemInput"
     * )
     */
    public string $more = 'Show more';

    /**
     * @BeConfigItem("更多链接",
     *     driver = "FormItemInput"
     * )
     */
    public string $moreLink = '';

    /**
     * @BeConfigItem("内边距（手机端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingMobile = '0';

    /**
     * @BeConfigItem("内边距（平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingTablet = '0';

    /**
     * @BeConfigItem("内边距（电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingDesktop = '0';

    /**
     * @BeConfigItem("外边距（手机端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginMobile = '1rem 0 0 0';

    /**
     * @BeConfigItem("外边距（平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginTablet = '2rem 0 0 0';

    /**
     * @BeConfigItem("外边距（电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginDesktop = '3rem 0 0 0';


}
