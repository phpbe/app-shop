ALTER TABLE `shop_product`
CHANGE `download_remote` `download_remote_image` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '是否下载远程图片（0-不下载/1-下载/2-已下载完成/-1-下载失败） ';
