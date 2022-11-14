
function remove(e) {
    let $e = $(e);
    let $product = $e.closest(".cart-index-product");
    let productId = $product.data("productid");
    let productItemId = $product.data("productitemid");
    $.ajax({
        url: ShopUrl.cartRemove,
        data: {
            "product_id": productId,
            "product_item_id": productItemId
        },
        type: "POST",
        success: function (json) {
            if (json.success) {
                $e.closest(".cart-index-product").remove();
                if ($(".cart-index-product").length === 0) {
                    window.location.reload();
                } else {
                    update();
                }
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("Sysem Error!");
        }
    });
}


function changeQuantity (e, n) {
    var $e = $(e);
    if ($e.hasClass("disabled")) {
        return;
    }

    let $product = $e.closest(".cart-index-product");
    let productId = $product.data("productid");
    let productItemId = $product.data("productitemid");

    var $quantity = $(".cart-index-product-quantity", $product);
    var quantity = $quantity.val();
    if (isNaN(quantity)) {
        quantity = 1;
    } else {
        quantity = Number(quantity);
    }
    quantity += n;
    quantity = parseInt(quantity);
    if (quantity < 1) quantity = 1;
    $quantity.val(quantity);

    $.ajax({
        url: ShopUrl.cartChange,
        data: {
            "product_id": productId,
            "product_item_id": productItemId,
            "quantity": quantity
        },
        type: "POST",
        success: function (json) {
            if (json.success) {
                reloadCart();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("Sysem Error!");
        }
    });
}




/**
 * 更新
 */
function reloadCart() {
    $.ajax({
        url: ShopUrl.cartGetProducts,
        method: "GET",
        success: function (json) {
            if (json.success) {
                cartIndex_products = json.products;
                cartIndex_productTotalQuantity = json.productTotalQuantity;
                cartIndex_productTotalAmount = json.productTotalAmount;
                cartIndex_discountAmount = json.discountAmount;
                cartIndex_totalAmount = json.totalAmount;
                update();
            }
        },
        error: function () {
            alert("Sysem Error!");
        }
    });
}


/**
 * 更新
 */
function update() {
    for(let product of cartIndex_products) {
        let $parent = $("#cart-index-product-item-" + product.item_id);
        if ($parent) {
            $(".cart-index-product-amount", $parent).html(product.amount);
            let $quantityMinus = $(".cart-index-product-quantity-minus", $parent);
            if (product.quantity === 1) {
                $quantityMinus.addClass("disabled");
            } else {
                $quantityMinus.removeClass("disabled");
            }
        }
    }

    $("#cart-index-product-total-amount").html(cartIndex_productTotalAmount);
    $("#cart-index-discount-amount").html(cartIndex_discountAmount);
    $("#cart-index-total-amount").html(cartIndex_totalAmount);
}

