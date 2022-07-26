<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/payment/success.css"/>
</be-head>

<be-page-content>
    <div class="be-ta-center be-c-green payment-success-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
    </div>
    <div class="be-mt-200 be-ta-center be-fs-200">Thank you!</div>
    <div class="be-mt-200 be-ta-center be-c-999">Thank you for shopping with us! Your payment has been received!</div>
    <div class="be-mt-200 be-ta-center">
        <a href="<?php echo beUrl('Shop.Payment.success'); ?>">Continue shopping</a> |
        <a href="<?php echo beUrl('Shop.Order.orders'); ?>">See my orders</a>
    </div>
</be-page-content>
