
CREATE TABLE `shop_product_video` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`product_id` varchar(36) NOT NULL DEFAULT '' COMMENT '商品ID',
`product_item_id` varchar(36) NOT NULL DEFAULT '' COMMENT '商品子项ID',
`url` varchar(300) NOT NULL DEFAULT '' COMMENT '网址',
`is_main` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否主图',
`ordering` int(11) NOT NULL DEFAULT '1' COMMENT '排序',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='商品视频';

ALTER TABLE `shop_product_video`
ADD PRIMARY KEY (`id`),
ADD KEY `product_id` (`product_id`,`product_item_id`,`is_main`) USING BTREE;
