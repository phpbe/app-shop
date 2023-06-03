<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class Product extends Base
{


    /**
     * @BeMenu("商品列表")
     * @BeRoute("/products")
     */
    public function products()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $response->set('title', $pageConfig->title ?: '');
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        $response->display();
    }


    /**
     *
     * @BeMenu("商品详情", picker="return \Be\Be::getService('App.Shop.Admin.Product')->getProductMenuPicker()")
     * @BeRoute("\Be\Be::getService('App.Shop.Product')->getProductUrl($params)")
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $id = $request->get('id');

        $product = Be::getService('App.Shop.Product')->hit($id);
        $product->promotion_templates = Be::getService('App.Shop.Promotion')->getProductTemplates($id);

        $response->set('product', $product);

        $response->set('title', $product->seo_title);
        $response->set('metaKeywords', $product->seo_keywords);
        $response->set('metaDescription', $product->seo_description);

        $response->set('pageTitle', $product->name);

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

        $product = Be::getService('App.Shop.Product')->getProduct($id);
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

            Be::getService('App.Shop.ProductReview')->post($userId, $productId, $rating, $name, $content, $imageInfos);

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

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $keywords = $request->get('keywords', '');
        $keywords = urldecode($keywords);
        $keywords = trim($keywords);
        $title = 'Search result of:' . $keywords;

        $response->set('title', $title);
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $title);
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

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $response->set('title', $pageConfig->title ?: '');
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        $response->display();
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

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $response->set('title', $pageConfig->title ?: '');
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        $response->display();
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

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $response->set('title', $pageConfig->title ?: '');
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        $response->display();
    }

    /**
     * 热搜商品
     *
     * @BeMenu("热搜商品")
     * @BeRoute("/product/hot-search")
     */
    public function hotSearch()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $response->set('title', $pageConfig->title ?: '');
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        $response->display();
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

        $pageConfig = $response->getPageConfig();
        $response->set('pageConfig', $pageConfig);

        $response->set('title', $pageConfig->title ?: '');
        $response->set('metaDescription', $pageConfig->metaDescription ?: '');
        $response->set('metaKeywords', $pageConfig->metaKeywords ?: '');
        $response->set('pageTitle', $pageConfig->pageTitle ?: ($pageConfig->title ?: ''));

        $response->display();
    }



}
