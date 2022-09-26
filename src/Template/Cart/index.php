<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/js/cart/index.js"></script>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/cart/index.css"/>

    <script>
        const cartIndex_products = <?php echo json_encode($this->products); ?>;
        const cartIndex_productTotalQuantity = <?php echo $this->productTotalQuantity; ?>;
        const cartIndex_productTotalAmount = "<?php echo $this->productTotalAmount; ?>";
        const cartIndex_discountAmount = "<?php echo $this->discountAmount; ?>";
        const cartIndex_totalAmount = "<?php echo $this->totalAmount; ?>";
    </script>
</be-head>

<be-page-content>
    <?php
    $configStore = \Be\Be::getConfig('App.ShopFai.Store');
    if (count($this->products) == 0) {
        ?>
        <div class="be-py-400 be-my-400 be-ta-center be-c-999">Your shopping cart is empty.</div>
        <?php
    } else {
        ?>
        <form action="<?php echo beUrl('ShopFai.Cart.checkout'); ?>" method="post">
        <div class="be-row">
            <div class="be-col-24 be-lg-col-16">
                <div class="cart-index-products">
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
                            foreach ($this->products as $product) {
                                ?>
                                <tr><td class="be-pt-100" colspan="5"></td></tr>
                                <tr class="cart-index-product" id="cart-index-product-item-<?php echo $product->product_item_id; ?>" data-productid="<?php echo $product->product_id; ?>" data-productitemid="<?php echo $product->product_item_id; ?>">
                                    <td>
                                        <input type="hidden" name="product_id[]" value="<?php echo $product->product_id; ?>">
                                        <input type="hidden" name="product_item_id[]" value="<?php echo $product->product_item_id; ?>">
                                        <a class="cart-index-product-image" href="<?php echo $product->url; ?>">
                                            <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                        </a>
                                    </td>
                                    <td>
                                        <a class="be-t-ellipsis-2" href="<?php echo $product->url; ?>">
                                            <?php echo $product->name; ?>
                                        </a>
                                        <div class="be-mt-50 be-c-999">
                                            <?php echo $product->style; ?>
                                        </div>
                                    </td>

                                    <td class="be-ta-center">
                                        <?php echo $configStore->currencySymbol . $product->price; ?>
                                    </td>

                                    <td>
                                        <div class="cart-index-product-quantity-input">
                                            <button class="cart-index-product-quantity-minus" type="button" onclick="changeQuantity(this, -1);">-</button>
                                            <input class="be-input cart-index-product-quantity" name="quantity[]" type="text" value="<?php echo $product->quantity; ?>" maxlength="3" onkeyup="changeQuantity(this, 0);">
                                            <button class="cart-index-product-quantity-plus" type="button" onclick="changeQuantity(this, 1);">+</button>
                                        </div>
                                    </td>

                                    <td class="be-ta-center">
                                        <?php echo $configStore->currencySymbol; ?><span class="cart-index-product-amount"><?php echo $product->amount; ?></span>
                                    </td>

                                    <td>
                                        <a class="cart-index-product-remove" href="javascript:void(0);" onclick="remove(this);"></a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="be-col-24 be-lg-col-7">

                <div class="be-row">
                    <div class="be-col-0 be-lg-col-2">
                    </div>
                    <div class="be-col-24 be-lg-col-22">

                        <div class="be-fs-125 be-pb-100 be-mb-200" style="border-bottom: #ddd 1px solid;">Order Summary</div>

                        <div class="be-row">
                            <div class="be-col-auto">
                                Subtotal
                            </div>
                            <div class="be-col be-ta-right">
                                <?php echo $configStore->currencySymbol; ?><span id="cart-index-product-total-amount"><?php echo $this->productTotalAmount; ?></span>
                            </div>
                        </div>
                        <div class="be-row be-mt-100">
                            <div class="be-col-auto">
                                Discount
                            </div>
                            <div class="be-col be-ta-right">
                                <?php echo $configStore->currencySymbol; ?><span id="cart-index-discount-amount"><?php echo $this->discountAmount; ?></span>
                            </div>
                        </div>

                        <div class="be-mt-200 be-pt-200" style="border-top: #eee 1px solid;">
                            <div class="be-row">
                                <div class="be-col-auto be-fs-125">
                                    Total
                                </div>
                                <div class="be-col be-ta-right be-fs-150">
                                    <?php echo $configStore->currencySymbol; ?><span id="cart-index-total-amount"><?php echo $this->totalAmount; ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="be-mt-200 be-ta-right">
                            <input type="submit" class="be-btn be-btn-main be-btn-lg" value="Check out now" />
                        </div>
                    </div>
                </div>


            </div>
        </div>
        </form>
        <?php
    }
    ?>

</be-page-content>
