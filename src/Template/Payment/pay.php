<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/payment/pay.css"/>
    <script src="<?php echo $wwwUrl; ?>/js/payment/change.js"></script>

    <?php
    $configTheme = \Be\Be::getConfig('Theme.Shop.Theme');
    ?>
    <style>
        .payment:hover {
            border: <?php echo $configTheme->linkHoverColor; ?> 3px solid;
        }
    </style>
    <script>
        const payment_confirmUrl = "<?php echo beUrl('Shop.Payment.confirm'); ?>";
        const orderId = "<?php echo $this->order->id; ?>";
    </script>
</be-head>

<be-page-content>
    <div class="be-ta-center be-c-green payment-select-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
    </div>
    <div class="be-mt-200 be-ta-center be-fs-200">Thank you!</div>
    <div class="be-mt-200 be-ta-center be-c-999">Thank you for shopping with us! Your order has been received!</div>

    <div class="be-mt-200 be-ta-center be-c-666">Order No: <?php echo $this->order->order_sn; ?></div>
    <div class="be-mt-200 be-ta-center be-c-666">Total Amount: <?php echo $this->configStore->currencySymbol . ' ' . $this->order->amount; ?></div>

    <div class="be-mt-200 be-ta-center be-fs-150">Select your payment method</div>
    <div class="be-mt-200 be-ta-center">
        <?php
        echo '<div class="be-row">';
        echo '<div class="be-col"></div>';
        foreach ($this->storePayments as $storePayment) {
            echo '<div class="be-col-auto payment">';
            echo '<a href="javascript:void(0);"  onclick="changePayment(\'' . $storePayment->id . '\', \'' . $storePayment->item->id . '\');">';
            echo '<img src="' . $storePayment->logo . '" alt="' . $storePayment->label . '">';
            echo '</a>';
            echo '</div>';
        }
        echo '<div class="be-col"></div>';
        echo '</div>';
        ?>
    </div>
</be-page-content>
