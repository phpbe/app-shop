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
                    <div class="be-col-24 be-lg-col-7">

                        <div class="be-row">
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

        echo '<style>';
        echo $this->getCssBackgroundColor('app-shop-cart-index');
        echo $this->getCssPadding('app-shop-cart-index');
        echo $this->getCssMargin('app-shop-cart-index');

        echo '.app-shop-cart-index-products {';
        echo '}';

        echo '.app-shop-cart-index-product-image {';
        echo 'position: relative;';
        $configProduct = Be::getConfig('App.Shop.Product');
        echo 'aspect-ratio: ' . $configProduct->imageAspectRatio . ';';
        echo '}';

        echo '.app-shop-cart-index-product-image:after {';
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

        echo '.app-shop-cart-index-product-image img {';
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
        
        echo '.app-shop-cart-index-product-quantity-input {';
        echo 'position: relative;';
        echo 'min-width: 6rem;';
        echo 'max-width: 8rem;';
        echo '}';

        echo '.app-shop-cart-index-product-quantity-minus,';
        echo '.app-shop-cart-index-product-quantity-plus {';
        echo 'position: absolute;';
        echo 'top: 1px;';
        echo 'bottom: 1px;';
        echo 'width: 2rem;';
        echo 'border: none;';
        echo 'outline: 0;';
        echo 'color: #aaa;';
        echo 'background-color: #f6f6f6;';
        echo 'border-radius: .25rem;';
        echo 'cursor: pointer;';
        echo '}';

        echo '.app-shop-cart-index-product-quantity-minus:hover,';
        echo '.app-shop-cart-index-product-quantity-plus:hover {';
        echo 'color: #666;';
        echo 'background-color: #eee;';
        echo '}';

        echo '.app-shop-cart-index-product-quantity-minus.disabled,';
        echo '.app-shop-cart-index-product-quantity-plus.disabled,';
        echo '.app-shop-cart-index-product-quantity-minus:disabled,';
        echo '.app-shop-cart-index-product-quantity-plus:disabled {';
        echo 'color: #ccc !important;';
        echo 'background-color: #fafafa !important;';
        echo 'pointer-events: none;';
        echo '}';

        echo '.app-shop-cart-index-product-quantity-minus {';
        echo 'left: 1px;';
        echo 'border-top-right-radius: 0;';
        echo 'border-bottom-right-radius: 0;';
        echo '}';

        echo '.app-shop-cart-index-product-quantity-plus {';
        echo 'right: 1px;';
        echo 'border-top-left-radius: 0;';
        echo 'border-bottom-left-radius: 0;';
        echo '}';

        echo '.app-shop-cart-index-product-quantity {';
        echo 'text-align: center;';
        echo 'padding-left: 2rem;';
        echo 'padding-right: 2rem;';
        echo '}';


        if ($isMobile) {

            echo '.app-shop-cart-index-product {';
            echo 'border: #eee 1px solid;';
            echo 'padding: 1rem .25rem;';
            echo '}';

            echo '.app-shop-cart-index-product-image {';
            echo 'width: 60px;';
            echo '}';

        } else {

            echo '.app-shop-cart-index-product-image {';
            echo 'width: 80px;';
            echo '}';

            echo '.app-shop-cart-index-products table {';
            echo 'width: 100%;';
            echo 'border-collapse: collapse;';
            echo 'border-spacing: 0;';
            echo '}';

            echo '.app-shop-cart-index-product {';
            echo 'border: #eee 1px solid;';
            echo '}';

            echo '.app-shop-cart-index-products th {';
            echo 'background-color: #fafafa;';
            echo 'text-align: center;';
            echo 'font-weight: 500;';
            echo 'padding: 1rem .5rem;';
            echo '}';

            echo '.app-shop-cart-index-product td {';
            echo 'padding: 1rem .5rem;';
            echo '}';

            echo '.app-shop-cart-index-products th:first-child,';
            echo '.app-shop-cart-index-product td:first-child {';
            echo 'padding-left: 1rem;';
            echo '}';

            echo '.app-shop-cart-index-products th:last-child,';
            echo '.app-shop-cart-index-product td:last-child {';
            echo 'padding-right: 1rem;';
            echo '}';

            echo '@media (max-width: 1200px) {';

            echo '.app-shop-cart-index-products th {';
            echo 'padding: .5rem .25rem;';
            echo '}';

            echo '.app-shop-cart-index-product td {';
            echo 'padding: .5rem .25rem;';
            echo '}';

            echo '.app-shop-cart-index-products th:first-child,';
            echo '.app-shop-cart-index-product td:first-child {';
            echo 'padding-left: .5rem;';
            echo '}';

            echo '.app-shop-cart-index-products th:last-child,';
            echo '.app-shop-cart-index-product td:last-child {';
            echo 'padding-right: .5rem;';
            echo '}';
            echo '}';
        }

        echo '</style>';
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

