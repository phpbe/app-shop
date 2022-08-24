<?php

namespace Be\App\ShopFai\Controller;

use Be\Be;

/**
 * 活动
 *
 * Class PromotionActivity
 * @package Be\App\ShopFai\Controller
 */
class PromotionActivity extends Base
{

    /**
     * @BeRoute("\Be\Be::getService('App.ShopFai.PromotionActivity')->getPromotionActivityUrl($params)")
     */
    public function detail()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $id = $request->get('id');

        $service = Be::getService('App.ShopFai.PromotionActivity');

        $promotionActivity = $service->getPromotionActivity($id);
        $response->set('promotionActivity', $promotionActivity);

        $response->set('title', $promotionActivity->seo_title);
        $response->set('meta_keywords', $promotionActivity->seo_keywords);
        $response->set('meta_description', $promotionActivity->seo_description);

        $page = $request->get('page', 1);
        $products = $service->getPromotionActivityProducts($promotionActivity, [
            'page' => $page,
        ]);
        $response->set('products', $products);

        $response->display();
    }

}
