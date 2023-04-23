# 店熵商城

## 应用简介

    be/app-shop 是一款基于BE双驱框架实现的商城管理系统；与 phpbe.com 平台下的所有应用和主题兼容。
    通过页面构建器可任意聚合其它应用或主题中的部件。使您的商城系统拥有与众不同的外观和功能。
    同时，基于 swoole 驱动的架构设计，店熵系统可以轻松支持海量并发。


## 如何安装

### 1 新建 be 项目

    composer create-project be/new

### 2 安装 be/app-shop

    composer require be/app-shop




## 部件

### 1xxx 商品相关部件
* 1001 - 最新商品列表 - Product.Latest
* 1002 - 最新商品TopN - Product.LatestTopN
* 1003 - 最新商品TopN边栏 - Product.LatestTopNSide
* 1004 - 热门商品列表 - Product.Hottest
* 1005 - 热门商品TopN - Product.HottestTopN
* 1006 - 热门商品TopN边栏 - Product.HottestSide
* 1007 - 热销商品列表 - Product.TopSales
* 1008 - 热销商品TopN - Product.TopSalesTopN
* 1009 - 热销商品TopN边栏 - Product.TopSalesTopNSide
* 1010 - 热搜商品列表 - Product.HotSearch
* 1011 - 热搜商品TopN - Product.HotSearchTopN
* 1012 - 热搜商品TopN边栏 - Product.HotSearchTopNSide
* 1013 - 猜你喜欢商品列表 - Product.GuessYouLike
* 1014 - 猜你喜欢商品TopN - Product.GuessYouLikeTopN
* 1015 - 猜你喜欢商品TopN边栏 - Product.GuessYouLikeTopNSide
* 1016 - 搜索结果 - Product.Search
* 1017 - 商品详情-主体 - Product.Detail.Main
* 1018 - 商品详情-描述 - Product.Detail.Description
* 1019 - 商品详情-评论 - Product.Detail.Reviews
* 1020 - 商品详情-类似商品TopN - Product.Detail.SimilarTopN
* 1021 - 商品详情-类似商品TopN边栏 - Product.Detail.SimilarTopNSide

### 2xxx 分类相关部件
* 2001 - 分类商品列表 - Category.Products
* 2002 - 分类列表TopN - Category.TopN
* 2003 - 分类列表TopN边栏 - Category.TopNSide

