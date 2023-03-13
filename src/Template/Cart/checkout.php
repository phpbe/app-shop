<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/js/cart/checkout.js"></script>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/cart/checkout.css"/>
</be-head>


<be-page-content>
    <?php
    $configStore = \Be\Be::getConfig('App.Shop.Store');
    $defaultAddress = false;
    $billingAddress = false;
    $my = \Be\Be::getUser();
    if (!$my->isGuest()) {
        $defaultAddress = \Be\Be::getService('App.Shop.UserShippingAddress')->getDefaultAddress($my->id);
        $billingAddress = \Be\Be::getService('App.Shop.UserBillingAddress')->getAddress($my->id);
    }

    $countryKeyValues = \Be\Be::getService('App.Shop.Shipping')->getCountryIdNameKeyValues();
    ?>
    <script>
        const cartCheckout_products = <?php echo json_encode($this->products); ?>;
        const cartCheckout_productTotalAmount = "<?php echo $this->productTotalAmount; ?>";

        const cartCheckout_defaultStateId = "<?php echo $defaultAddress && isset($defaultAddress->state_id) ? $defaultAddress->state_id : ''; ?>";

        const cartCheckout_saveUrl = "<?php echo beUrl('Shop.Cart.checkoutSave'); ?>";
        const cartCheckout_shippingGetStateKeyValuesUrl = "<?php echo beUrl('Shop.Shipping.getStateKeyValues'); ?>";
        const cartCheckout_shippingGetShippingPlansUrl = "<?php echo beUrl('Shop.Shipping.getShippingPlans'); ?>";
        const cartCheckout_getStorePaymentsUrl = "<?php echo beUrl('Shop.Payment.getStorePaymentsByShippingPlanId'); ?>";
        const cartCheckout_promotionCouponCheck = "<?php echo beUrl('Shop.PromotionCoupon.check'); ?>";
        const cartCheckout_promotionGetDiscountAmount = "<?php echo beUrl('Shop.Promotion.getDiscountAmount'); ?>";

        var cartCheckout_shippingPlans = [];
        var cartCheckout_shippingPlanId = "";
        var cartCheckout_shippingFee = "0.00";
        var cartCheckout_discountAmount = "<?php echo $this->discountAmount; ?>";
        var cartCheckout_totalAmount = "<?php echo $this->totalAmount; ?>";
        var cartCheckout_paymentId = "";
        var cartCheckout_paymentItemId = "";
    </script>

    <form id="cart-checkout-form">
        <div class="be-row">
            <div class="be-col-24 be-md-col-14">

                <div style="border-right: #eee 1px solid;">
                    <input type="hidden" name="from" value="<?php echo $this->from; ?>" />
                    <?php
                    if ($my->isGuest()) {
                        ?>
                        <div class="be-row be-pr-200">
                            <div class="be-col-24 be-lg-col-12">
                                <div class="be-fs-125 be-lh-150">Customer Information</div>
                            </div>
                            <div class="be-col-24 be-lg-col-12">
                                <div class="be-mt-50 be-ta-right">Already have an account? <a href="<?php echo beUrl('Shop.User.login'); ?>">Login</a></div>
                            </div>
                        </div>

                        <input type="hidden" name="user_id" value="0">

                        <div class="be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="email" id="email" class="be-input" placeholder="Email">
                                <label class="be-floating-label" for="email">Email <span class="be-c-red">*</span></label>
                            </div>
                        </div>

                        <div class="be-mt-100 be-pr-200">
                            <input type="checkbox" name="subscribe" id="subscribe" class="be-checkbox">
                            <label for="subscribe">Email me with news and offers</label>
                        </div>

                        <?php
                    } else {
                        ?>
                        <input type="hidden" name="user_id" value="<?php echo $my->id; ?>">
                        <input type="hidden" name="email" value="<?php echo $my->email; ?>">
                        <?php
                    }
                    ?>

                    <div class="be-fs-125 be-lh-150 <?php echo $my->isGuest() ? 'be-mt-200' : '';?>">Shipping Address</div>

                    <div class="be-row">
                        <div class="be-col-24 be-md-col-12 be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="first_name" id="first_name" class="be-input" placeholder="First Name" value="<?php echo $defaultAddress && isset($defaultAddress->first_name) ? $defaultAddress->first_name : ''; ?>">
                                <label class="be-floating-label" for="first_name">First Name <span class="be-c-red">*</span></label>
                            </div>
                        </div>
                        <div class="be-col-24 be-md-col-12 be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="last_name" id="last_name" class="be-input"  placeholder="Last Name" value="<?php echo $defaultAddress && isset($defaultAddress->last_name) ? $defaultAddress->last_name : ''; ?>">
                                <label class="be-floating-label" for="last_name">Last Name <span class="be-c-red">*</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="be-mt-100 be-pr-200">
                        <div class="be-floating">
                            <input type="text" name="address" id="cart-checkout-address" class="be-input" placeholder="Address" value="<?php echo $defaultAddress && isset($defaultAddress->address) ? $defaultAddress->address : ''; ?>">
                            <label class="be-floating-label" for="cart-checkout-address">Address <span class="be-c-red">*</span></label>
                        </div>
                    </div>

                    <div class="be-mt-100 be-pr-200">
                        <div class="be-floating">
                            <input type="text" name="address2" id="cart-checkout-address2" class="be-input" placeholder="Apartment, suite, etc. (optional)" value="<?php echo $defaultAddress && isset($defaultAddress->address2) ? $defaultAddress->address2 : ''; ?>">
                            <label class="be-floating-label" for="cart-checkout-address2">Apartment, suite, etc. (optional)</label>
                        </div>
                    </div>

                    <div class="be-mt-100 be-pr-200">
                        <div class="be-floating">
                            <input type="text" name="city" id="cart-checkout-city" class="be-input" placeholder="City" value="<?php echo $defaultAddress && isset($defaultAddress->city) ? $defaultAddress->city : ''; ?>">
                            <label class="be-floating-label" for="cart-checkout-city">City <span class="be-c-red">*</span></label>
                        </div>
                    </div>

                    <div class="be-row">
                        <div class="be-col-24 be-md-col-12 be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <select name="country_id" id="cart-checkout-country-id" class="be-select" onchange="updateState();">
                                    <?php
                                    foreach ($countryKeyValues as $key => $val) {
                                        echo '<option value="' . $key . '"';
                                        if ($defaultAddress && isset($defaultAddress->country_id)) {
                                            if ($defaultAddress->country_id == $key) {
                                                echo ' selected';
                                            }
                                        }
                                        echo '>' . $val . '</option>';
                                    }
                                    ?>
                                </select>
                                <label class="be-floating-label" for="cart-checkout-country-id">Country/Region <span class="be-c-red">*</span></label>
                            </div>
                        </div>

                        <div class="be-col-24 be-md-col-12 be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <select name="state_id" id="cart-checkout-state-id" class="be-select" onchange="updateShippingPlans();">
                                    <option value="">Select</option>
                                </select>
                                <label class="be-floating-label" for="cart-checkout-state-id">State <span class="be-c-red">*</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="be-mt-100 be-pr-200">
                        <div class="be-floating">
                            <input type="text" name="zip_code" id="cart-checkout-zip-code" class="be-input" placeholder="Zip code" value="<?php echo $defaultAddress && isset($defaultAddress->zip_code) ? $defaultAddress->zip_code : ''; ?>">
                            <label class="be-floating-label" for="cart-checkout-zip-code">Zip code <span class="be-c-red">*</span></label>
                        </div>
                    </div>

                    <div class="be-mt-100 be-pr-200">
                        <div class="be-floating">
                            <input type="text" name="mobile" id="cart-checkout-mobile" class="be-input" placeholder="Mobile phone number" value="<?php echo $defaultAddress && isset($defaultAddress->mobile) ? $defaultAddress->mobile : ''; ?>">
                            <label class="be-floating-label" for="cart-checkout-mobile">Mobile phone number <span class="be-c-red">*</span></label>
                        </div>
                    </div>

                    <div class="be-mt-200 be-pt-200" id="cart-checkout-shipping">
                        <div class="be-fs-125 be-lh-150">Shipping Method</div>
                        <div id="cart-checkout-shipping-plans"></div>
                    </div>

                    <div class="be-mt-200 be-pt-200" id="cart-checkout-payment">
                        <div class="be-fs-125 be-lh-150">Payment Method</div>
                        <div id="cart-checkout-payments"></div>
                        <input type="hidden" name="payment_item_id" id="cart-checkout-payment_item_id" value="">
                    </div>

                    <div class="be-mt-150 be-pr-200">
                        <input type="submit" class="be-btn be-btn-major be-btn-lg be-lh-200 be-mt-50" id="cart-checkout-submit" value="Place Your Order">
                        <a href="<?php echo beUrl('Shop.Cart.index'); ?>" class="be-d-inline-block be-lh-300 be-va-middle be-ml-100 be-mt-50">Return to cart</a>
                    </div>

                </div>


            </div>

            <div class="be-col-0 be-md-col-10 be-pl-200">

                <div class="cart-checkout-products">
                    <table>
                        <tbody>
                        <?php
                        foreach ($this->products as $product) {
                            ?>
                            <tr>
                                <td>
                                    <input type="hidden" name="product_id[]" value="<?php echo $product->product_id; ?>" />
                                    <input type="hidden" name="product_item_id[]" value="<?php echo $product->product_item_id; ?>" />
                                    <input type="hidden" name="quantity[]" value="<?php echo $product->quantity; ?>" />
                                    <a class="cart-checkout-product-image" href="<?php echo $product->url; ?>">
                                        <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                        <span><?php echo $product->quantity; ?></span>
                                    </a>
                                </td>

                                <td>
                                    <a class="be-d-block be-t-ellipsis-2" href="<?php echo $product->url; ?>">
                                        <?php echo $product->name; ?>
                                    </a>
                                    <div class="be-mt-50 be-c-999">
                                        <?php echo $product->style; ?>
                                    </div>
                                </td>

                                <td class="be-ta-right"><?php echo $configStore->currencySymbol . $product->amount; ?></td>
                            </tr>
                            <tr><td class="be-pt-100" colspan="3"></td></tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="be-mt-150 be-pt-200" style="border-top: #eee 1px solid;">
                    <div class="be-d-flex">
                        <div class="be-flex-1 be-pr-100">
                            <div class="be-floating">
                                <input type="text" name="promotion_coupon_code" id="promotion_coupon_code" class="be-input" placeholder="Discount code" >
                                <label class="be-floating-label" for="promotion_coupon_code">Discount code</label>
                            </div>
                        </div>
                        <div class="be-flex-0">
                            <input type="button" class="be-btn be-btn-lg be-lh-200" id="cart-promotion_coupon_code-apply" value="Apply">
                        </div>
                    </div>
                </div>


                <div class="be-mt-200 be-pt-200" style="border-top: #eee 1px solid;">
                    <div class="be-row">
                        <div class="be-col-auto">
                            Subtotal
                        </div>
                        <div class="be-col be-ta-right">
                            <?php echo $configStore->currencySymbol; ?><?php echo $this->totalAmount; ?>
                        </div>
                    </div>
                    <div class="be-row be-mt-100">
                        <div class="be-col-auto">
                            Discount
                        </div>
                        <div class="be-col be-ta-right">
                            <?php echo $configStore->currencySymbol; ?><span id="cart-checkout-discount-amount"><?php echo $this->discountAmount; ?></span>
                        </div>
                    </div>
                    <div class="be-row be-mt-100">
                        <div class="be-col-auto">
                            Shipping
                        </div>
                        <div class="be-col be-ta-right">
                            <?php echo $configStore->currencySymbol; ?><span id="cart-checkout-shipping-fee"><?php echo '0.00'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="be-mt-200 be-pt-200" style="border-top: #eee 1px solid;">
                    <div class="be-row">
                        <div class="be-col-auto be-fs-125">
                            Total
                        </div>
                        <div class="be-col be-ta-right be-fs-150">
                            <?php echo $configStore->currencySymbol; ?><span id="cart-checkout-total-amount"><?php echo $this->totalAmount; ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

</be-page-content>