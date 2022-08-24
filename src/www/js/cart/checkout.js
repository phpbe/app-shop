$(function () {
    updateState(cartCheckout_defaultStateId);

    $("#cart-checkout-form").validate({
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
                url: cartCheckout_saveUrl,
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
                url: cartCheckout_promotionCouponCheck,
                data: $("#cart-checkout-form").serialize(),
                type: "POST",
                success: function (json) {
                    if (json.success) {
                        updateDiscountAmount()
                    } else {
                        alert(json.message);
                    }
                },
                error: function () {
                    alert("System Error!");
                }
            });
        } else {
            updateDiscountAmount();
        }
    });
});

function updateDiscountAmount() {
    $.ajax({
        url: cartCheckout_promotionGetDiscountAmount,
        data: $("#cart-checkout-form").serialize(),
        type: "POST",
        success: function (json) {
            if (json.success) {
                cartCheckout_discountAmount = json.discountAmount;
                update();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}

function updateState(selectStateId = '') {

    let $countryId = $("#cart-checkout-country-id");
    let countryId = $countryId.val();
    if (!countryId) {
        return;
    }

    let eState = document.getElementById("cart-checkout-state-id");
    eState.options.length = 0;
    let optionItem = new Option("Select", "");
    eState.options.add(optionItem);

    cartCheckout_shippingPlans = [];
    cartCheckout_shippingPlanId = "";
    cartCheckout_shippingFee = "0.00";

    cartCheckout_paymentId = "";
    cartCheckout_paymentItemId = "";

    $.ajax({
        url: cartCheckout_shippingGetStateKeyValuesUrl,
        data: {
            country_id: countryId
        },
        type: "POST",
        success: function (json) {
            if (json.success) {
                let $stateId = $("#cart-checkout-state-id");
                if (json.stateKeyValues.length === 0) {
                    $stateId.closest(".be-col-24").hide();
                    $countryId.closest(".be-col-24").removeClass("be-col-md-12");
                } else {
                    $stateId.closest(".be-col-24").show();
                    $countryId.closest(".be-col-24").addClass("be-col-md-12");

                    for (var x in json.stateKeyValues) {
                        eState.add(new Option(json.stateKeyValues[x], x));
                    }

                    if (selectStateId) {
                        $stateId.val(selectStateId);
                    }
                }
                updateShippingPlans();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}

function updateShippingPlans() {

    cartCheckout_paymentId = "";
    cartCheckout_paymentItemId = "";

    $.ajax({
        url: cartCheckout_shippingGetShippingPlansUrl,
        data: $("#cart-checkout-form").serialize(),
        type: "POST",
        success: function (json) {
            if (json.success) {

                cartCheckout_shippingPlans = json.shippingPlans;

                let $shipping = $("#cart-checkout-shipping");
                if (json.shippingPlans.length > 0) {
                    $shipping.show();
                } else {
                    $shipping.hide();
                }

                let newShippingPlanId = "";
                let newShippingFee = "0.00";
                
                if (cartCheckout_shippingPlans.length === 1) {
                    cartCheckout_shippingPlanId = cartCheckout_shippingPlans[0].id;
                }

                let html = "";
                for (let shippingPlan of cartCheckout_shippingPlans) {
                    html += '<div class="be-row be-mt-100">';

                    html += '<div class="be-col-auto">';
                    html += '<input type="radio" class="be-radio" name="shipping_plan_id" id="shipping_plan_id-' + shippingPlan.id + '" value="' + shippingPlan.id + '" onchange="selectShippingPlan(this, \'' + shippingPlan.id + '\');"';
                    if (shippingPlan.id === cartCheckout_shippingPlanId) {
                        html += 'checked';
                        newShippingPlanId = shippingPlan.id;
                        newShippingFee = shippingPlan.shipping_fee;
                    }
                    html += '>';

                    html += '</div>';

                    html += '<div class="be-col"><div class="be-px-100">';
                    html += '<label class="be-fw-bold be-fs-125 be-lh-150" for="shipping_plan_id-' + shippingPlan.id + '">' + shippingPlan.name + '</label>';
                    if (shippingPlan.description) {
                        html += '<div class="be-c-999 be-mt-50">' + shippingPlan.description + '</div>';
                    }
                    html += '</div></div>';

                    html += '<div class="be-col-auto"><div class="be-pr-200">';
                    html += '<div class="be-c-999">$' + shippingPlan.shipping_fee + '</div>';
                    html += '</div></div>';

                    html += '</div>';
                }

                $("#cart-checkout-shipping-plans").html(html);

                cartCheckout_shippingPlanId = newShippingPlanId;
                cartCheckout_shippingFee = newShippingFee;

                updatePayments();
            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}

function selectShippingPlan(e, newShippingPlanId) {
    cartCheckout_shippingPlanId = newShippingPlanId;
    for(let shippingPlan of cartCheckout_shippingPlans) {
        if (shippingPlan.id === newShippingPlanId) {
            cartCheckout_shippingFee = shippingPlan.shipping_fee;
            break;
        }
    }

    updatePayments();
}


function updatePayments() {
    if (cartCheckout_shippingPlanId === "") {
        update();

        return;
    }

    $.ajax({
        url: cartCheckout_getStorePaymentsUrl,
        data: {
            shipping_plan_id: cartCheckout_shippingPlanId
        },
        type: "POST",
        success: function (json) {
            if (json.success) {

                let $payment = $("#cart-checkout-payment");
                if (json.storePayments.length > 0) {
                    $payment.show();
                } else {
                    $payment.hide();
                }

                let newPaymentId = "";
                let newPaymentItemId = "";
                let html = "";
                for (let storePayment of json.storePayments) {
                    html += '<div class="be-row be-mt-100 cart-checkout-payment-item">';

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

                $("#cart-checkout-payments").html(html);

                cartCheckout_paymentId = newPaymentId;
                cartCheckout_paymentItemId = newPaymentItemId;

                $("#cart-checkout-payment_item_id").val(newPaymentItemId);

                update();

            } else {
                alert(json.message);
            }
        },
        error: function () {
            alert("System Error!");
        }
    });
}


function selectPayment(e, paymentId, paymentItemId) {
    cartCheckout_paymentId = newPaymentId;
    cartCheckout_paymentItemId = newPaymentItemId;
    $("#cart-checkout-payment_item_id").val(newPaymentItemId);
    update();
}

function update() {
    $submit = $("#cart-checkout-submit");
    if (cartCheckout_shippingPlanId === "" || cartCheckout_paymentId === "") {
        $submit.addClass("disabled");
    } else {
        $submit.removeClass("disabled");
    }

    cartCheckout_totalAmount = ((Number(cartCheckout_productTotalAmount) * 100 + Number(cartCheckout_shippingFee) * 100 + Number(cartCheckout_discountAmount) * 100) / 100).toFixed(2);

    $("#cart-checkout-shipping-fee").html(cartCheckout_shippingFee);
    $("#cart-checkout-discount-amount").html(cartCheckout_discountAmount);
    $("#cart-checkout-total-amount").html(cartCheckout_totalAmount);
}

