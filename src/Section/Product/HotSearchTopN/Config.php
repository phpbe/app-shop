<?php

namespace Be\App\Shop\Section\Product\HotSearchTopN;

/**
 * @BeConfig("热搜商品TopN", icon="bi-search-heart", ordering="1011")
 */
class Config
{

    /**
     * @BeConfigItem("是否启用",
     *     driver = "FormItemSwitch")
     */
    public $enable = 1;

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
     * @BeConfigItem("标题",
     *     driver = "FormItemInput"
     * )
     */
    public string $title = 'Hot Search';

    /**
     * @BeConfigItem("展示多少个商品?",
     *     driver = "FormItemSlider",
     *     ui="return [':min' => 1, ':max' => 100];"
     * )
     */
    public int $quantity = 12;

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
     * @BeConfigItem("查看更多链接",
     *     driver = "FormItemInput"
     * )
     */
    public string $more = 'Show More';

    /**
     * @BeConfigItem("更多链接",
     *     driver = "FormItemInput"
     * )
     */
    public string $moreLink = '';

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
    public string $marginMobile = '1rem 0 0 0';

    /**
     * @BeConfigItem("外边距 （平板端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginTablet = '1.5rem 0 0 0';

    /**
     * @BeConfigItem("外边距 （电脑端）",
     *     driver = "FormItemInput",
     *     description = "上右下左（CSS margin 语法）"
     * )
     */
    public string $marginDesktop = '2rem 0 0 0';

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
