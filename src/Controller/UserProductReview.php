<?php

namespace Be\App\Shop\Controller;

use Be\Be;

class UserProductReview extends Auth
{

    /**
     * 我的评论
     *
     * @BeMenu("用户 - 评论")
     * @BeRoute("/my-reviews")
     */
    public function reviews()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $my = Be::getUser();
        $userId = $my->id;

        $option = [];

        $total = Be::getService('App.Shop.UserProductReview')->getCount($userId, $option);
        $response->set('total', $total);

        $pageSize = $request->get('pageSize', 12);
        $page = $request->get('page', 1);

        if ($pageSize < 1) $pageSize = 1;
        if ($pageSize > 100) $pageSize = 100;
        if ($page <= 0) $page = 1;
        $pages = $total > 0 ? ceil($total / $pageSize) : 1;
        if ($page > $pages) $page = $pages;
        $response->set('pageSize', $pageSize);
        $response->set('pages', $pages);
        $response->set('page', $page);

        $option['pageSize'] = $pageSize;
        $option['page'] = $page;

        $reviews = Be::getService('App.Shop.UserProductReview')->getReviews($userId, $option);
        $response->set('reviews', $reviews);

        $response->display();
    }

}
