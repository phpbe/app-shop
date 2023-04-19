
CREATE TABLE `shop_cart` (
`id` varchar(36) NOT NULL DEFAULT 'uuid()' COMMENT 'UUID',
`user_id` varchar(36) NOT NULL DEFAULT '' COMMENT '用户ID',
`user_token` varchar(32) NOT NULL COMMENT '用户TOKEN',
`product_id` varchar(36) NOT NULL DEFAULT '' COMMENT '商品ID',
`product_item_id` varchar(36) NOT NULL DEFAULT '' COMMENT '商品子项ID',
`quantity` int(11) NOT NULL DEFAULT '1' COMMENT '数量',
`create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
`update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci COMMENT='购物车';


ALTER TABLE `shop_cart`
ADD PRIMARY KEY (`id`),
ADD KEY `user_id` (`user_id`),
ADD KEY `user_token` (`user_token`);

