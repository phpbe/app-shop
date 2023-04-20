<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/order/detail.css" />
</be-head>


<be-middle>

    <div class="be-container be-mt-200 be-mb-400">
        <div class="be-d-flex">
            <div class="be-west">
                <be-include>App.Shop.UserCenter.west</be-include>
            </div>
            <div class="be-center">
                <h4 class="be-h4">
                    <a href="<?php echo beURL('Shop.Order.orders') ;?>"><i class="bi-chevron-left"></i></a>
                    Order Details
                </h4>

                <!-- 手机端 开始 -->
                <div class="be-d-block be-d-lg-none">
                    <div class="be-row be-mt-150">
                        <div class="be-col-auto" style="min-width: 8rem;">
                            Shipped To:
                        </div>
                        <div class="be-col">
                            <?php echo $this->order->shipping_address->first_name; ?>&nbsp;
                            <?php echo $this->order->shipping_address->last_name; ?><br/>

                            <?php echo $this->order->shipping_address->address; ?>
                            <?php echo $this->order->shipping_address->address2; ?>
                            <?php echo $this->order->shipping_address->city; ?>
                            <?php echo $this->order->shipping_address->zip_code; ?>
                            <?php echo $this->order->shipping_address->state; ?><br/>

                            <?php echo $this->order->shipping_address->country; ?><br/>

                            <?php echo $this->order->shipping_address->mobile; ?>
                        </div>
                    </div>


                    <div class="be-row be-mt-150">
                        <div class="be-col-auto" style="min-width: 8rem;">
                            Billed To:
                        </div>
                        <div class="be-col">
                            <?php echo $this->order->billing_address->first_name; ?>&nbsp;
                            <?php echo $this->order->billing_address->last_name; ?><br/>

                            <?php echo $this->order->billing_address->address; ?>
                            <?php echo $this->order->billing_address->address2; ?>
                            <?php echo $this->order->billing_address->city; ?>
                            <?php echo $this->order->billing_address->zip_code; ?>
                            <?php echo $this->order->billing_address->state; ?><br/>

                            <?php echo $this->order->billing_address->country; ?><br/>

                            <?php echo $this->order->billing_address->mobile; ?>
                        </div>
                    </div>

                    <div class="be-mt-200 be-py-100 be-fw-bold be-bb">Summary</div>
                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Number:</div>
                        <div class="be-col-12 be-ta-right"><?php echo $this->order->order_sn; ?></div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Status:</div>
                        <div class="be-col-12 be-ta-right"><?php echo $this->order->status_name; ?></div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Created:</div>
                        <div class="be-col-12 be-ta-right"><?php echo $this->order->create_time; ?></div>
                    </div>

                    <?php if ($this->order->status == 'cancelled') { ?>
                        <div class="be-row be-mt-100">
                            <div class="be-col-12">Cancelled:</div>
                            <div class="be-col-12 be-ta-right"><?php echo $this->order->cancel_time; ?></div>
                        </div>
                    <?php } elseif ($this->order->status == 'expired')  { ?>
                        <div class="be-row be-mt-100">
                            <div class="be-col-12">Payment expired:</div>
                            <div class="be-col-12 be-ta-right"><?php echo $this->order->pay_expire_time; ?></div>
                        </div>
                    <?php } else {
                        $tPaymentTime = strtotime($this->order->pay_time);
                        $tShipTime = strtotime($this->order->ship_time);
                        $tReceiveTime = strtotime($this->order->receive_time);
                        ?>

                        <?php if ($tPaymentTime > 0) { ?>
                            <div class="be-row be-mt-100">
                                <div class="be-col-12">Payment:</div>
                                <div class="be-col-12 be-ta-right"><?php echo $this->order->pay_time; ?></div>
                            </div>
                        <?php } ?>

                        <?php if ($tShipTime > 0) { ?>
                            <div class="be-row be-mt-100">
                                <div class="be-col-12">Shpped:</div>
                                <div class="be-col-12 be-ta-right"><?php echo $this->order->ship_time; ?></div>
                            </div>
                        <?php } ?>

                        <?php if ($tReceiveTime > 0) { ?>
                            <div class="be-row be-mt-100">
                                <div class="be-col-12">Received:</div>
                                <div class="be-col-12 be-ta-right"><?php echo $this->order->receive_time; ?></div>
                            </div>
                        <?php } ?>
                    <?php }  ?>


                    <div class="be-mt-200 be-py-100 be-fw-bold be-bb">Products</div>
                    <?php
                    $totalQuantity = 0;
                    foreach ($this->order->products as $product) {
                        $totalQuantity += $product->quantity;
                        ?>
                        <div class="be-row be-mt-100">
                            <div style="width: 6rem;">
                                <img class="be-mw-100" src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                            </div>
                            <div class="be-col">
                                <div class="be-mx-50">
                                    <a class="be-d-block be-t-ellipsis-2 be-fw-lighter" href="<?php echo beUrl('Shop.Product.detail', ['id' => $product->product_id]); ?>" target="_blank">
                                        <?php echo $product->name; ?>
                                    </a>
                                    <div class="be-c-999">
                                        <?php echo $product->style; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="be-col-auto be-ta-right">
                                <div><?php echo $this->configStore->currencySymbol . $product->price; ?></div>
                                <div class="be-c-999">&times;<?php echo $product->quantity; ?></div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="be-row be-mt-200">
                        <div class="be-col-12">Total Quantity:</div>
                        <div class="be-col-12 be-ta-right be-c-red"><?php echo $totalQuantity; ?></div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Subtotal:</div>
                        <div class="be-col-12 be-ta-right be-c-red"><?php echo $this->configStore->currencySymbol . $this->order->product_amount; ?></div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Discount:</div>
                        <div class="be-col-12 be-ta-right be-c-red"><?php echo $this->configStore->currencySymbol . $this->order->discount_amount; ?></div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Shipping charges:</div>
                        <div class="be-col-12 be-ta-right be-c-red"><?php echo $this->configStore->currencySymbol . $this->order->shipping_fee; ?></div>
                    </div>

                    <div class="be-row be-mt-100">
                        <div class="be-col-12">Total:</div>
                        <div class="be-col-12 be-ta-right be-c-red"><?php echo $this->configStore->currencySymbol . $this->order->amount; ?></div>
                    </div>


                    <div class="be-pt-100">
                        <?php
                        switch ($this->order->status) {
                            case 'pending':
                                ?>
                                <a class="be-mt-100 be-btn be-w-100" href="<?php echo beURL('Shop.Payment.pay', ['order_id' => $this->order->id]) ;?>">Pay now</a>
                                <a class="be-mt-100 be-btn be-btn-orange be-w-100" href="<?php echo beURL('Shop.Order.cancel', ['order_id' => $this->order->id]) ;?>">Cancel</a>
                                <?php
                                break;
                        }
                        ?>
                        <a class="be-mt-100 be-btn be-btn-outline be-w-100" href="<?php echo beURL('Shop.Order.orders') ;?>">Back</a>
                    </div>

                </div>
                <!-- 手机端 结束 -->


                <!-- PC端 开始 -->
                <div class="be-d-none be-d-lg-block">

                    <div class="be-fc be-mt-200">
                        <div class="be-fl">
                            No.&nbsp;<?php echo $this->order->order_sn; ?>
                        </div>
                        <div class="be-fr">
                            <?php echo $this->order->status_name; ?>
                        </div>
                    </div>

                    <?php
                    if ($this->order->status == 'cancelled' || $this->order->status == 'expired') {
                        ?>
                        <div class="be-mt-200 order-detail-progress">
                            <div class="order-detail-progress-bar be-w-50"><?php echo date('m/d/Y H:i', strtotime($this->order->create_time)); ?></div>

                            <?php if ($this->order->status == 'cancelled') { ?>
                                <div class="order-detail-progress-bar be-w-50"><?php echo date('m/d/Y H:i', strtotime($this->order->cancel_time)); ?></div>
                            <?php } else { ?>
                                <div class="order-detail-progress-bar be-w-50"><?php echo date('m/d/Y H:i', strtotime($this->order->pay_expire_time)); ?></div>
                            <?php } ?>
                        </div>
                        <div class="be-row be-mt-100" style="font-size: small">
                            <div class="be-col be-ta-center">Created<br/>Awaiting Payment</div>
                            <div class="be-col be-ta-center">
                                <?php echo $this->order->status == 'cancelled' ? 'Cancelled' : 'Payment expired'; ?>
                            </div>
                        </div>
                        <?php
                    } else {
                        $tPaymentTime = strtotime($this->order->pay_time);
                        $tShipTime = strtotime($this->order->ship_time);
                        $tReceiveTime = strtotime($this->order->receive_time);
                        ?>
                        <div class="be-row be-mt-200 order-detail-progress">
                            <div class="order-detail-progress-bar be-w-20"><?php echo date('m/d/Y H:i', strtotime($this->order->create_time)); ?></div>

                            <?php if ($tPaymentTime > 0) { ?>
                                <div class="order-detail-progress-bar be-w-20"><?php echo date('m/d/Y H:i', $tPaymentTime); ?></div>
                            <?php } ?>

                            <?php if ($tShipTime > 0) { ?>
                                <div class="order-detail-progress-bar be-w-20"><?php echo date('m/d/Y H:i', $tShipTime); ?></div>
                            <?php } ?>

                            <?php if ($tReceiveTime > 0) { ?>
                                <div class="order-detail-progress-bar be-w-20"><?php echo date('m/d/Y H:i', $tReceiveTime); ?></div>
                            <?php } ?>

                            <?php if ($tReceiveTime > 0) { ?>
                                <div class="order-detail-progress-bar be-w-20"></div>
                            <?php } ?>
                        </div>
                        <div class="be-row be-mt-100" style="font-size: small">
                            <div class="be-col be-ta-center">Created<br/>Awaiting Payment</div>
                            <div class="be-col be-ta-center">Paid<br/>Awaiting Shipping</div>
                            <div class="be-col be-ta-center">Shipped<br/>Awaiting Receive</div>
                            <div class="be-col be-ta-center">Received</div>
                            <div class="be-col be-ta-center">Complete</div>
                        </div>
                        <?php
                    }
                    ?>


                    <h5 class="be-h5 be-mt-250">Order Information</h5>

                    <div class="order-detail-information be-mt-100">
                        <div class="be-row">
                            <div class="be-col-14" style="background-color: #fff;">
                                <table>
                                    <tbody>
                                    <tr>
                                        <th>Order Date:</th>
                                        <td><?php echo date('M j, Y', strtotime($this->order->create_time)); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Shipped To:</th>
                                        <td>

                                            <?php echo $this->order->shipping_address->first_name; ?>&nbsp;
                                            <?php echo $this->order->shipping_address->last_name; ?><br/>

                                            <?php echo $this->order->shipping_address->address; ?>
                                            <?php echo $this->order->shipping_address->address2; ?>
                                            <?php echo $this->order->shipping_address->city; ?>
                                            <?php echo $this->order->shipping_address->zip_code; ?>
                                            <?php echo $this->order->shipping_address->state_name; ?><br/>

                                            <?php echo $this->order->shipping_address->country_name; ?><br/>

                                            <?php echo $this->order->shipping_address->mobile; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Billed To:</th>
                                        <td>

                                            <?php echo $this->order->billing_address->first_name; ?>&nbsp;
                                            <?php echo $this->order->billing_address->last_name; ?><br/>

                                            <?php echo $this->order->billing_address->address; ?>
                                            <?php echo $this->order->billing_address->address2; ?>
                                            <?php echo $this->order->billing_address->city; ?>
                                            <?php echo $this->order->billing_address->zip_code; ?>
                                            <?php echo $this->order->billing_address->state_name; ?><br/>

                                            <?php echo $this->order->billing_address->country_name; ?><br/>

                                            <?php echo $this->order->billing_address->mobile; ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                            <div class="be-col-10">
                                <table>
                                    <tbody>
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td><?php echo $this->configStore->currencySymbol . $this->order->product_amount; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Discount:</th>
                                        <td><?php echo $this->configStore->currencySymbol . $this->order->discount_amount; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Shipping charges:</th>
                                        <td><?php echo $this->configStore->currencySymbol . $this->order->shipping_fee; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Grand Total:</th>
                                        <td><span style="font-size: 24px; color: #e53935; "><?php echo $this->configStore->currencySymbol . $this->order->amount; ?></span></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div class="be-mt-100 be-ta-center">
                                <?php
                                switch ($this->order->status) {
                                    case 'pending':
                                        ?>
                                        <a class="be-btn" href="<?php echo beURL('Shop.Payment.pay', ['order_id' => $this->order->id]) ;?>">Pay now</a>
                                        <a class="be-btn be-btn-orange" href="<?php echo beURL('Shop.Order.cancel', ['order_id' => $this->order->id]) ;?>">Cancel</a>
                                        <?php
                                        break;
                                }
                                ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <h5 class="be-h5 be-mt-250">Order summary</h5>

                    <table class="be-table be-mt-100">
                        <thead>
                        <tr>
                            <th style="width: 6rem;"></th>
                            <th class="be-ta-left">Item</th>
                            <th class="be-ta-center" style="max-width: 80px;">Price</th>
                            <th class="be-ta-center" style="max-width: 80px;">Qry</th>
                            <th class="be-ta-center" style="max-width: 80px;">Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($this->order->products as $product) {
                            ?>
                            <tr class="product">
                                <td>
                                    <img class="be-mw-100" src="<?php echo $product->image; ?>" alt="<?php echo $product->_name; ?>">
                                </td>
                                <td class="be-ta-left">
                                    <a class="be-d-block" href="<?php echo beUrl('Shop.Product.detail', ['id' => $product->product_id]); ?>" target="_blank">
                                        <?php echo $product->name; ?>
                                    </a>
                                    <div class="be-c-999">
                                        <?php echo $product->style; ?>
                                    </div>
                                </td>
                                <td class="be-ta-center"><?php echo $this->configStore->currencySymbol . $product->price; ?></td>
                                <td class="be-ta-center"><?php echo $product->quantity; ?></td>
                                <td class="be-ta-center"><?php echo $this->configStore->currencySymbol . $product->amount; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>

                    <a class="be-mt-100 be-btn be-btn-outline" href="<?php echo beURL('Shop.Order.orders') ;?>">Back</a>

                </div>
                <!-- PC端 结束 -->

            </div>
        </div>
    </div>
</be-middle>