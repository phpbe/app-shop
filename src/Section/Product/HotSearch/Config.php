<?php

namespace Be\App\Shop\Section\Product\HotSearch;

/**
 * @BeConfig("热搜商品列表", icon="bi-search-heart-fill", ordering="1010")
 */
class Config
{

    /**
     * @BeConfigItem("最新",
     *     driver = "FormItemSwitch")
     */
    public int $enable = 1;

    /**
     * @BeConfigItem("宽度",
     *     description="位于middle时有效",
     *     driver="FormItemSelect",
     *     keyValues = "return ['default' => '默认', 'fullWidth' => '全屏'];"
     * )
     */
    public string $width = 'default';

    /**
     * @BeConfigItem("背景颜色",
     *     driver="FormItemColorPicker"
     * )
     */
    public string $backgroundColor = '#fff';

    /**
     * @BeConfigItem("分页太小",,
     *     description = "分页为0时取系统配置",
     *     driver = "FormItemSlider",
     *     ui="return [':min' => 0, ':max' => 100];"
     * )
     */
    public int $pageSize = 48;

    /**
     * @BeConfigItem("最大分页",
     *     description = "为节约服务器资源，限制分页展示时的最大页码数",
     *     driver = "FormItemInputNumberInt",
     * )
     */
    public int $maxPages = 100;

    /**
     * @BeConfigItem("展示多少列?",
     *     description = "仅对电脑端有效",
     *     driver = "FormItemSlider",
     *     ui="return [':min' => 3, ':max' => 6];"
     * )
     */
    public int $cols = 4;

    /**
     * @BeConfigItem("鼠标悬停效果",
     *     driver = "FormItemSelect",
     *     keyValues = "return ['none' => '无', 'scale' => '放大', 'rotateScale' => '旋转放大', 'toggleImage' => '切换图片'];"
     * )
     */
    public string $hoverEffect = 'toggleImage';

    /**
     * @BeConfigItem("内边距 （手机端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingMobile = '1rem';

    /**
     * @BeConfigItem("内边距 （平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingTablet = '1.5rem';

    /**
     * @BeConfigItem("内边距 （电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS padding 语法）"
     * )
     */
    public string $paddingDesktop = '2rem';

    /**
     * @BeConfigItem("外边距 （手机端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginMobile = '0';

    /**
     * @BeConfigItem("外边距 （平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginTablet = '0';

    /**
     * @BeConfigItem("外边距 （电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginDesktop = '0';

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
