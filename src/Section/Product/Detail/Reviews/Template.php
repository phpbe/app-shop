<?php

namespace Be\App\Shop\Section\Product\Detail\Reviews;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Product.detail'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $this->css();

        $ratingAvg = $this->page->product->rating_avg;
        $ratingCount = $this->page->product->rating_count;
        $pageSize = $this->config->pageSize;
        $reviewPage = \Be\Be::getRequest()->get('reviewPage', 1);
        if ($reviewPage > $this->config->maxPages) {
            $reviewPage = $this->config->maxPages;
        }
        $reviews = \Be\Be::getService('App.Shop.ProductReview')->getReviews($this->page->product->id, [
            'page' => $reviewPage,
            'pageSize' => $pageSize,
        ], ['images' => true]);

        echo '<div class="app-shop-product-detail-reviews">';
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }

        echo '<h4 class="be-h4 be-lh-200">' . $this->config->title . '</h4>';

        echo '<div class="be-row">';
        echo '<div class="be-col-24 be-col-sm-16 be-mt-100">';
        $roundRatingAvg = round($ratingAvg);
        echo '<span class="be-mr-50 be-va-middle">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $roundRatingAvg) {
                echo '<i class="icon-star-fill icon-star-fill-150"></i>';
            } else {
                echo '<i class="icon-star icon-star-150"></i>';
            }
        }
        echo '</span>';
        echo $ratingCount . ' review(s)';
        echo '</div>';
        echo '<div class="be-col-24 be-col-sm-8 be-ta-right be-mt-100">';
        echo '<a href="' . beUrl('Shop.Product.review', ['id' => $this->page->product->id]) . '" class="be-btn be-btn-round">Write a review</a>';
        echo '</div>';
        echo '</div>';

        if ($ratingCount > 0) {
            echo '<div class="be-mt-200">';
            foreach ($reviews as $review) {
                echo '<div class="app-shop-product-detail-review be-py-100">';

                echo '<div class="be-fc">';
                echo '<div class="app-shop-product-detail-review-avatar">';
                echo '<div class="app-shop-product-detail-review-avatar-letter">' . substr($review->name, 0, 1) . '</div>';
                if ($review->user_id > 0) {
                    echo '<i class="app-shop-product-detail-review-avatar-icon"></i>';
                }
                echo '</div>';
                echo '<div class="be-d-inline-block">';
                echo '<div class="be-mb-50">';
                echo $review->name;
                if ($review->user_id > 0) {
                    echo ' <span class="be-c-999">Verified Buyer</span>';
                }
                echo '</div>';
                echo '<div class="be-mb-50">';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $review->rating) {
                        echo '<i class="icon-star-fill icon-star-fill-120"></i>';
                    } else {
                        echo '<i class="icon-star icon-star-120"></i>';
                    }
                }
                echo '</div>';
                echo '</div>';
                echo '<div class="app-shop-product-detail-review-time-desktop be-fr be-c-999">' . date('m/d/y', strtotime($review->create_time)) . '</div>';
                echo '</div>';

                echo '<div class="app-shop-product-detail-review-content">' . $review->content . '</div>';

                if (isset($review->images) && count($review->images) > 0) {
                    echo '<div class="app-shop-product-detail-review-images">';
                    foreach ($review->images as $reviewImage) {
                        echo '<a class="light-box-image" data-lightbox="roadtrip" href="' . $reviewImage->url . '" target="_blank">';
                        echo '<img src="' . $reviewImage->url . '" />';
                        echo '</a>';
                    }
                    echo '</div>';
                }

                echo '<div class="app-shop-product-detail-review-time-mobile be-c-999 be-pt-50">';
                echo date('m/d/y', strtotime($review->create_time));
                echo '</div>';

                echo '</div>';
            }
            echo '</div>';

            $total = $ratingCount;
            $page = $reviewPage;
            $pages = ceil($total / $pageSize);
            if ($page > $pages) $page = $pages;

            if ($pages > 1) {
                echo '<nav class="be-mt-200">';
                echo '<ul class="be-pagination" style="justify-content: center;">';
                echo '<li>';
                if ($page > 1) {
                    $url = beUrl('Shop.Product.detail', ['id' => $this->page->product->id,]);
                    $url .= strpos($url, '?') === false ? '?' : '&';
                    $url .= http_build_query(['reviewPage' => ($page - 1)]);
                    echo '<a href="' . $url . '">Previous</a>';
                } else {
                    echo '<span>Previous</span>';
                }
                echo '</li>';

                $from = null;
                $to = null;
                if ($pages < 9) {
                    $from = 1;
                    $to = $pages;
                } else {
                    $from = $page - 4;
                    if ($from < 1) {
                        $from = 1;
                    }

                    $to = $from + 8;
                    if ($to > $pages) {
                        $to = $pages;
                    }
                }

                if ($from > 1) {
                    echo '<li><span>...</span></li>';
                }

                for ($i = $from; $i <= $to; $i++) {
                    if ($i == $page) {
                        echo '<li class="active">';
                        echo '<span>' . $i . '</span>';
                        echo '</li>';
                    } else {
                        $url = beUrl('Shop.Product.detail', ['id' => $this->page->product->id,]);
                        $url .= strpos($url, '?') === false ? '?' : '&';
                        $url .= http_build_query(['reviewPage' => $i]);
                        echo '<li>';
                        echo '<a href="' . $url . '">' . $i . '</a>';
                        echo '</li>';
                    }
                }

                if ($to < $pages) {
                    echo '<li><span>...</span></li>';
                }

                echo '<li>';
                if ($page < $pages) {
                    $url = beUrl('Shop.Product.detail', ['id' => $this->page->product->id,]);
                    $url .= strpos($url, '?') === false ? '?' : '&';
                    $url .= http_build_query(['reviewPage' => ($page + 1)]);
                    echo '<a href="' . $url . '">Next</a>';
                } else {
                    echo '<span>Next</span>';
                }
                echo '</li>';
                echo '</ul>';
                echo '</nav>';
            }
        }

        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
        echo '</div>';

        $this->js();
    }


    private function css()
    {
        echo '<style type="text/css">';
        echo $this->getCssBackgroundColor('app-shop-product-detail-reviews');
        echo $this->getCssPadding('app-shop-product-detail-reviews');
        echo $this->getCssMargin('app-shop-product-detail-reviews');

        echo '#' . $this->id . ' .app-shop-product-detail-review {';
        echo 'border-top: #eee 1px solid;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-product-detail-review-avatar {';
        echo 'width: 3rem;';
        echo 'height: 3rem;';
        echo 'background-color: #7b7979;';
        echo '-webkit-border-radius: 50%;';
        echo '-moz-border-radius: 50%;';
        echo 'border-radius: 50%;';
        echo 'margin-right: 1rem;';
        echo 'display: inline-block;';
        echo 'position: relative;';
        echo 'vertical-align: top;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-product-detail-review-avatar-letter {';
        echo 'width: 3rem;';
        echo 'height: 3rem;';
        echo 'line-height: 3rem;';
        echo 'color: #fff;';
        echo 'text-align: center;';
        echo 'font-size: 20px;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-product-detail-review-avatar-icon {';
        echo 'display: block;';
        echo 'width: 1rem;';
        echo 'height: 1rem;';
        echo 'position: absolute;';
        echo 'left: 2rem;';
        echo 'top: 2rem;';
        echo 'border-radius: 50%;';
        echo 'background-color: #fff;';
        echo 'background-repeat: no-repeat;';
        echo 'background-position: center center;';
        echo 'background-size: 1rem 1rem;';
        echo 'background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath  fill=\'%231cc286\' d=\'M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z\'/%3e%3c/svg%3e");';
        echo '}';

        echo '#' . $this->id . ' .app-shop-product-detail-review-content,';
        echo '#' . $this->id . ' .app-shop-product-detail-review-images {';
        echo 'margin: .5rem 0 0 4rem;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-product-detail-review-images a {';
        echo 'display: inline-block;';
        echo 'width: 4rem;';
        echo 'height: 4rem;';
        echo 'margin-right: .5rem;';
        echo 'margin-top: .5rem;';
        echo '}';

        echo '#' . $this->id . ' .app-shop-product-detail-review-images a img {';
        echo 'max-width: 100%;';
        echo 'max-height: 100%;';
        echo 'vertical-align: middle;';
        echo '}';

        // 手机端
        echo '@media (max-width: 768px) {';
        echo '#' . $this->id . ' .app-shop-product-detail-review-time-mobile {';
        echo 'display: block;';
        echo '}';
        echo '#' . $this->id . ' .app-shop-product-detail-review-time-desktop {';
        echo 'display: none;';
        echo '}';
        echo '#' . $this->id . ' .app-shop-product-detail-review-content,';
        echo '#' . $this->id . ' .app-shop-product-detail-review-images {';
        echo 'margin-left: 0 !important;';
        echo '}';
        echo '}';

        // 电脑端
        echo '@media (min-width: 768px) {';
        echo '#' . $this->id . ' .app-shop-product-detail-review-time-mobile {';
        echo 'display: none;';
        echo '}';
        echo '#' . $this->id . ' .app-shop-product-detail-review-time-desktop {';
        echo 'display: block;';
        echo '}';
        echo '}';

        echo '</style>';
    }


    private function js()
    {
    }

}

