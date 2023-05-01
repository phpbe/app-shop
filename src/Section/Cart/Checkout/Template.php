<?php

namespace Be\App\Shop\Section\Cart\Checkout;

use Be\Be;
use Be\Theme\Section;

class Template extends Section
{

    public array $positions = ['middle', 'center'];

    public array $routes = ['Shop.Cart.checkout'];

    public function display()
    {
        if ($this->config->enable === 0) {
            return;
        }

        $configStore = \Be\Be::getConfig('App.Shop.Store');
        $my = \Be\Be::getUser();
        if (!$my->isGuest()) {
            $defaultAddress = \Be\Be::getService('App.Shop.UserShippingAddress')->getDefaultAddress($my->id);
            $billingAddress = \Be\Be::getService('App.Shop.UserBillingAddress')->getAddress($my->id);
        }

        $countryKeyValues = \Be\Be::getService('App.Shop.Shipping')->getCountryIdNameKeyValues();

        $this->css();

        echo '<div class="app-shop-cart-checkout">';
        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '<div class="be-container">';
        }
        ?>

        <form id="app-shop-cart-checkout-form">
            <div class="be-row">
                <div class="be-col-24 be-md-col-14">

                    <div style="border-right: #eee 1px solid;">
                        <input type="hidden" name="from" value="<?php echo $this->page->from; ?>" />
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
                                <input type="text" name="address" id="app-shop-cart-checkout-address" class="be-input" placeholder="Address" value="<?php echo $defaultAddress && isset($defaultAddress->address) ? $defaultAddress->address : ''; ?>">
                                <label class="be-floating-label" for="app-shop-cart-checkout-address">Address <span class="be-c-red">*</span></label>
                            </div>
                        </div>

                        <div class="be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="address2" id="app-shop-cart-checkout-address2" class="be-input" placeholder="Apartment, suite, etc. (optional)" value="<?php echo $defaultAddress && isset($defaultAddress->address2) ? $defaultAddress->address2 : ''; ?>">
                                <label class="be-floating-label" for="app-shop-cart-checkout-address2">Apartment, suite, etc. (optional)</label>
                            </div>
                        </div>

                        <div class="be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="city" id="app-shop-cart-checkout-city" class="be-input" placeholder="City" value="<?php echo $defaultAddress && isset($defaultAddress->city) ? $defaultAddress->city : ''; ?>">
                                <label class="be-floating-label" for="app-shop-cart-checkout-city">City <span class="be-c-red">*</span></label>
                            </div>
                        </div>

                        <div class="be-row">
                            <div class="be-col-24 be-md-col-12 be-mt-100 be-pr-200">
                                <div class="be-floating">
                                    <select name="country_id" id="app-shop-cart-checkout-country-id" class="be-select" onchange="updateState();">
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
                                    <label class="be-floating-label" for="app-shop-cart-checkout-country-id">Country/Region <span class="be-c-red">*</span></label>
                                </div>
                            </div>

                            <div class="be-col-24 be-md-col-12 be-mt-100 be-pr-200">
                                <div class="be-floating">
                                    <select name="state_id" id="app-shop-cart-checkout-state-id" class="be-select" onchange="AppShopCartCheckout.updateShippingPlans();">
                                        <option value="">Select</option>
                                    </select>
                                    <label class="be-floating-label" for="app-shop-cart-checkout-state-id">State <span class="be-c-red">*</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="zip_code" id="app-shop-cart-checkout-zip-code" class="be-input" placeholder="Zip code" value="<?php echo $defaultAddress && isset($defaultAddress->zip_code) ? $defaultAddress->zip_code : ''; ?>">
                                <label class="be-floating-label" for="app-shop-cart-checkout-zip-code">Zip code <span class="be-c-red">*</span></label>
                            </div>
                        </div>

                        <div class="be-mt-100 be-pr-200">
                            <div class="be-floating">
                                <input type="text" name="mobile" id="app-shop-cart-checkout-mobile" class="be-input" placeholder="Mobile phone number" value="<?php echo $defaultAddress && isset($defaultAddress->mobile) ? $defaultAddress->mobile : ''; ?>">
                                <label class="be-floating-label" for="app-shop-cart-checkout-mobile">Mobile phone number <span class="be-c-red">*</span></label>
                            </div>
                        </div>

                        <div class="be-mt-200 be-pt-200" id="app-shop-cart-checkout-shipping">
                            <div class="be-fs-125 be-lh-150">Shipping Method</div>
                            <div id="app-shop-cart-checkout-shipping-plans"></div>
                        </div>

                        <div class="be-mt-200 be-pt-200" id="app-shop-cart-checkout-payment">
                            <div class="be-fs-125 be-lh-150">Payment Method</div>
                            <div id="app-shop-cart-checkout-payments"></div>
                            <input type="hidden" name="payment_item_id" id="app-shop-cart-checkout-payment_item_id" value="">
                        </div>

                        <div class="be-mt-150 be-pr-200">
                            <input type="submit" class="be-btn be-btn-major be-btn-lg be-lh-200 be-mt-50" id="app-shop-cart-checkout-submit" value="Place Your Order">
                            <a href="<?php echo beUrl('Shop.Cart.index'); ?>" class="be-d-inline-block be-lh-300 be-va-middle be-ml-100 be-mt-50">Return to cart</a>
                        </div>

                    </div>


                </div>

                <div class="be-col-0 be-md-col-10 be-pl-200">

                    <div class="app-shop-cart-checkout-products">
                        <table>
                            <tbody>
                            <?php
                            foreach ($this->page->products as $product) {
                                ?>
                                <tr>
                                    <td>
                                        <input type="hidden" name="product_id[]" value="<?php echo $product->product_id; ?>" />
                                        <input type="hidden" name="product_item_id[]" value="<?php echo $product->product_item_id; ?>" />
                                        <input type="hidden" name="quantity[]" value="<?php echo $product->quantity; ?>" />
                                        <div class="app-shop-cart-checkout-product-image">
                                            <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                            <span><?php echo $product->quantity; ?></span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="be-px-50">
                                            <a class="be-d-block be-t-ellipsis-2" href="<?php echo $product->url; ?>">
                                                <?php echo $product->name; ?>
                                            </a>
                                            <div class="be-mt-50 be-c-font-4">
                                                <?php echo $product->style; ?>
                                            </div>
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
                                <?php echo $configStore->currencySymbol; ?><?php echo $this->page->totalAmount; ?>
                            </div>
                        </div>
                        <div class="be-row be-mt-100">
                            <div class="be-col-auto">
                                Discount
                            </div>
                            <div class="be-col be-ta-right">
                                <?php echo $configStore->currencySymbol; ?><span id="app-shop-cart-checkout-discount-amount"><?php echo $this->page->discountAmount; ?></span>
                            </div>
                        </div>
                        <div class="be-row be-mt-100">
                            <div class="be-col-auto">
                                Shipping
                            </div>
                            <div class="be-col be-ta-right">
                                <?php echo $configStore->currencySymbol; ?><span id="app-shop-cart-checkout-shipping-fee"><?php echo '0.00'; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="be-mt-200 be-pt-200" style="border-top: #eee 1px solid;">
                        <div class="be-row">
                            <div class="be-col-auto be-fs-125">
                                Total
                            </div>
                            <div class="be-col be-ta-right be-fs-150">
                                <?php echo $configStore->currencySymbol; ?><span id="app-shop-cart-checkout-total-amount"><?php echo $this->page->totalAmount; ?></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
        <?php

        if ($this->position === 'middle' && $this->config->width === 'default') {
            echo '</div>';
        }
        echo '</div>';

        $this->js();
    }


    private function css()
    {
        ?>
        <style>
            <?php
            echo $this->getCssBackgroundColor('app-shop-cart-checkout');
            echo $this->getCssPadding('app-shop-cart-checkout');
            echo $this->getCssMargin('app-shop-cart-checkout');
            ?>
            #app-shop-cart-checkout-shipping {
                border-top: #eee 1px solid;
                display: none;
            }

            #app-shop-cart-checkout-payment {
                border-top: #eee 1px solid;
                display: none;
            }

            .app-shop-cart-checkout-payment-item {
                cursor: pointer;
            }

            .app-shop-cart-checkout-payment-item img {
                max-width: 120px;
                max-height: 3rem;
                vertical-align: middle;
            }

            .app-shop-cart-checkout-products {
                max-height: 20rem;
                overflow-y: auto;
                padding: 1rem 1rem 1rem 0;
            }

            .app-shop-cart-checkout-products table {
                margin-top: 1rem;
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
            }

            .app-shop-cart-checkout-product-image {
                position: relative;
                width: 4rem;
                <?php
                $configProduct = Be::getConfig('App.Shop.Product');
                echo 'aspect-ratio: ' . $configProduct->imageAspectRatio .';';
                ?>
            }

            .app-shop-cart-checkout-product-image:after {
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

            .app-shop-cart-checkout-product-image img {
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

            .app-shop-cart-checkout-product-image span {
                display: inline-block;
                position: absolute;
                right: -0.75rem;
                top: -0.75rem;
                font-size: 0.85rem;
                min-width: 1.75rem;
                height: 1.75rem;
                line-height: 1.75rem;
                padding: 0 0.5rem;
                background-color: #666;
                color: #fff;
                border-radius: 1rem;
                text-align: center;
                white-space: nowrap;
                z-index: 3;
            }

            .app-shop-cart-checkout-discount-code {
                border-top: #eee 1px solid;
                border-bottom: #eee 1px solid;
            }
        </style>
        <?php
    }

    private function js()
    {
        $configStore = \Be\Be::getConfig('App.Shop.Store');
        $defaultAddress = false;
        // $billingAddress = false;
        $my = \Be\Be::getUser();
        if (!$my->isGuest()) {
            $defaultAddress = \Be\Be::getService('App.Shop.UserShippingAddress')->getDefaultAddress($my->id);
            // $billingAddress = \Be\Be::getService('App.Shop.UserBillingAddress')->getAddress($my->id);
        }
        ?>
        <script>
            var AppShopCartCheckout = {

                products: <?php echo json_encode($this->page->products); ?>,
                productTotalAmount: "<?php echo $this->page->productTotalAmount; ?>",

                shippingPlans: [],
                shippingPlanId: "",
                shippingFee: "0.00",
                discountAmount: "<?php echo $this->page->discountAmount; ?>",
                totalAmount: "<?php echo $this->page->totalAmount; ?>",
                paymentId: "",
                paymentItemId: "",

                updateDiscountAmount: function() {
                    $.ajax({
                        url: "<?php echo beUrl('Shop.Promotion.getDiscountAmount'); ?>",
                        data: $("#app-shop-cart-checkout-form").serialize(),
                        type: "POST",
                        success: function (json) {
                            if (json.success) {
                                AppShopCartCheckout.discountAmount = json.discountAmount;
                                AppShopCartCheckout.update();
                            } else {
                                alert(json.message);
                            }
                        },
                        error: function () {
                            alert("System Error!");
                        }
                    });
                },

                updateState: function (selectStateId = '') {
                        let $countryId = $("#app-shop-cart-checkout-country-id");
                    let countryId = $countryId.val();
                    if (!countryId) {
                        return;
                    }

                    let eState = document.getElementById("app-shop-cart-checkout-state-id");
                    eState.options.length = 0;
                    let optionItem = new Option("Select", "");
                    eState.options.add(optionItem);

                    AppShopCartCheckout.shippingPlans = [];
                    AppShopCartCheckout.shippingPlanId = "";
                    AppShopCartCheckout.shippingFee = "0.00";

                    AppShopCartCheckout.paymentId = "";
                    AppShopCartCheckout.paymentItemId = "";

                    $.ajax({
                        url: "<?php echo beUrl('Shop.Shipping.getStateKeyValues'); ?>",
                        data: {
                            country_id: countryId
                        },
                        type: "POST",
                        success: function (json) {
                            if (json.success) {
                                let $stateId = $("#app-shop-cart-checkout-state-id");
                                if (json.stateKeyValues.length === 0) {
                                    $stateId.closest(".be-col-24").hide();
                                    $countryId.closest(".be-col-24").removeClass("be-md-col-12");
                                } else {
                                    $stateId.closest(".be-col-24").show();
                                    $countryId.closest(".be-col-24").addClass("be-md-col-12");

                                    for (var x in json.stateKeyValues) {
                                        eState.add(new Option(json.stateKeyValues[x], x));
                                    }

                                    if (selectStateId) {
                                        $stateId.val(selectStateId);
                                    }
                                }
                                AppShopCartCheckout.updateShippingPlans();
                            } else {
                                alert(json.message);
                            }
                        },
                        error: function () {
                            alert("System Error!");
                        }
                    });
                },

                updateShippingPlans: function() {

                    AppShopCartCheckout.paymentId = "";
                    AppShopCartCheckout.paymentItemId = "";

                    $.ajax({
                        url: "<?php echo beUrl('Shop.Shipping.getShippingPlans'); ?>",
                        data: $("#app-shop-cart-checkout-form").serialize(),
                        type: "POST",
                        success: function (json) {
                            if (json.success) {

                                AppShopCartCheckout.shippingPlans = json.shippingPlans;

                                let $shipping = $("#app-shop-cart-checkout-shipping");
                                if (json.shippingPlans.length > 0) {
                                    $shipping.show();
                                } else {
                                    $shipping.hide();
                                }

                                let newShippingPlanId = "";
                                let newShippingFee = "0.00";

                                if (AppShopCartCheckout.shippingPlans.length === 1) {
                                    AppShopCartCheckout.shippingPlanId = AppShopCartCheckout.shippingPlans[0].id;
                                }

                                let html = "";
                                for (let shippingPlan of AppShopCartCheckout.shippingPlans) {
                                    html += '<div class="be-row be-mt-100">';

                                    html += '<div class="be-col-auto">';
                                    html += '<input type="radio" class="be-radio" name="shipping_plan_id" id="shipping_plan_id-' + shippingPlan.id + '" value="' + shippingPlan.id + '" onchange="selectShippingPlan(this, \'' + shippingPlan.id + '\');"';
                                    if (shippingPlan.id === AppShopCartCheckout.shippingPlanId) {
                                        html += 'checked';
                                        newShippingPlanId = shippingPlan.id;
                                        newShippingFee = shippingPlan.shipping_fee;
                                    }
                                    html += '>';

                                    html += '</div>';

                                    html += '<div class="be-col"><div class="be-px-100">';
                                    html += '<label class="be-fw-bold be-fs-125 be-lh-150" for="shipping_plan_id-' + shippingPlan.id + '">' + shippingPlan.name + '</label>';
                                    if (shippingPlan.description) {
                                        html += '<div class="be-c-font-4 be-mt-50">' + shippingPlan.description + '</div>';
                                    }
                                    html += '</div></div>';

                                    html += '<div class="be-col-auto"><div class="be-pr-200">';
                                    html += '<div class="be-c-font-4"><?php echo $configStore->currencySymbol; ?>' + shippingPlan.shipping_fee + '</div>';
                                    html += '</div></div>';

                                    html += '</div>';
                                }

                                $("#app-shop-cart-checkout-shipping-plans").html(html);

                                AppShopCartCheckout.shippingPlanId = newShippingPlanId;
                                AppShopCartCheckout.shippingFee = newShippingFee;

                                AppShopCartCheckout.updatePayments();
                            } else {
                                alert(json.message);
                            }
                        },
                        error: function () {
                            alert("System Error!");
                        }
                    });
                },

                selectShippingPlan: function (e, newShippingPlanId) {
                    AppShopCartCheckout.shippingPlanId = newShippingPlanId;
                    for(let shippingPlan of shippingPlans) {
                        if (shippingPlan.id === newShippingPlanId) {
                            AppShopCartCheckout.shippingFee = shippingPlan.shipping_fee;
                            break;
                        }
                    }

                    AppShopCartCheckout.updatePayments();
                },


                updatePayments: function() {
                    if (AppShopCartCheckout.shippingPlanId === "") {
                        AppShopCartCheckout.update();

                        return;
                    }

                    $.ajax({
                        url: "<?php echo beUrl('Shop.Payment.getStorePaymentsByShippingPlanId'); ?>",
                        data: {
                            shipping_plan_id: AppShopCartCheckout.shippingPlanId
                        },
                        type: "POST",
                        success: function (json) {
                            if (json.success) {

                                let $payment = $("#app-shop-cart-checkout-payment");
                                if (json.storePayments.length > 0) {
                                    $payment.show();
                                } else {
                                    $payment.hide();
                                }

                                let newPaymentId = "";
                                let newPaymentItemId = "";
                                let html = "";
                                for (let storePayment of json.storePayments) {
                                    html += '<div class="be-row be-mt-100 app-shop-cart-checkout-payment-item">';

                                    html += '<div class="be-col-auto">';
                                    html += '<input type="radio" class="be-radio be-mt-150" name="payment_id" id="payment_id-' + storePayment.id + '" value="' + storePayment.id + '" onchange="selectPayment(this, \'' + storePayment.id + '\', \'' + storePayment.item.id + '\');"';
                                    if (newPaymentId === "") {
                                        html += 'checked';
                                        newPaymentId = storePayment.id;
                                        newPaymentItemId = storePayment.item.id;
                                    }
                                    html += '>';
                                    html += '</div>';

                                    html += '<div class="be-col"><div class="be-p-100">';
                                    html += '<label class="be-fw-bold be-fs-125 be-lh-150" for="payment_id-' + storePayment.id + '">';
                                    html += '<img src="' + storePayment.logo + '" alt="' + storePayment.label + '">';
                                    html += '</label>';
                                    html += '</div></div>';
                                    html += '</div>';
                                }

                                $("#app-shop-cart-checkout-payments").html(html);

                                AppShopCartCheckout.paymentId = newPaymentId;
                                AppShopCartCheckout.paymentItemId = newPaymentItemId;

                                $("#app-shop-cart-checkout-payment_item_id").val(newPaymentItemId);

                                AppShopCartCheckout.update();

                            } else {
                                alert(json.message);
                            }
                        },
                        error: function () {
                            alert("System Error!");
                        }
                    });
                },


                selectPayment: function(e, paymentId, paymentItemId) {
                    AppShopCartCheckout.paymentId = newPaymentId;
                    AppShopCartCheckout.paymentItemId = newPaymentItemId;
                    $("#app-shop-cart-checkout-payment_item_id").val(newPaymentItemId);
                    AppShopCartCheckout.update();
                },

                update: function() {
                    $submit = $("#app-shop-cart-checkout-submit");
                    if (AppShopCartCheckout.shippingPlanId === "" || AppShopCartCheckout.paymentId === "") {
                        $submit.addClass("disabled");
                    } else {
                        $submit.removeClass("disabled");
                    }

                    AppShopCartCheckout.totalAmount = ((Number(AppShopCartCheckout.productTotalAmount) * 100 + Number(AppShopCartCheckout.shippingFee) * 100 + Number(AppShopCartCheckout.discountAmount) * 100) / 100).toFixed(2);

                    $("#app-shop-cart-checkout-shipping-fee").html(AppShopCartCheckout.shippingFee);
                    $("#app-shop-cart-checkout-discount-amount").html(AppShopCartCheckout.discountAmount);
                    $("#app-shop-cart-checkout-total-amount").html(AppShopCartCheckout.totalAmount);
                }
            }


            $(function () {
                AppShopCartCheckout.updateState("<?php echo $defaultAddress && isset($defaultAddress->state_id) ? $defaultAddress->state_id : ''; ?>");

                $("#app-shop-cart-checkout-form").validate({
                    //debug:true,
                    rules: {
                        email: {
                            required: true,
                            email: true
                        },
                        first_name: {
                            required: true
                        },
                        last_name: {
                            required: true
                        },
                        address: {
                            required: true
                        },
                        city: {
                            required: true
                        },
                        zip_code: {
                            required: true
                        },
                        mobile: {
                            required: true
                        }
                    },
                    messages: {
                        email: {
                            required: "Please enter your email.",
                            email: "The email address you entered is incorrect."
                        },
                        first_name: {
                            required: "Please enter your first name."
                        },
                        last_name: {
                            required: "Please enter your last name."
                        },
                        address: {
                            required: "Please enter your address."
                        },
                        city: {
                            required: "Please enter your city."
                        },
                        zip_code: {
                            required: "Please enter your zip code."
                        },
                        mobile: {
                            required: "Please enter your mobile phone number."
                        }
                    },

                    submitHandler: function (form) {
                        $.ajax({
                            url: "<?php echo beUrl('Shop.Cart.checkoutSave'); ?>",
                            data: $(form).serialize(),
                            type: "POST",
                            success: function (json) {
                                if (json.success) {
                                    window.location.href = json.redirectUrl;
                                } else {
                                    alert(json.message);
                                }
                            },
                            error: function () {
                                alert("System Error!");
                            }
                        });
                    }
                });

                $("#cart-promotion_coupon_code-apply").click(function () {
                    let coupon = $.trim($("#promotion_coupon_code").val());
                    if (coupon) {
                        $.ajax({
                            url: "<?php echo beUrl('Shop.PromotionCoupon.check'); ?>",
                            data: $("#app-shop-cart-checkout-form").serialize(),
                            type: "POST",
                            success: function (json) {
                                if (json.success) {
                                    AppShopCartCheckout.updateDiscountAmount()
                                } else {
                                    alert(json.message);
                                }
                            },
                            error: function () {
                                alert("System Error!");
                            }
                        });
                    } else {
                        AppShopCartCheckout.updateDiscountAmount();
                    }
                });
            });

        </script>
        <?php
    }
}

