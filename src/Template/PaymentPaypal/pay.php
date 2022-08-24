<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/payment-paypal/pay.css" />
</be-head>


<be-middle>

    <div class="be-ta-center be-c-green payment-paypal-pay-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
    </div>
    <div class="be-mt-200 be-ta-center be-fs-200">Thank you!</div>
    <div class="be-mt-200 be-ta-center be-c-999">Thank you for shopping with us! Your order has been received!</div>
    <div class="be-mt-200 be-ta-center">

        <div id="paypal-btn"></div>
        <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $this->account->client_id; ?>"></script>
        <script>
            paypal.Buttons({

                createOrder: function(data, actions) {
                    return fetch(
                        '<?php echo beUrl('ShopFai.PaymentPaypal.create', ['order_id' => $this->order->id]); ?>', {
                            method: 'post',
                            headers: {
                                'content-type': 'application/json'
                            }
                        }
                    ).then(function(response) {
                        return response.json();
                    }).then(function(json) {
                        //console.log(json);
                        if (json.success) {
                            return json.data.id;
                        } else {
                            if (json.message) {
                                alert(json.message);
                            } else {
                                alert("System Error!");
                            }
                        }
                    });
                },

                onApprove: function(data, actions) {
                    return fetch(
                        '<?php echo beUrl('ShopFai.PaymentPaypal.approve', ['order_id' => $this->order->id]); ?>',
                        {
                            method: 'post',
                            headers: {
                                'content-type': 'application/json'
                            },
                            body: JSON.stringify({
                                paypal_order_id: data.orderID,
                                paypal_payer_id: data.payerID
                            })
                        }
                    ).then(function(response) {
                        return response.json();
                    }).then(function(json) {
                        //console.log(json);
                        if (json.success) {
                            window.location.href = "<?php echo beUrl('ShopFai.Payment.success', ['order_id' => $this->order->id]); ?>";
                        } else {
                            if (json.message) {
                                alert(json.message);
                            } else {
                                alert("System Error!");
                            }
                        }
                    });
                }

            }).render('#paypal-btn');
        </script>

    </div>

</be-middle>
