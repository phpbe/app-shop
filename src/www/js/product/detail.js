
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

    // 更新多款式的UI样式
    for (let filterStyleId in filterStyle) {
        filterStyleValueIndex = filterStyle[filterStyleId];
        $("#product-detail-style-" + filterStyleId + " .style-icon-link").removeClass("style-icon-link-current");
        if (filterStyleValueIndex !== -1) {
            for (let style of product.styles) {
                if (style.id === filterStyleId) {
                    $("#product-detail-style-value-" + filterStyleId).html(style.values[filterStyleValueIndex]);
                    break;
                }
            }
            $("#product-detail-style-" + filterStyleId + " .style-icon-link").eq(filterStyleValueIndex).addClass("style-icon-link-current");
        } else {
            $("#product-detail-style-value-" + filterStyleId).html("");
        }
    }

    // 获取匹配上的产品子项列表
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
                    currentStyleValue = currentStyle.values[filterStyleValueIndex];
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

    // 跟据匹配上的子项列表，更新款式
    if (matchedItems.length === 1) {
        productItemId = matchedItems[0].id;
    } else {
        productItemId = "";
    }
    $("#product-detail-item-id").val(productItemId);

    let originalPriceRange = "";
    let priceRange = "";
    let originalPrice;
    let price;

    // ----------------------------------------------------------------------------------------------------------------- 价格范围
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
    let $originalPrice = $("#product-detail-original-price-range");
    if (originalPriceRange) {
        $originalPrice.html("$" + originalPriceRange).show();
    } else {
        $originalPrice.html("").hide();
    }
    $("#product-detail-price-range").html("$" + priceRange);
    // ================================================================================================================= 价格范围

    // 购买，加入购物车按钮是否禁用
    if (matchedItems.length === 1) {
        $("#product-detail-buy-now").prop("disabled", false);
        $("#product-detail-add-to-cart").prop("disabled", false);
    } else {
        $("#product-detail-buy-now").prop("disabled", true);
        $("#product-detail-add-to-cart").prop("disabled", true);
    }

    // ----------------------------------------------------------------------------------------------------------------- 更新款式按钮是否可点击
    let available;
    let styleValue;
    let styleMatchedItems;
    for (let style of product.styles) {
        for (let styleValueIndex in style.values) {

            // 获取排除当前款式时，匹配上的产品子项列表
            styleMatchedItems = [];
            match = true;
            for (let item of product.items) {
                match = true;

                for (let filterStyleId in filterStyle) {

                    // 排除当前款式时
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
                            currentStyleValue = currentStyle.values[filterStyleValueIndex];
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

            styleValue = style.values[styleValueIndex];
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

            let $e = $("#product-detail-style-" + style.id + " .style-icon-link").eq(styleValueIndex);
            if (available) {
                if ($e.hasClass("style-icon-link-disable")) {
                    $e.removeClass("style-icon-link-disable");
                }
            } else {
                $e.addClass("style-icon-link-disable");
            }
        }
    }
    // ================================================================================================================= 更新款式按钮是否可点击



    // ----------------------------------------------------------------------------------------------------------------- 更新轮播图
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
        swiperSmall.removeAllSlides();
        swiperlarge.removeAllSlides();
        let swiperImage;
        for (let i in swiperImages) {
            swiperImage = swiperImages[i];
            swiperSmall.appendSlide('<div class="swiper-slide" data-index="' + i + '"><img src="' + swiperImage.url + '" alt=""></div>');
            if (isMobile) {
                swiperlarge.appendSlide('<div class="swiper-slide"><img src="' + swiperImage.url + '" alt=""></div>');
            } else {
                swiperlarge.appendSlide('<div class="swiper-slide"><img src="' + swiperImage.url + '" alt="" class="cloudzoom" data-cloudzoom="tintColor:\'#999\', zoomSizeMode:\'image\', zoomImage:\'' + swiperImage.url + '\'"></div>');
            }
        }

        $(".swiper-small .swiper-slide").on("click", function(){
            swiperlarge.slideTo($(this).data("index"));
        });

        if (!isMobile) {
            CloudZoom.quickStart();
        }
    }
    // ================================================================================================================= 更新轮播图
}

$(document).ready(function () {

    updateStyles();

    $("#product-detail-buy-now").click(function () {
        $(this).closest("form").submit();
    });

    $("#product-detail-add-to-cart").click(function () {
        let quantity = $("#product-detail-quantity").val();
        if (isNaN(quantity)) {
            quantity = 1;
        }
        quantity = parseInt(quantity);
        if (quantity < 0) quantity = 1;

        $.ajax({
            url: addToCartUrl,
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

function changeQuantity(n) {
    let $e = $("#product-detail-quantity");
    let quantity = $e.val();
    if (isNaN(quantity)) {
        quantity = 1;
    } else {
        quantity = Number(quantity);
    }
    quantity += n;
    quantity = parseInt(quantity);
    if (quantity < 1) quantity = 1;
    $e.val(quantity);
}

function addToCart() {

}