<?php

namespace Be\App\Shop\Section\Product\Detail\Main;

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

        $isMobile = Be::getRequest()->isMobile();

        echo '<div class="app-shop-product-detail-main">';
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }

        echo '<div class="be-row">';
        echo '<div class="be-col-24 be-md-col-8">';


        // ------------------------------------------------------------------------------------------------------------- 左侧图像区
        echo '<div class="app-shop-product-detail-main-images">';
        echo '<div class="be-row" style="height: 100%;">';
        echo '<div class="be-col-0 be-sm-col-auto">';


        echo '<div class="swiper-small">';
        echo '<div class="swiper">';
        echo '<div class="swiper-wrapper">';
        $i = 0;
        foreach ($this->page->product->images as $image) {
            echo '<div class="swiper-slide" data-index="' . $i . '"><img src="' . $image->url . '" alt=""></div>';
            $i++;
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="swiper-button-prev"></div>';
        echo '<div class="swiper-button-next"></div>';
        echo '</div>'; // swiper-small


        echo '</div>';
        echo '<div class="be-col-0 be-sm-col-auto"><div class="be-pl-100"></div></div>';
        echo '<div class="be-col-24 be-sm-col" style="min-width: 0;">';


        echo '<div class="swiper-large">';
        echo '<div class="swiper">';
        echo '<div class="swiper-wrapper">';
        foreach ($this->page->product->images as $image) {
            echo '<div class="swiper-slide">';
            echo '<img src="' . $image->url . '"';
            if (!$isMobile) {
                echo ' class="cloudzoom" data-cloudzoom="tintColor:\'#f7f7f7\', zoomSizeMode:\'image\', zoomImage:\'' . $image->url . '\'"';
            }
            echo '>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>'; // swiper-large

        echo '</div>';
        echo '</div>';
        echo '</div>';
        // ============================================================================================================= 左侧图像区


        echo '</div>';
        echo '<div class="be-col-24 be-md-col-16">';

        echo '<div class="be-row">';
        echo '<div class="be-col-24 be-md-col-auto"><div class="be-pl-200 be-mt-200"></div></div>';
        echo '<div class="be-col-24 be-md-col">';
        $this->summaryRight();
        echo '</div>';
        echo '</div>';

        echo '</div>'; // be-col-24 be-md-col-16
        echo '</div>'; // be-row

        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
        echo '</div>';

        $this->js();
    }


    private function summaryRight()
    {
        $product = $this->page->product;
        $configStore = Be::getConfig('App.Shop.Store');

        echo '<div class="be-mt-100">';
        echo '<h4 class="be-h4 be-lh-200">' . $product->name . '</h4>';
        echo '</div>';

        if (isset($product->summary) && $product->summary) {
            echo '<div class="be-mt-100 be-c-font-6">' . $product->summary . '</div>';
        }

        echo '<div class="be-mt-100 be-c-major">';
        $averageRating = round($product->rating_avg);
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $averageRating) {
                echo '<i class="bi-star-fill"></i>';
            } else {
                echo '<i class="bi-star"></i>';
            }
        }
        echo '<span class="be-pl-100">' . $product->rating_avg . '</span>';
        echo '</div>';


        echo '<div class="be-mt-100 be-bt-eee be-pt-100">';

        echo '<span class="be-fw-bold">' . $configStore->currency . ': </span>';

        if ($product->original_price_to !== '0.00') {
            if ($product->original_price_from !== $product->price_from || $product->original_price_to !== $product->price_to) {
                echo '<span class="be-td-line-through be-ml-50" id="app-shop-product-detail-main-original-price-range">' . $configStore->currencySymbol;
                if ($product->original_price_from === $product->original_price_to) {
                    echo $product->original_price_from;
                } else {
                    echo $product->original_price_from . '~' . $product->original_price_to;;
                }
                echo '</span>';
            }
        }

        echo '<span class="be-fw-bold be-ml-50" id=" app-shop-product-detail-main-price-range">' . $configStore->currencySymbol;
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
            echo '<div class="be-mt-100 be-bt-eee be-pt-100">';
            echo '<span class="be-fw-bold">' . $product->relate->name . ':</span>';
            echo '<span class="be-pl-50">' . $product->relate->value . '</span>';
            echo '</div>';

            echo '<div class="be-row be-mt-100">';
            foreach ($product->relate->items as $relateItem) {
                echo '<div class="be-col-auto be-pr-100 be-pb-50">';

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
                    echo '<div class="be-mt-100 be-bt-eee be-pt-100">';
                    echo '<span class="be-fw-bold">' . $style->name . ':</span>';
                    echo '<span class="be-pl-50" id="app-shop-product-detail-main-style-value-' . $style->id . '"></span>';
                    echo '</div>';

                    echo '<div class="be-row be-mt-100" id="app-shop-product-detail-main-style-' . $style->id . '">';
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
                        echo '<div class="be-col-auto be-pr-100 be-pb-50">';

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

        // ------------------------------------------------------------------------------------------------------------- 右侧摘要
        echo '<form action="' . beUrl('Shop.Cart.checkout', ['from' => 'buy_now']) . '" method="post">';

        echo '<input type="hidden" name="product_id" value="' . $product->id . '">';
        echo '<input type="hidden" name="product_item_id" value="" id="app-shop-product-detail-main-item-id">';

        echo '<div class="be-row be-mt-200">';
        echo '<div class="be-col-auto be-lh-250"><span class="be-fw-bold">Quantity:</span></div>';
        echo '<div class="be-col-auto"><div class="be-pl-100">';
        echo '<input type="number" class="be-input" name="quantity" value="1" id="app-shop-product-detail-main-quantity" />';
        echo '</div></div>';
        echo '</div>';


        echo '<div class="be-row be-mt-200">';
        echo '<div class="be-col-24 be-md-col-auto">';
        echo '<input type="button" class="be-btn be-btn-round be-w-100 be-btn-major add-to-cart" id="app-shop-product-detail-main-add-to-cart" value="Add to Cart">';
        echo '</div>';
        echo '<div class="be-col-24 be-md-col-auto"><div class="be-pl-100 be-mt-100"></div></div>';
        echo '<div class="be-col-24 be-md-col-auto">';
        echo '<input type="button" class="be-btn be-btn-round be-w-100 be-btn-orange buy-now" id="app-shop-product-detail-main-buy-now" value="Buy Now">';
        echo '</div>';
        echo '</div>';

        echo '</form>';
        // ============================================================================================================= 右侧摘要



    }


    private function css()
    {
        $wwwUrl = Be::getProperty('App.Shop')->getWwwUrl();
        $configProduct = Be::getConfig('App.Shop.Product');
        $isMobile = Be::getRequest()->isMobile();

        if (!$isMobile) {
            echo '<link rel="stylesheet" href="' . $wwwUrl . '/lib/cloudzoom/cloudzoom.css" />';
        }

        echo '<link rel="stylesheet" href="' . $wwwUrl . '/lib/swiper/8.3.2/swiper-bundle.min.css">';

        echo '<style rel="stylesheet">';

        echo $this->getCssBackgroundColor('app-shop-product-detail-main');
        echo $this->getCssPadding('app-shop-product-detail-main');
        echo $this->getCssMargin('app-shop-product-detail-main');

        echo '#' . $this->id . ' .app-shop-product-detail-main-images {';
        echo 'position: sticky;';
        echo 'top: 0;';
        echo '}';

        echo '#' . $this->id . ' .swiper-slide {';
        echo 'aspect-ratio: ' . $configProduct->imageAspectRatio . ';';
        echo '}';

        echo '#' . $this->id . ' .swiper-slide:after {';
        echo 'position: absolute;';
        echo 'content: \'\';';
        echo 'left: 0;';
        echo 'top: 0;';
        echo 'width: 100%;';
        echo 'height: 100%;';
        echo 'background: #000;';
        echo 'opacity: .03;';
        echo 'pointer-events: none;';
        echo '}';


        echo '#' . $this->id . ' .swiper-slide img {';
        echo 'display: block;';
        echo 'position: absolute;';
        echo 'left: 0;';
        echo 'right: 0;';
        echo 'top: 0;';
        echo 'bottom: 0;';
        echo 'margin: auto;';
        echo 'max-width: 100%;';
        echo 'max-height: 100%;';
        echo 'transition: all .3s;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small {';
        echo 'padding: 20px 0;';
        echo 'position:relative;';
        echo '--swiper-navigation-size: 20px;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-wrapper {';
        echo 'max-height:60vh;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-slide {';
        echo 'width:40px;';
        echo 'height:64px !important;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-slide-thumb-active {';
        echo 'border: var(--major-color) 1px solid;';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-button-prev, ';
        echo '#' . $this->id . ' .swiper-small .swiper-button-next {';
        echo 'color: var(--major-color);';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-button-prev {';
        echo 'left: 13px;';
        echo 'top: 0;';
        echo 'bottom: auto;';
        echo 'transform: rotate(90deg);';
        echo '}';

        echo '#' . $this->id . ' .swiper-small .swiper-button-next {';
        echo 'left: 13px;';
        echo 'top: auto;';
        echo 'bottom: 0;';
        echo 'transform: rotate(90deg);';
        echo '}';

        echo '#' . $this->id . ' .swiper-large .swiper-slide {';
        echo 'max-height:80vh;';
        echo '}';

        echo '.cloudzoom-zoom {';
        echo 'background-color: #fff;';
        echo '}';

        echo '.cloudzoom-lens {';
        echo 'border: 1px solid var(--font-color-8);';
        echo '}';

        // ------------------------------------------------------------------------------------------------------------- 多款式
        echo '#' . $this->id . ' .style-icon-link {';
        echo 'display: inline-block;';
        echo 'border: var(--font-color-6) 1px solid;';
        echo '}';

        echo '#' . $this->id . ' .style-icon-link-current {';
        echo 'border: var(--major-color) 1px solid !important;';
        echo '}';

        echo '#' . $this->id . ' .style-icon-link-disable {';
        echo 'border: var(--font-color-9) 1px solid !important;';
        echo 'cursor: not-allowed !important;';
        echo '}';

        echo '#' . $this->id . ' .style-icon {';
        echo 'display: inline-block;';
        echo 'border: #fff 2px solid;';
        echo 'border-radius: .1rem;';
        echo '}';

        echo '#' . $this->id . ' .style-icon-text {';
        echo 'padding: 0.25rem 0.75rem;';
        echo '}';

        echo '#' . $this->id . ' .style-icon-image {';
        echo 'display: block;';
        echo 'width: 40px;';
        echo 'aspect-ratio: ' . $configProduct->imageAspectRatio . ';';
        echo 'position: relative;';
        echo '}';

        echo '#' . $this->id . ' .style-icon-image:after {';
        echo 'position: absolute;';
        echo 'content: \'\';';
        echo 'left: 0;';
        echo 'top: 0;';
        echo 'width: 100%;';
        echo 'height: 100%;';
        echo 'background: #000;';
        echo 'opacity: .03;';
        echo 'pointer-events: none;';
        echo '}';

        echo '#' . $this->id . ' .style-icon-image img {';
        echo 'display: block;';
        echo 'position: absolute;';
        echo 'left: 0;';
        echo 'right: 0;';
        echo 'top: 0;';
        echo 'bottom: 0;';
        echo 'margin: auto;';
        echo 'max-width: 100%;';
        echo 'max-height: 100%;';
        echo 'transition: all .3s;';
        echo '}';


        echo '#' . $this->id . ' .style-icon-color {';
        echo 'display: block;';
        echo 'width: 40px;';
        echo 'height: 40px;';
        echo '}';


        echo '#' . $this->id . ' .style-icon-link-current .style-icon-text {';
        echo 'color: var(--major-color);';
        echo '}';

        echo '#' . $this->id . ' .style-icon-link-disable .style-icon-text {';
        echo 'color: var(--font-color-4) !important;';
        echo '}';
        // ============================================================================================================= 多款式

        echo '</style>';
    }


    private function js()
    {
        $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
        $configStore = \Be\Be::getConfig('App.Shop.Store');
        $isMobile = Be::getRequest()->isMobile();

        if (!$isMobile) {
            echo '<script type="text/javascript" src="' . $wwwUrl . '/lib/cloudzoom/cloudzoom.js"></script>';
        }

        echo '<script src="' . $wwwUrl . '/lib/swiper/8.3.2/swiper-bundle.min.js"></script>';
        ?>
        <script>

            let product = <?php echo json_encode($this->page->product); ?>;

            let swiperSmall = new Swiper("#<?php echo $this->id; ?> .swiper-small .swiper", {
                direction: "vertical",
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
                },

                spaceBetween: 10,
                slidesPerView: 'auto'
            });

            let swiperlarge = new Swiper("#<?php echo $this->id; ?> .swiper-large .swiper", {
                thumbs: {
                    swiper: swiperSmall
                }
            });

            $(".swiper-small .swiper-slide").hover(function(){
                swiperlarge.slideTo($(this).data("index"));
            });

            <?php if (!$isMobile) { ?>
                CloudZoom.quickStart();
            <?php } ?>

            let productItemId = "";
            if (product.style === 1) {
                productItemId = product.items[0].id;
            }

            let filterStyle = [];
            let swiperImagesType = 'product';

            function toggleStyle(e, styleId, styleValueIndex) {
                let $e = $(e);
                if ($e.hasClass("style-icon-link-disable")) {
                    if ($e.hasClass("style-icon-link-current")) {
                        $e.removeClass("style-icon-link-current")
                        filterStyle[styleId] = -1;
                    }
                } else {
                    if (!filterStyle.hasOwnProperty(styleId)) {
                        filterStyle[styleId] = -1;
                    }

                    if (filterStyle[styleId] === styleValueIndex) {
                        filterStyle[styleId] = -1;
                    } else {
                        filterStyle[styleId] = styleValueIndex
                    }
                }

                updateStyles();
            }

            function updateStyles() {
                let filterStyleValueIndex;

                // update multiple style classes
                for (let filterStyleId in filterStyle) {
                    filterStyleValueIndex = filterStyle[filterStyleId];
                    $("#app-shop-product-detail-main-style-" + filterStyleId + " .style-icon-link").removeClass("style-icon-link-current");
                    if (filterStyleValueIndex !== -1) {
                        for (let style of product.styles) {
                            if (style.id === filterStyleId) {
                                $("#app-shop-product-detail-main-style-value-" + filterStyleId).html(style.items[filterStyleValueIndex].value);
                                break;
                            }
                        }
                        $("#app-shop-product-detail-main-style-" + filterStyleId + " .style-icon-link").eq(filterStyleValueIndex).addClass("style-icon-link-current");
                    } else {
                        $("#app-shop-product-detail-main-style-value-" + filterStyleId).html("");
                    }
                }

                // calc matched product items
                let matchedItems = [];
                let match = true;
                let currentStyle;
                let currentStyleName;
                let currentStyleValue;
                for (let item of product.items) {
                    match = true;

                    for (let filterStyleId in filterStyle) {
                        filterStyleValueIndex = filterStyle[filterStyleId];
                        if (filterStyleValueIndex !== -1) {
                            currentStyle = false;
                            for (let style of product.styles) {
                                if (style.id === filterStyleId) {
                                    currentStyle = style;
                                    break;
                                }
                            }

                            if (currentStyle) {
                                currentStyleName = currentStyle.name;
                                currentStyleValue = currentStyle.items[filterStyleValueIndex].value;
                                for (let x of item.style_json) {
                                    if (x.name === currentStyleName) {
                                        if (x.value !== currentStyleValue) {
                                            match = false;
                                            break;
                                        }
                                    }
                                }
                            } else {
                                match = false;
                            }
                        }
                    }

                    if (match) {
                        matchedItems.push(item);
                    }
                }

                //console.log(matchedItems);

                // according to the matched product items, update style icons
                if (matchedItems.length === 1) {
                    productItemId = matchedItems[0].id;
                } else {
                    productItemId = "";
                }
                $("#app-shop-product-detail-main-item-id").val(productItemId);

                let originalPriceRange = "";
                let priceRange = "";
                let originalPrice;
                let price;

                // ----------------------------------------------------------------------------------------------------- update price range
                if (matchedItems.length === 1) {
                    originalPrice = matchedItems[0].original_price;
                    price = matchedItems[0].price;
                    if (originalPrice !== "0.00" && originalPrice !== price) {
                        originalPriceRange = originalPrice;
                    }
                    priceRange = price;
                } else if (matchedItems.length > 0) {
                    let originalPriceFrom = -1;
                    let originalPriceTo = -1;
                    let priceFrom = -1;
                    let priceTo = -1;
                    for (let item of matchedItems) {
                        originalPrice = Math.round(Number(item.original_price) * 100);
                        if (originalPriceFrom === -1) {
                            originalPriceFrom = originalPrice;
                        }
                        if (originalPriceTo === -1) {
                            originalPriceTo = originalPrice;
                        }
                        if (originalPrice < originalPriceFrom) {
                            originalPriceFrom = originalPrice;
                        }
                        if (originalPrice > originalPriceTo) {
                            originalPriceTo = originalPrice;
                        }

                        price = Math.round(Number(item.price) * 100);
                        if (priceFrom === -1) {
                            priceFrom = price;
                        }
                        if (priceTo === -1) {
                            priceTo = price;
                        }
                        if (price < priceFrom) {
                            priceFrom = price;
                        }
                        if (price > priceTo) {
                            priceTo = price;
                        }
                    }

                    if (originalPriceTo > 0) {
                        if (originalPriceFrom !== priceFrom || originalPriceTo !== priceTo) {
                            if (originalPriceFrom === originalPriceTo) {
                                originalPriceRange = (originalPriceFrom / 100).toFixed(2);
                            } else {
                                originalPriceRange = (originalPriceFrom / 100).toFixed(2) + "~" + (originalPriceTo / 100).toFixed(2);
                            }
                        }
                    }

                    if (priceFrom === priceTo) {
                        priceRange = (priceFrom / 100).toFixed(2);
                    } else {
                        priceRange = (priceFrom / 100).toFixed(2) + "~" + (priceTo / 100).toFixed(2);
                    }
                }
                let $originalPrice = $("#app-shop-product-detail-main-original-price-range");
                if (originalPriceRange) {
                    $originalPrice.html("<?php echo $configStore->currencySymbol; ?>" + originalPriceRange).show();
                } else {
                    $originalPrice.html("").hide();
                }
                $("#app-shop-product-detail-main-price-range").html("<?php echo $configStore->currencySymbol; ?>" + priceRange);
                // ===================================================================================================== update price range

                // update the "disabled" status of the "buy now" & "add to cart" buttons
                if (matchedItems.length === 1) {
                    $("#app-shop-product-detail-main-buy-now").prop("disabled", false);
                    $("#app-shop-product-detail-main-add-to-cart").prop("disabled", false);
                } else {
                    $("#app-shop-product-detail-main-buy-now").prop("disabled", true);
                    $("#app-shop-product-detail-main-add-to-cart").prop("disabled", true);
                }

                // ----------------------------------------------------------------------------------------------------- update style icons
                let available;
                let styleValue;
                let styleMatchedItems;
                for (let style of product.styles) {
                    for (let styleValueIndex in style.items) {

                        // calc the matched product items when exclude current style
                        styleMatchedItems = [];
                        match = true;
                        for (let item of product.items) {
                            match = true;

                            for (let filterStyleId in filterStyle) {

                                // exclude current style
                                if (filterStyleId === style.id) {
                                    continue;
                                }

                                filterStyleValueIndex = filterStyle[filterStyleId];
                                if (filterStyleValueIndex !== -1) {
                                    currentStyle = false;
                                    for (let style of product.styles) {
                                        if (style.id === filterStyleId) {
                                            currentStyle = style;
                                            break;
                                        }
                                    }

                                    if (currentStyle) {
                                        currentStyleName = currentStyle.name;
                                        currentStyleValue = currentStyle.items[filterStyleValueIndex].value;
                                        for (let x of item.style_json) {
                                            if (x.name === currentStyleName) {
                                                if (x.value !== currentStyleValue) {
                                                    match = false;
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        match = false;
                                    }
                                }
                            }

                            if (match) {
                                styleMatchedItems.push(item);
                            }
                        }

                        styleValue = style.items[styleValueIndex].value;
                        available = false;
                        if (styleMatchedItems.length > 0) {
                            for (let item of styleMatchedItems) {
                                for (let x of item.style_json) {
                                    if (x.name === style.name && x.value === styleValue) {
                                        available = true;
                                        break;
                                    }
                                }

                                if (available) {
                                    break;
                                }
                            }
                        }

                        let $e = $("#app-shop-product-detail-main-style-" + style.id + " .style-icon-link").eq(styleValueIndex);
                        if (available) {
                            if ($e.hasClass("style-icon-link-disable")) {
                                $e.removeClass("style-icon-link-disable");
                            }
                        } else {
                            $e.addClass("style-icon-link-disable");
                        }
                    }
                }
                // ===================================================================================================== update style icons


                // ----------------------------------------------------------------------------------------------------- update image sliders
                let newSwiperImagesType = 'product';
                let swiperImages = product.images;
                if (matchedItems.length === 1) {
                    let matchedItem = matchedItems[0];
                    if (matchedItem.images.length > 0) {
                        newSwiperImagesType = 'product-item:' + matchedItem.id;
                        swiperImages = matchedItem.images;
                    }
                }

                if (newSwiperImagesType !== swiperImagesType) {
                    swiperImagesType = newSwiperImagesType;

                    swiperSmall.removeAllSlides();
                    swiperlarge.removeAllSlides();

                    let swiperImage;
                    for (let i in swiperImages) {
                        swiperImage = swiperImages[i];
                        swiperSmall.appendSlide('<div class="swiper-slide" data-index="' + i + '"><img src="' + swiperImage.url + '" alt=""></div>');

                        <?php if ($isMobile) { ?>
                        swiperlarge.appendSlide('<div class="swiper-slide"><img src="' + swiperImage.url + '" alt=""></div>');
                        <?php } else { ?>
                        swiperlarge.appendSlide('<div class="swiper-slide"><img src="' + swiperImage.url + '" alt="" class="cloudzoom" data-cloudzoom="tintColor:\'#999\', zoomSizeMode:\'image\', zoomImage:\'' + swiperImage.url + '\'"></div>');
                        <?php } ?>
                    }

                    <?php if (!$isMobile) { ?>
                    CloudZoom.quickStart();
                    <?php } ?>

                    $(".swiper-small .swiper-slide").hover(function () {
                        swiperlarge.slideTo($(this).data("index"));
                    });
                }
                // ===================================================================================================== update image sliders
            }

            $(document).ready(function () {

                if (product.style === 2) {
                    let defaultProductItem = product.items[0];
                    let match = false;
                    for (let style of product.styles) {
                        for (let styleValueIndex in style.items) {

                            match = false;
                            for (let x of defaultProductItem.style_json) {
                                if (x.name === style.name && x.value === style.items[styleValueIndex].value) {
                                    match = true;
                                    break;
                                }
                            }

                            // 选中该款式
                            if (match) {
                                filterStyle[style.id] = styleValueIndex;
                                break;
                            }
                        }
                    }
                }

                updateStyles();

                $("#app-shop-product-detail-main-buy-now").click(function () {
                    $(this).closest("form").submit();
                });

                $("#app-shop-product-detail-main-add-to-cart").click(function () {
                    let quantity = $("#app-shop-product-detail-main-quantity").val();
                    if (isNaN(quantity)) {
                        quantity = 1;
                    }
                    quantity = parseInt(quantity);
                    if (quantity < 0) quantity = 1;

                    $.ajax({
                        url: "<?php echo beUrl('Shop.Cart.add'); ?>",
                        data: {
                            "product_id": product.id,
                            "product_item_id": productItemId,
                            "quantity": quantity
                        },
                        type: "POST",
                        success: function (json) {
                            if (json.success) {
                                DrawerCart.load();
                                DrawerCart.show();
                            }
                        },
                        error: function () {
                            alert("System Error!");
                        }
                    });

                });
            });
        </script>
        <?php
    }

}

