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
    <div class="be-mt-200 be-ta-center be-c-666">Order No: <?php echo $this->order->order_sn; ?></div>
    <div class="be-mt-200 be-ta-center be-c-666">Total Amount: <?php echo $this->configStore->currencySymbol . ' ' . $this->order->amount; ?></div>
    <div class="be-mt-200 be-ta-center be-fs-150">Change your payment method</div>
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
