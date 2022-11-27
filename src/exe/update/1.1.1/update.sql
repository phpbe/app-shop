ALTER TABLE `shop_product_image`
ADD `product_item_id` VARCHAR(36) NOT NULL DEFAULT '' COMMENT '商品子项ID' AFTER `product_id`;


ALTER TABLE `shop_product_image`
CHANGE `large` `url` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '网址';


ALTER TABLE `shop_product_image`
DROP `small`,
DROP `medium`,
DROP `original`;


ALTER TABLE `shop_category`
CHANGE `image_large` `image` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '封面图片';

ALTER TABLE `shop_category`
DROP `image_small`,
DROP `image_medium`,
DROP `image_original`;


ALTER TABLE `shop_product_item`
DROP `image`;
