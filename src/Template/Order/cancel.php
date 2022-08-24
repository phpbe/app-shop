<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <script src="<?php echo $wwwUrl; ?>/js/order/order-cancel.js"></script>
</be-head>


<be-page-content>
    <div class="be-d-flex">
        <div class="be-west">
            <be-include>App.ShopFai.UserCenter.west</be-include>
        </div>
        <div class="be-center">
            <h4 class="be-h4">
                <a href="<?php echo beURL('ShopFai.Order.orders') ;?>"><i class="user-center-back"></i></a>
                Cancel Order
            </h4>

            <div class="be-fc be-mt-200 be-p-100 be-bc-eee">
                <div class="be-fl">
                    Order No. <?php echo $this->order->order_sn; ?>
                </div>
                <div class="be-fr">
                    <?php echo date('M j, Y', strtotime($this->order->create_time)); ?>
                </div>
            </div>

            <h5 class="be-h5 be-mt-200">Cancel Reason</h5>

            <form class="be-mt-100" id="order-cancel-form">
                <input type="hidden" name="orderId" value="<?php echo $this->order->id; ?>" />
                <div class="be-floating">
                    <textarea name="reason" class="be-input" rows="3" placeholder="Reason" style="min-height: 120px;"></textarea>
                    <label class="be-floating-label">Reason</label>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-24 be-col-md-12 be-col-lg-6">
                        <input type="submit" class="be-btn be-btn-lg be-w-100" value="Confirm cancel the Order">
                    </div>
                </div>
            </form>

            <div class="be-d-block be-d-lg-none">
                <div class="be-mt-150 be-row">
                    <div class="be-col-24 be-col-md-12 be-col-lg-6">
                        <a href="<?php echo beURL('ShopFai.UserCenter.dashboard') ;?>" class="be-btn be-btn-outline be-btn-lg be-w-100">Back</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</be-page-content>