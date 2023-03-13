<?php

namespace Be\App\Shop\Section\ProductDetail;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Product.detail'];

    public function display()
    {
        $request = Be::getRequest();
        $isMobile = $request->isMobile();
        $wwwUrl = Be::getProperty('App.Shop')->getWwwUrl();

        if (!$isMobile) {
            echo '<link href="' . $wwwUrl . '/lib/cloudzoom/cloudzoom.css" type="text/css" rel="stylesheet" />';
            echo '<script type="text/javascript" src="' . $wwwUrl . '/lib/cloudzoom/cloudzoom.js"></script>';
        }

        echo '<link rel="stylesheet" href="' . $wwwUrl . '/css/product/detail.css">';

        echo '<style type="text/css">';

        echo $this->getCssBackgroundColor('product-detail');
        echo $this->getCssPadding('product-detail');

        if ($this->config->showDescription === 1) {
            echo $this->getCssBackgroundColor('product-detail-description');
            echo $this->getCssPadding('product-detail-description');
        }

        if ($this->config->showReviews === 1) {
            echo $this->getCssBackgroundColor('product-detail-reviews');
            echo $this->getCssPadding('product-detail-reviews');
        }

        if ($this->config->showDescription === 1) {
            $this->descriptionCss();
        }
        if ($this->config->showReviews === 1) {
            $this->reviewCss();
        }

        echo '#' . $this->id . ' .swiper-slide img {';
        echo 'max-width: 100%;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small {';
        echo 'padding: 0 60px;';
        //echo 'overflow: hidden;';
        echo 'position:relative;';
        echo '--swiper-navigation-size: 20px;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-slide-thumb-active {';
        echo 'border: var(--major-color) 1px solid;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-button-prev, ';
        echo '#' . $this->id . ' .swiper-small .swiper-button-next {';
        echo 'color: var(--major-color);';
        echo '}';

        echo '.cloudzoom-zoom {';
        echo 'background-color: #fff;';
        echo '}';

        echo '.cloudzoom-lens {';
        echo 'border: 1px solid #999;';
        echo '}';

       echo '</style>';

        echo '<div class="product-detail">';
        if ($this->position === 'middle') echo '<div class="be-container">';

        echo '<div class="be-row">';
        echo '<div class="be-col-24 be-lg-col-auto">';
        echo '<div style="max-width:450px; margin: 0 auto;">';

        echo '<div class="swiper-large">';
        echo '<div class="swiper">';
        echo '<div class="swiper-wrapper">';
        foreach ($this->page->product->images as $image) {
            echo '<div class="swiper-slide">';
            echo '<img src="' . $image->url . '"';
            if (!$isMobile) {
                echo ' class="cloudzoom" data-cloudzoom="tintColor:\'#999\', zoomSizeMode:\'image\', zoomImage:\'' . $image->url . '\'"';
            }
            echo '>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="swiper-small be-mt-100">';
        echo '<div class="swiper">';
        echo '<div class="swiper-wrapper">';
        $i = 0;
        foreach ($this->page->product->images as $image) {
            echo '<div class="swiper-slide" data-index="'. $i . '"><img src="' . $image->url . '" alt=""></div>';
            $i++;
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="swiper-button-prev"></div>';
        echo '<div class="swiper-button-next"></div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '<div class="be-col-24 be-lg-col-auto"><div class="be-pl-200 be-mt-200"></div></div>';

        echo '<div class="be-col-24 be-lg-col">';
        $this->summaryRight();
        echo '</div>';
        echo '</div>';

        if ($this->position === 'middle') echo '</div>';
        echo '</div>';

        if ($this->config->showDescription === 1) {
            $this->description();
        }
        if ($this->config->showReviews === 1) {
            $this->reviews();
        }

        $key = 'App:Shop:swiper';
        if (!Be::hasContext($key)) {
            $wwwUrl = Be::getProperty('App.Shop')->getWwwUrl();
            echo '<link rel="stylesheet" href="' . $wwwUrl . '/lib/swiper/8.3.2/swiper-bundle.min.css">';
            echo '<script src="' . $wwwUrl . '/lib/swiper/8.3.2/swiper-bundle.min.js"></script>';
        }

        echo '<script>';

        echo 'let isMobile = ' . ($isMobile ? 'true' : 'false') . ';';

        echo 'let product = ' . json_encode($this->page->product) . ';';
        $productItemId = '';
        if ($this->page->product->style === 1) {
            $productItemId = $this->page->product->items[0]->id;
        }
        echo 'let productItemId = "' . $productItemId . '";';

        echo 'let addToCartUrl = "' . beUrl('Shop.Cart.Add') . '";';

        echo 'var swiperSmall = new Swiper("#' . $this->id . ' .swiper-small .swiper", {';
        echo 'navigation: {';
        echo 'nextEl: ".swiper-button-next",';
        echo 'prevEl: ".swiper-button-prev"';
        echo '},';

        echo 'spaceBetween: 20,';
        echo 'slidesPerView: 3,';

        echo 'breakpoints: {';
        echo '768: {';
        echo 'slidesPerView: 4';
        echo '},';
        echo '1680: {';
        echo 'slidesPerView: 5';
        echo '}';
        echo '}';

        echo '});';

        echo 'var swiperlarge = new Swiper("#' . $this->id . ' .swiper-large .swiper", {';
        echo 'thumbs: {';
        echo 'swiper: swiperSmall';
        echo '}';
        echo '});';

        // 处理点击过于频繁时失效
        echo '$(".swiper-small .swiper-slide").hover(function(){';
        echo 'swiperlarge.slideTo($(this).data("index"));';
        echo '});';

        if ($this->config->showDescription === 1) {
            $this->descriptionJs();
        }
        if ($this->config->showReviews === 1) {
            $this->reviewJs();
        }

        if (!$isMobile) {
            echo 'CloudZoom.quickStart();';
        }

        echo '</script>';
        echo '<script src="' . $wwwUrl . '/js//product/detail.js?v=20221120"></script>';

    }


    private function summaryRight()
    {
        $product = $this->page->product;
        $configStore = Be::getConfig('App.Shop.Store');

        echo '<form action="' . beUrl('Shop.Cart.checkout', ['from' => 'buy_now']) . '" method="post">';

        echo '<input type="hidden" name="product_id" value="' . $product->id . '">';
        echo '<input type="hidden" name="product_item_id" value="" id="product-detail-item-id">';

        echo '<div class="be-mt-100">';
        echo '<h4 class="be-h4 be-lh-200">' . $product->name . '</h4>';
        echo '</div>';

        if (isset($product->summary) && $product->summary) {
            echo '<div class="be-mt-100 be-c-bbb">' . $product->summary . '</div>';
        }

        echo '<div class="be-mt-200 be-bb be-pb-100 be-mb-100">';

        echo '<span class="be-fw-bold">' . $configStore->currency . ': </span>';

        if ($product->original_price_to !== '0.00') {
            if ($product->original_price_from !== $product->price_from || $product->original_price_to !== $product->price_to) {
                echo '<span class="be-td-line-through be-ml-50" id="product-detail-original-price-range">' . $configStore->currencySymbol;
                if ($product->original_price_from === $product->original_price_to) {
                    echo $product->original_price_from;
                } else {
                    echo $product->original_price_from . '~' . $product->original_price_to;;
                }
                echo '</span>';
            }
        }

        echo '<span class="be-fw-bold be-ml-50" id="product-detail-price-range">' . $configStore->currencySymbol;
        if ($product->price_from === $product->price_to) {
            echo $product->price_from;
        } else {
            echo $product->price_from . '~' . $product->price_to;
        }

        echo '</span>';
        echo '</div>';

        if (count($product->promotion_templates) > 0) {
            foreach ($product->promotion_templates as $promotion_template) {
                echo $promotion_template;
            }
        }

        if ($product->relate_id !== '') {
            echo '<div class="be-mt-200">';
            echo '<span class="be-fw-bold">' . $product->relate->name . ':</span>';
            echo '<span class="be-pl-50">' . $product->relate->value . '</span>';
            echo '</div>';

            echo '<div class="be-row be-mt-100">';
            foreach ($product->relate->items as $relateItem) {
                echo '<div class="be-col-auto be-pr-100">';

                echo '<a class="style-icon-link style-icon-link-' . $product->relate->icon_type;
                if ($relateItem->self) {
                    echo ' style-icon-link-current" href="javascript:void(0);"';
                } else {
                    echo '" href="' . $relateItem->url . '"';
                }
                echo ' title="' . $relateItem->value . '">';

                if ($product->relate->icon_type === 'text') {
                    echo '<span class="style-icon style-icon-text">';
                    echo $relateItem->value;
                    echo '</span>';
                } elseif ($product->relate->icon_type === 'image') {
                    echo '<span class="style-icon style-icon-image">';
                    echo '<img src="' . $relateItem->icon_image . '" alt="' . $relateItem->value . '">';
                    echo '</span>';
                } else {
                    echo '<span class="style-icon style-icon-color" style="background-color:' . $relateItem->icon_color . '"></span>';
                }

                echo '</a>';
                echo '</div>';
            }
            echo '</div>';
        }

        // 多款式
        if ($product->style === 2) {
            if (isset($product->styles) && is_array($product->styles) && count($product->styles) > 0) {
                foreach ($product->styles as $style) {
                    echo '<div class="be-mt-200">';
                    echo '<span class="be-fw-bold">' . $style->name . ':</span>';
                    echo '<span class="be-pl-50" id="product-detail-style-value-' . $style->id . '"></span>';
                    echo '</div>';

                    echo '<div class="be-row be-mt-100" id="product-detail-style-' . $style->id . '">';
                    /*
                    foreach ($style->values as $styleValueIndex => $styleValue) {
                        echo '<div class="be-col-auto be-pr-100">';
                        echo '<a class="style-icon-link style-icon-link-text" href="javascript:void(0);" onclick="toggleStyle(this, \'' . $style->id . '\',' . $styleValueIndex . ')" title="' . $styleValue . '">';
                        echo '<span class="style-icon style-icon-text">';
                        echo $styleValue;
                        echo '</span>';
                        echo '</a>';
                        echo '</div>';
                    }
                    */
                    foreach ($style->items as $styleItemIndex => $styleItem) {
                        echo '<div class="be-col-auto be-pr-100">';


                        echo '<a class="style-icon-link style-icon-link-' . $style->icon_type . '" href="javascript:void(0);" onclick="toggleStyle(this, \'' . $style->id . '\',' . $styleItemIndex . ')" title="' . $styleItem->value . '">';

                        if ($style->icon_type === 'text') {
                            echo '<span class="style-icon style-icon-text">';
                            echo $styleItem->value;
                            echo '</span>';
                        } elseif ($style->icon_type === 'image') {
                            echo '<span class="style-icon style-icon-image">';
                            echo '<img src="' . $styleItem->icon_image . '" alt="' . $styleItem->value . '">';
                            echo '</span>';
                        } else {
                            echo '<span class="style-icon style-icon-color" style="background-color:' . $styleItem->icon_color . '"></span>';
                        }
                        echo '</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
        }

        echo '<label class="be-d-block be-mt-200 be-fw-bold" for="quantity">Quantity: </label>';
        echo '<div class="be-mt-50">';
        echo '<div class="be-p-relative" style="max-width: 12rem;">';
        echo '<button class="be-btn be-btn-round be-p-absolute be-l-0 be-t-0 be-b-0" style="border-top-right-radius: 0; border-bottom-right-radius: 0;" type="button" onclick="changeQuantity(-1);">-</button>';
        echo '<input class="be-input round-be-input be-px-200 be-ta-center" id="product-detail-quantity" name="quantity" type="text" value="1" maxlength="3" onkeyup="changeQuantity(0);">';
        echo '<button class="be-btn be-btn-round be-p-absolute be-r-0 be-t-0 be-b-0" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" type="button" onclick="changeQuantity(1);">+</button>';
        echo '</div>';
        echo '</div>';

        echo '<div class="be-row be-mt-200">';
        echo '<div class="be-col-12 be-lg-col-6 be-pr-100">';
        echo '<input type="button" class="be-btn be-btn-major be-btn-lg be-btn-round be-w-100 buy-now" id="product-detail-buy-now" value="Buy Now">';
        echo '</div>';
        echo '<div class="be-col-12 be-lg-col-6 be-pl-100">';
        echo '<input type="button" class="be-btn be-btn-lg be-btn-round be-w-100 add-to-cart" id="product-detail-add-to-cart" value="Add to Cart">';
        echo '</div>';
        echo '</div>';

        echo '</form>';
    }

    private function descriptionCss()
    {
    }

    private function descriptionJs()
    {
    }

    private function description()
    {
        echo '<div class="product-detail-description be-mt-200">';
        echo '<div class="be-container">';
        echo '<h4 class="be-h4 be-lh-200 be-mb-100">Description</h4>';
        echo '<div>';
        echo $this->page->product->description;
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }


    private function reviewCss()
    {
        echo '#' . $this->id . ' .product-detail-review {';
        echo 'border-top: #eee 1px solid;';
        echo '}';

        echo '#' . $this->id . ' .product-detail-review-avatar {';
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

        echo '#' . $this->id . ' .product-detail-review-avatar-letter {';
        echo 'width: 3rem;';
        echo 'height: 3rem;';
        echo 'line-height: 3rem;';
        echo 'color: #fff;';
        echo 'text-align: center;';
        echo 'font-size: 20px;';
        echo '}';

        echo '#' . $this->id . ' .product-detail-review-avatar-icon {';
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

        echo '#' . $this->id . ' .product-detail-review-content,';
        echo '#' . $this->id . ' .product-detail-review-images {';
        echo 'margin: .5rem 0 0 4rem;';
        echo '}';

        echo '#' . $this->id . ' .product-detail-review-images a {';
        echo 'display: inline-block;';
        echo 'width: 4rem;';
        echo 'height: 4rem;';
        echo 'margin-right: .5rem;';
        echo 'margin-top: .5rem;';
        echo '}';

        echo '#' . $this->id . ' .product-detail-review-images a img {';
        echo 'max-width: 100%;';
        echo 'max-height: 100%;';
        echo 'vertical-align: middle;';
        echo '}';

        // 手机端
        echo '@media (max-width: 768px) {';
        echo '#' . $this->id . ' .product-detail-review-time-mobile {';
        echo 'display: block;';
        echo '}';
        echo '#' . $this->id . ' .product-detail-review-time-desktop {';
        echo 'display: none;';
        echo '}';
        echo '#' . $this->id . ' .product-detail-review-content,';
        echo '#' . $this->id . ' .product-detail-review-images {';
        echo 'margin-left: 0 !important;';
        echo '}';
        echo '}';

        // 电脑端
        echo '@media (min-width: 768px) {';
        echo '#' . $this->id . ' .product-detail-review-time-mobile {';
        echo 'display: none;';
        echo '}';
        echo '#' . $this->id . ' .product-detail-review-time-desktop {';
        echo 'display: block;';
        echo '}';
        echo '}';
    }

    private function reviewJs()
    {
    }

    private function reviews()
    {
        $reviewAverageRating = $this->page->product->rating_avg;
        $reviewCount = $this->page->product->rating_count;
        $reviewPageSize = $this->config->reviewPageSize;
        $reviewPage = \Be\Be::getRequest()->get('reviewPage', 1);
        $reviews = \Be\Be::getService('App.Shop.ProductReview')->getReviews($this->page->product->id, [
            'page' => $reviewPage,
            'pageSize' => $reviewPageSize,
        ], ['images' => true]);

        echo '<div class="product-detail-reviews be-mt-200">';
        echo '<div class="be-container">';

        echo '<h4 class="be-h4 be-lh-200">' . $this->config->reviewTitle . '</h4>';

        echo '<div class="be-row">';
        echo '<div class="be-col-24 be-col-sm-16 be-mt-100">';
        $roundReviewAverageRating = round($reviewAverageRating);
        echo '<span class="be-mr-50 be-va-middle">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $roundReviewAverageRating) {
                echo '<i class="icon-star-fill icon-star-fill-150"></i>';
            } else {
                echo '<i class="icon-star icon-star-150"></i>';
            }
        }
        echo '</span>';
        echo $reviewCount . ' review(s)';
        echo '</div>';
        echo '<div class="be-col-24 be-col-sm-8 be-ta-right be-mt-100">';
        echo '<a href="' . beUrl('Shop.Product.review', ['id' => $this->page->product->id]) . '" class="be-btn be-btn-round">Write a review</a>';
        echo '</div>';
        echo '</div>';

        if ($reviewCount > 0) {
            echo '<div class="be-mt-200">';
            foreach ($reviews as $review) {
                echo '<div class="product-detail-review be-py-100">';

                echo '<div class="be-fc">';
                echo '<div class="product-detail-review-avatar">';
                echo '<div class="product-detail-review-avatar-letter">' . substr($review->name, 0, 1) . '</div>';
                if ($review->user_id > 0) {
                    echo '<i class="product-detail-review-avatar-icon"></i>';
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
                echo '<div class="product-detail-review-time-desktop be-fr be-c-999">' . date('m/d/y', strtotime($review->create_time)) . '</div>';
                echo '</div>';

                echo '<div class="product-detail-review-content">' . $review->content . '</div>';

                if (isset($review->images) && count($review->images) > 0) {
                    echo '<div class="product-detail-review-images">';
                    foreach ($review->images as $reviewImage) {
                        echo '<a class="light-box-image" data-lightbox="roadtrip" href="' . $reviewImage->url . '" target="_blank">';
                        echo '<img src="' . $reviewImage->url . '" />';
                        echo '</a>';
                    }
                    echo '</div>';
                }

                echo '<div class="product-detail-review-time-mobile be-c-999 be-pt-50">';
                echo date('m/d/y', strtotime($review->create_time));
                echo '</div>';

                echo '</div>';
            }
            echo '</div>';

            $total = $reviewCount;
            $pageSize = $reviewPageSize;
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

        echo '</div>';
        echo '</div>';
    }

}

