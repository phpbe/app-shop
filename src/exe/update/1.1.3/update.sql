ALTER TABLE `shop_product_tag` ADD `ordering` INT NOT NULL DEFAULT '0' COMMENT '排序' AFTER `tag`;

ALTER TABLE `shop_product_item` ADD `ordering` INT NOT NULL DEFAULT '0' COMMENT '排序' AFTER `stock`;

ALTER TABLE `shop_product_category` ADD `ordering` INT NOT NULL DEFAULT '0' COMMENT '排序' AFTER `category_id`;

ALTER TABLE `shop_product_relate`
CHANGE `icon_type` `icon_type` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'text' COMMENT '商品关联的图标类型';

RENAME TABLE `shop_product_relate_detail` TO `shop_product_relate_item`;
ALTER TABLE `shop_product_relate_item` COMMENT = '商品关联子项';

ALTER TABLE `shop_product_style`
ADD `icon_type` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'text' COMMENT '款式图标类型' AFTER `name`,
ADD `ordering` INT NOT NULL DEFAULT '0' COMMENT '排序' AFTER `icon_type`;
ALTER TABLE `shop_product_style` DROP `values`;

CREATE TABLE `shop_product_style_item` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`product_style_id` varchar(36) NOT NULL DEFAULT '' COMMENT '款式ID',
`value` varchar(60) NOT NULL DEFAULT '' COMMENT '关联属性的值',
`icon_image` varchar(120) NOT NULL DEFAULT '' COMMENT '图标 - 图像',
`icon_color` varchar(10) NOT NULL DEFAULT '' COMMENT '图标 - 色块',
`ordering` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品关联明细';

ALTER TABLE `shop_product_style_item`
ADD PRIMARY KEY (`id`),
ADD KEY `product_style_id` (`product_style_id`);
