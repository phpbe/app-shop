<?php

namespace Be\App\Shop\Section\Cart\Index;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Cart.index'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $this->css();

        echo '<div class="app-shop-cart-index">';
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }

        if (count($this->page->products) == 0) {
            ?>
            <div class="be-py-400 be-my-400 be-ta-center be-c-999">Your shopping cart is empty.</div>
            <?php
        } else {

            $configStore = \Be\Be::getConfig('App.Shop.Store');

            $isMobile = Be::getRequest()->isMobile();
            ?>
            <form action="<?php echo beUrl('Shop.Cart.checkout'); ?>" method="post">
                <div class="be-row">
                    <div class="be-col-24 be-lg-col-16">
                        <div class="app-shop-cart-index-products">
                            <?php
                            if ($isMobile) {
                                ?>
                                <?php
                                foreach ($this->page->products as $product) {
                                    ?>
                                    <div class="be-row be-mt-100 app-shop-cart-index-product" id="app-shop-cart-index-product-item-<?php echo $product->product_item_id; ?>" data-productid="<?php echo $product->product_id; ?>" data-productitemid="<?php echo $product->product_item_id; ?>">
                                        <div class="be-col-auto">
                                            <div class="app-shop-cart-index-product-image">
                                                <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                            </div>
                                        </div>
                                        <div class="be-col-auto">
                                            <div class="be-pl-50"></div>
                                        </div>
                                        <div class="be-col">
                                            <div>
                                                <?php echo $product->name; ?>
                                            </div>

                                            <div class="be-mt-50 be-c-font-4">
                                                <?php echo $product->style; ?>
                                            </div>

                                            <div class="be-mt-50">
                                                <?php echo $configStore->currencySymbol . $product->price; ?>
                                            </div>

                                            <div class="be-mt-50 app-shop-cart-index-product-quantity-input">
                                                <button class="app-shop-cart-index-product-quantity-minus" type="button" onclick="changeQuantity(this, -1);">-</button>
                                                <input class="be-input app-shop-cart-index-product-quantity" name="quantity[]" type="text" value="<?php echo $product->quantity; ?>" maxlength="3" onkeyup="changeQuantity(this, 0);">
                                                <button class="app-shop-cart-index-product-quantity-plus" type="button" onclick="changeQuantity(this, 1);">+</button>
                                            </div>
                                        </div>
                                        <div class="be-col-auto">
                                            <div class="be-pl-50 be-fs-125">
                                                <a href="javascript:void(0);" onclick="remove(this);"><i class="bi-x-lg"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?php
                            } else {
                                ?>
                                <table style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th style="width: 120px;"></th>
                                        <th style="text-align: left;">Products</th>
                                        <th>Price</th>
                                        <th style="width: 8rem;">Quantity</th>
                                        <th class="cart-index-product-head-amount">Total</th>
                                        <th style="width: 30px;"></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    foreach ($this->page->products as $product) {
                                        ?>
                                        <tr><td class="be-pt-100" colspan="5"></td></tr>
                                        <tr class="app-shop-cart-index-product" id="app-shop-cart-index-product-item-<?php echo $product->product_item_id; ?>" data-productid="<?php echo $product->product_id; ?>" data-productitemid="<?php echo $product->product_item_id; ?>">
                                            <td>
                                                <input type="hidden" name="product_id[]" value="<?php echo $product->product_id; ?>">
                                                <input type="hidden" name="product_item_id[]" value="<?php echo $product->product_item_id; ?>">
                                                <div class="app-shop-cart-index-product-image">
                                                    <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?php echo $product->url; ?>">
                                                    <?php echo $product->name; ?>
                                                </a>
                                                <div class="be-mt-50 be-c-font-4">
                                                    <?php echo $product->style; ?>
                                                </div>
                                            </td>

                                            <td class="be-ta-center">
                                                <?php echo $configStore->currencySymbol . $product->price; ?>
                                            </td>

                                            <td>
                                                <div class="app-shop-cart-index-product-quantity-input">
                                                    <button class="app-shop-cart-index-product-quantity-minus" type="button" onclick="changeQuantity(this, -1);">-</button>
                                                    <input class="be-input app-shop-cart-index-product-quantity" name="quantity[]" type="text" value="<?php echo $product->quantity; ?>" maxlength="3" onkeyup="changeQuantity(this, 0);">
                                                    <button class="app-shop-cart-index-product-quantity-plus" type="button" onclick="changeQuantity(this, 1);">+</button>
                                                </div>
                                            </td>

                                            <td class="be-ta-center">
                                                <?php echo $configStore->currencySymbol; ?><span class="app-shop-cart-index-product-amount"><?php echo $product->amount; ?></span>
                                            </td>

                                            <td>
                                                <a href="javascript:void(0);" onclick="remove(this);"><i class="bi-x-lg"></i></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="be-col-12 be-lg-col-0"><div class="be-mt-200"></div></div>
                    <div class="be-col-24 be-lg-col-8">

                        <div class="be-row" style="position: sticky; top: 2rem;">
                            <div class="be-col-0 be-lg-col-auto"><div class="be-pl-200"></div></div>
                            <div class="be-col-24 be-lg-col">

                                <div class="be-fs-125 be-pb-100 be-mb-200" style="border-bottom: #ddd 1px solid;">Order Summary</div>

                                <div class="be-row">
                                    <div class="be-col-auto">
                                        Subtotal
                                    </div>
                                    <div class="be-col be-ta-right">
                                        <?php echo $configStore->currencySymbol; ?><span id="app-shop-cart-index-product-total-amount"><?php echo $this->page->productTotalAmount; ?></span>
                                    </div>
                                </div>
                                <div class="be-row be-mt-100">
                                    <div class="be-col-auto">
                                        Discount
                                    </div>
                                    <div class="be-col be-ta-right">
                                        <?php echo $configStore->currencySymbol; ?><span id="app-shop-cart-index-discount-amount"><?php echo $this->page->discountAmount; ?></span>
                                    </div>
                                </div>

                                <div class="be-mt-200 be-pt-200" style="border-top: #eee 1px solid;">
                                    <div class="be-row">
                                        <div class="be-col-auto be-fs-125">
                                            Total
                                        </div>
                                        <div class="be-col be-ta-right be-fs-150">
                                            <?php echo $configStore->currencySymbol; ?><span id="app-shop-cart-index-total-amount"><?php echo $this->page->totalAmount; ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="be-mt-200 be-ta-right">
                                    <input type="submit" class="be-btn be-btn-major be-btn-lg" value="Check out now" />
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </form>
            <?php
        }


        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
        echo '</div>';

        $this->js();
    }


    private function css()
    {
        $isMobile = Be::getRequest()->isMobile();

        ?>
        <style>
            <?php
            echo $this->getCssBackgroundColor('app-shop-cart-index');
            echo $this->getCssPadding('app-shop-cart-index');
            echo $this->getCssMargin('app-shop-cart-index');
            ?>

            .app-shop-cart-index-products {
            }

            .app-shop-cart-index-product-image {
                position: relative;
                <?php
                $configProduct = Be::getConfig('App.Shop.Product');
                echo 'aspect-ratio: ' . $configProduct->imageAspectRatio . ';';
                ?>
            }

            .app-shop-cart-index-product-image:after {
                position: absolute;
                content: '';
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: #000;
                opacity: .03;
                pointer-events: none;
            }

            .app-shop-cart-index-product-image img {
                display: block;
                position: absolute;
                left: 0;
                right: 0;
                top: 0;
                bottom: 0;
                margin: auto;
                max-width: 100%;
                max-height: 100%;
                transition: all .3s;
            }

            .app-shop-cart-index-product-quantity-input {
                position: relative;
                min-width: 6rem;
                max-width: 8rem;
            }

            .app-shop-cart-index-product-quantity-minus,
            .app-shop-cart-index-product-quantity-plus {
                position: absolute;
                top: 1px;
                bottom: 1px;
                width: 2rem;
                border: none;
                outline: 0;
                color: #aaa;
                background-color: #f6f6f6;
                border-radius: .25rem;
                cursor: pointer;
            }

            .app-shop-cart-index-product-quantity-minus:hover,
            .app-shop-cart-index-product-quantity-plus:hover {
                color: #666;
                background-color: #eee;
            }

            .app-shop-cart-index-product-quantity-minus.disabled,
            .app-shop-cart-index-product-quantity-plus.disabled,
            .app-shop-cart-index-product-quantity-minus:disabled,
            .app-shop-cart-index-product-quantity-plus:disabled {
                color: #ccc !important;
                background-color: #fafafa !important;
                pointer-events: none;
            }

            .app-shop-cart-index-product-quantity-minus {
                left: 1px;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }

            .app-shop-cart-index-product-quantity-plus {
                right: 1px;
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }

            .app-shop-cart-index-product-quantity {
                text-align: center;
                padding-left: 2rem;
                padding-right: 2rem;
            }

            <?php if ($isMobile) { ?>
            .app-shop-cart-index-product {
                border: #eee 1px solid;
                padding: 1rem .25rem;
            }

            .app-shop-cart-index-product-image {
                width: 60px;
            }
            <?php } else { ?>
            
            .app-shop-cart-index-product-image {
                width: 80px;
            }
            
            .app-shop-cart-index-products table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
            }
            
            .app-shop-cart-index-product {
                border: #eee 1px solid;
            }
            
            .app-shop-cart-index-products th {
                background-color: #fafafa;
                text-align: center;
                font-weight: 500;
                padding: 1rem .5rem;
            }
            
            .app-shop-cart-index-product td {
                padding: 1rem .5rem;
            }
            
            .app-shop-cart-index-products th:first-child,
            .app-shop-cart-index-product td:first-child {
                padding-left: 1rem;
            }
            
            .app-shop-cart-index-products th:last-child,
            .app-shop-cart-index-product td:last-child {
                padding-right: 1rem;
            }
            
            @media (max-width: 1200px) {
                .app-shop-cart-index-products th {
                    padding: .5rem .25rem;
                }

                .app-shop-cart-index-product td {
                    padding: .5rem .25rem;
                }

                .app-shop-cart-index-products th:first-child,
                .app-shop-cart-index-product td:first-child {
                    padding-left: .5rem;
                }

                .app-shop-cart-index-products th:last-child,
                .app-shop-cart-index-product td:last-child {
                    padding-right: .5rem;
                }
            }
            <?php } ?>
        </style>
        <?php
    }

    private function js()
    {
        ?>
        <script>
            let products = <?php echo json_encode($this->page->products); ?>;
            let productTotalQuantity = <?php echo $this->page->productTotalQuantity; ?>;
            let productTotalAmount = "<?php echo $this->page->productTotalAmount; ?>";
            let discountAmount = "<?php echo $this->page->discountAmount; ?>";
            let totalAmount = "<?php echo $this->page->totalAmount; ?>";

            function remove(e) {
                let $e = $(e);
                let $product = $e.closest(".app-shop-cart-index-product");
                let productId = $product.data("productid");
                let productItemId = $product.data("productitemid");
                $.ajax({
                    url: "<?php echo beUrl('Shop.Cart.remove'); ?>",
                    data: {
                        "product_id": productId,
                        "product_item_id": productItemId
                    },
                    type: "POST",
                    success: function (json) {
                        if (json.success) {
                            $e.closest(".app-shop-cart-index-product").remove();
                            if ($(".app-shop-cart-index-product").length === 0) {
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

                let $product = $e.closest(".app-shop-cart-index-product");
                let productId = $product.data("productid");
                let productItemId = $product.data("productitemid");

                var $quantity = $(".app-shop-cart-index-product-quantity", $product);
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
                    url: "<?php echo beUrl('Shop.Cart.change'); ?>",
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

            function reloadCart() {
                $.ajax({
                    url: "<?php echo beUrl('Shop.Cart.getProducts'); ?>",
                    method: "GET",
                    success: function (json) {
                        if (json.success) {
                            products = json.products;
                            productTotalQuantity = json.productTotalQuantity;
                            productTotalAmount = json.productTotalAmount;
                            discountAmount = json.discountAmount;
                            totalAmount = json.totalAmount;
                            update();
                        }
                    },
                    error: function () {
                        alert("Sysem Error!");
                    }
                });
            }

            function update() {
                for(let product of products) {
                    let $parent = $("#app-shop-cart-index-product-item-" + product.item_id);
                    if ($parent) {
                        $(".app-shop-cart-index-product-amount", $parent).html(product.amount);
                        let $quantityMinus = $(".app-shop-cart-index-product-quantity-minus", $parent);
                        if (product.quantity === 1) {
                            $quantityMinus.addClass("disabled");
                        } else {
                            $quantityMinus.removeClass("disabled");
                        }
                    }
                }

                $("#app-shop-cart-index-product-total-amount").html(productTotalAmount);
                $("#app-shop-cart-index-discount-amount").html(discountAmount);
                $("#app-shop-cart-index-total-amount").html(totalAmount);
            }
        </script>
        <?php
    }
}

