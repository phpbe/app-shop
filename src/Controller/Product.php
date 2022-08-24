<?php

namespace Be\App\ShopFai\Controller;

use Be\Be;

class Product extends Base
{

    /**
     *
     * @BeRoute("\Be\Be::getService('App.ShopFai.Product')->getProductUrl($params)")
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $id = $request->get('id');

        $product = Be::getService('App.ShopFai.Product')->hit($id);
        $product->promotion_templates = Be::getService('App.ShopFai.Promotion')->getProductTemplates($id);

        $response->set('product', $product);

        $response->display();
    }

    /**
     * @BeRoute("/product/review")
     */
    public function review()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $id = $request->get('id');

        $product = Be::getService('App.ShopFai.Product')->getProduct($id);
        $response->set('product', $product);

        $response->display();
    }

    /**
     * @BeRoute("/product/review-save")
     */
    public function reviewSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        try {
            $productId = $request->post('product_id');

            $rating = $request->post('rating', 5, 'int');

            $name = $request->post('name', '');
            $name = trim($name);

            $content = $request->post('content', '');
            $content = trim($content);

            //file_put_contents(Be::getRuntime()->getRootPath().'/p', print_r($request->post(), true));
            //file_put_contents(Be::getRuntime()->getRootPath().'/f', print_r($request->files(), true));

            $imageInfos = [];
            $images = $request->files('images');
            if (count($images) > 0) {
                foreach ($images as $image) {
                    if (isset($image['error']) && $image['error'] === 0) {
                        if (file_exists($image['tmp_name'])) {
                            $imageData = file_get_contents($image['tmp_name']);
                            $imageInfo = [];
                            $imageInfo['name'] = $image['name'];
                            $imageInfo['type'] = $image['type'];
                            $imageInfo['size'] = $image['size'];
                            $imageInfo['data'] = base64_encode($imageData);
                            $imageInfos[] = $imageInfo;
                        }
                    }
                }
            }

            Be::getService('App.ShopFai.ProductReview')->post($userId, $productId, $rating, $name, $content, $imageInfos);

            $response->set('success', true);
            $response->set('message', 'Submit success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * @BeRoute("/product/search")
     */
    public function search()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $keywords = $request->get('q');
        $keywords = urldecode($keywords);
        $response->set('q', $keywords);

        $orderBy = $request->get('orderBy', 'common');
        $orderByDir = $request->get('orderByDir', 'desc');
        $response->set('orderBy', $orderBy);
        $response->set('orderByDir', $orderByDir);

        $page = $request->get('page', 1);

        $result = Be::getService('App.ShopFai.Product')->search($keywords, [
            'orderByDir' => $orderByDir,
            'page' => $page,
            'orderBy' => $orderBy,
        ]);
        $response->set('result', $result); // 商品列表

        $response->display();
    }

    /**
     * 最新商品
     *
     * @BeMenu("最新商品")
     * @BeRoute("/product/latest")
     */
    public function latest()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $page = $request->get('page', 1);
        $result = Be::getService('App.ShopFai.Product')->search('', [
            'orderBy' => 'create_time',
            'orderByDir' => 'desc',
            'page' => $page,
        ]);
        $response->set('result', $result);

        $paginationUrl = beUrl('ShopFai.Product.latest');
        $response->set('paginationUrl', $paginationUrl);

        $response->display('App.ShopFai.Product.products');
    }

    /**
     * 热门商品
     *
     * @BeMenu("热门商品")
     * @BeRoute("/product/hottest")
     */
    public function hottest()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $page = $request->get('page', 1);
        $result = Be::getService('App.ShopFai.Product')->search('', [
            'orderBy' => 'hits',
            'orderByDir' => 'desc',
            'page' => $page,
        ]);
        $response->set('result', $result);

        $paginationUrl = beUrl('ShopFai.Product.hottest');
        $response->set('paginationUrl', $paginationUrl);

        $response->display('App.ShopFai.Product.products');
    }

    /**
     * 热销商品
     *
     * @BeMenu("热销商品")
     * @BeRoute("/product/top-sales")
     */
    public function topSales()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        $page = $request->get('page', 1);
        $result = Be::getService('App.ShopFai.Product')->search('', [
            'orderBy' => 'sales_volume',
            'orderByDir' => 'desc',
            'page' => $page,
        ]);
        $response->set('result', $result);

        $paginationUrl = beUrl('ShopFai.Product.topSales');
        $response->set('paginationUrl', $paginationUrl);

        $response->display('App.ShopFai.Product.products');
    }

    /**
     * 热搜商品
     *
     * @BeMenu("热搜商品")
     * @BeRoute("/product/top-search")
     */
    public function topSearch()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $rows = Be::getService('App.ShopFai.Product')->getTopSearch(100);
        $result = [
            'total' => count($rows),
            'pageSize' => 100,
            'page' => 1,
            'rows' => $rows,
        ];

        $response->set('result', $result);

        $paginationUrl = beUrl('ShopFai.Product.topSearch');
        $response->set('paginationUrl', $paginationUrl);

        $response->display('App.ShopFai.Product.products');
    }

    /**
     * 猜你喜欢
     * @BeMenu("猜你喜欢")
     * @BeRoute("/product/guess-you-like")
     */
    public function guessYouLike()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $rows = Be::getService('App.ShopFai.Product')->getGuessYouLikeProducts(100);
        $result = [
            'total' => count($rows),
            'pageSize' => 100,
            'page' => 1,
            'rows' => $rows,
        ];

        $response->set('result', $result);

        $paginationUrl = beUrl('ShopFai.Product.guessYouLike');
        $response->set('paginationUrl', $paginationUrl);

        $response->display('App.ShopFai.Product.products');
    }



}
