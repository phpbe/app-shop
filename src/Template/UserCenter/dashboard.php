<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/order/orders.css" />
</be-head>


<be-page-content>
    <?php
    $my = \Be\Be::getUser();
    ?>
    <div class="be-rowx">
        <div class="be-col-24 be-md-col-auto">
            <img src="<?php echo $my->avatar; ?>" alt="<?php echo $my->first_name . ' ' . $my->last_name; ?>">
        </div>

        <div class="be-col-24 be-md-col-auto">
            <div class="be-pl-100 be-mt-100"></div>
        </div>

        <div class="be-col-24 be-md-col">
            <div class="be-mt-50 be-fs-150 be-fw-bold">
                <?php echo $my->first_name . ' ' . $my->last_name; ?>
            </div>
            <div class="be-mt-50">
                <?php echo $my->email; ?>
            </div>
            <div class="be-mt-50 be-c-999">
                Last login at: <?php echo date('Y-m-d H:i', strtotime($my->last_login_time)); ?>
            </div>
        </div>
    </div>

    <div class="be-d-block be-lg-d-none">
        <ul class="be-mt-200 user-center-dashboard-nav">
            <li>
                <a href="<?php echo beUrl('Shop.Order.orders'); ?>">
                    <i class="bi-list-ul"></i>
                    <span>My Orders</span>
                    <i class="bi-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo beUrl('Shop.UserFavorite.favorites'); ?>">
                    <i class="bi-bag-heart"></i>
                    <span>Wish List</span>
                    <i class="bi-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo beUrl('Shop.UserAddress.addresses'); ?>">
                    <i class="bi-geo"></i>
                    <span>Address Book</span>
                    <i class="bi-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo beUrl('Shop.UserCenter.setting'); ?>">
                    <i class="bi-gear"></i>
                    <span>Setting</span>
                    <i class="bi-chevron-right"></i>
                </a>
            </li>
        </ul>

        <div class="be-pt-150">
            <a href="<?php echo beUrl('Shop.User.logout'); ?>" class="be-btn be-btn-lg be-w-100">Sign Out</a>
        </div>
    </div>

    <div class="be-d-none be-lg-d-block">
        <?php
        $option = [];
        $option['pageSize'] = 20;
        $option['page'] = 1;
        $option['statusIn'] = ['pending'];

        $orders = \Be\Be::getService('App.Shop.Order')->getOrders($option);
        if (count($orders) > 0) {
            ?>
            <div class="be-fc be-mt-200">
                <h5 class="be-h5 be-fl">Awaiting Payment Orders</h5>
                <a class="be-fr" href="<?php echo beUrl('Shop.Order.orders'); ?>">Show more</a>
            </div>

            <table class="be-mt-200 be-table">
                <thead>
                <tr>
                    <th class="be-ta-center" colspan="2">Order Information</th>
                    <th class="be-ta-center" style="max-width: 80px;">Price</th>
                    <th class="be-ta-center" style="max-width: 80px;">Qty</th>
                    <th class="be-ta-center" style="max-width: 80px;">Amount</th>
                    <th style="width: 120px; text-align: center;">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr class="order-table-head-space">
                    <td colspan="7"></td>
                </tr>
                <?php
                foreach ($orders as $order) {
                    ?>
                    <tr>
                        <td colspan="7" class="be-c-999">
                            <span class="me-4"><?php echo date('M j Y', strtotime($order->create_time)); ?></span>
                            Order No: <span><a href="<?php echo beURL('Shop.Order.detail', ['order_id' => $order->id]) ;?>"><?php echo $order->order_sn; ?></a></span>
                        </td>
                    </tr>
                    <?php
                    $n = count($order->products);
                    $i = 0;
                    foreach ($order->products as $product) {
                        ?>
                        <tr>
                            <td class="be-table-image">
                                <a href="<?php echo beUrl('Shop.Product.detail', ['id' => $product->product_id]); ?>" target="_blank">
                                    <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                </a>
                            </td>
                            <td>
                                <a class="be-d-block be-t-ellipsis-2" href="<?php echo beUrl('Shop.Product.detail', ['id' => $product->product_id]); ?>" target="_blank">
                                    <?php echo $product->name; ?>
                                </a>
                                <div class="be-c-999">
                                    <?php echo $product->style; ?>
                                </div>
                            </td>
                            <td class="be-ta-center"><?php echo $this->configStore->currencySymbol . $product->price; ?></td>
                            <td class="be-ta-center"><?php echo $product->quantity; ?></td>
                            <?php
                            if ($i == 0) {
                                ?>
                                <td class="be-ta-center" rowspan="<?php echo $n; ?>"><?php echo $this->configStore->currencySymbol . $order->amount; ?></td>
                                <td class="be-ta-center" rowspan="<?php echo $n; ?>">
                                    <?php
                                    $moreAction = [];
                                    switch ($order->status) {
                                        case 'pending':
                                            ?>
                                            <a class="be-btn be-btn-sm be-w-100" href="<?php echo beURL('Shop.Cart.payment', ['order_id' => $order->id]) ;?>" target="_blank">Pay Now</a>
                                            <?php
                                            $moreAction[] = [
                                                'name' => 'Cancel',
                                                'url' => beURL('Shop.Order.cancel', ['order_id' => $order->id]),
                                                'target' => '_self'
                                            ];
                                            break;
                                    }
                                    ?>

                                    <div class="be-dropdown be-mt-50 be-w-100">
                                        <button class="be-btn be-btn-outline be-btn-sm be-w-100 be-dropdown-toggle" type="button" onclick="$(this).parent().toggleClass('be-dropdown-open')" onblur="var _this = this; setTimeout(function() {$(_this).parent().removeClass('be-dropdown-open');}, 300)">
                                            View More
                                        </button>
                                        <ul>
                                            <li><a href="<?php echo beUrl('Shop.Order.detail', ['order_id' => $order->id]); ?>">Detail</a></li>
                                            <li><a href="<?php echo beUrl('Shop.Order.contact', ['order_id' => $order->id]); ?>">Contact</a></li>
                                            <?php
                                            if (count($moreAction) > 0) {
                                                foreach ($moreAction as $action) {
                                                    ?>
                                                    <li><a href="<?php echo $action['url'] ?>" target="<?php echo $action['target'] ?>"><?php echo $action['name'] ?></a></li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <li><a href="<?php echo beUrl('Shop.Order.printOrder', ['order_id' => $order->id]); ?>" target="_blank">Print</a></li>
                                        </ul>
                                    </div>

                                </td>
                                <?php
                            }
                            ?>

                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                    <tr class="order-table-body-space">
                        <td colspan="7">
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>

</be-page-content>