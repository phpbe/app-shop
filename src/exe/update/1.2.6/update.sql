ALTER TABLE `shop_product`
ADD `publish_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '上架时间' AFTER `stock_out_action`;