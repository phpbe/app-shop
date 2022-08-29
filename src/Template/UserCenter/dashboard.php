<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/order/orders.css" />
</be-head>


<be-page-content>
    <?php
    $my = \Be\Be::getUser();
    ?>
    <div class="be-d-flex">
        <div class="be-flex-0">
            <img src="<?php echo $my->avatar; ?>" alt="<?php echo $my->first_name . ' ' . $my->last_name; ?>">
        </div>

        <div class="be-flex-1 be-pl-100">
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

    <div class="be-d-block be-d-lg-none">
        <ul class="be-mt-200 user-center-dashboard-nav">
            <li>
                <a href="<?php echo beUrl('ShopFai.Order.orders'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                    <span>My Orders</span>
                    <i class="user-center-dashboard-nav-right"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo beUrl('ShopFai.UserFavorite.favorites'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 4.41c1.387-1.425 4.854 1.07 0 4.277C3.146 5.48 6.613 2.986 8 4.412z"/>
                        <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
                    </svg>
                    <span>Wish List</span>
                    <i class="user-center-dashboard-nav-right"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo beUrl('ShopFai.UserAddress.addresses'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A31.493 31.493 0 0 1 8 14.58a31.481 31.481 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94zM8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    </svg>
                    <span>Address Book</span>
                    <i class="user-center-dashboard-nav-right"></i>
                </a>
            </li>
            <li>
                <a href="<?php echo beUrl('ShopFai.UserCenter.setting'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                    </svg>
                    <span>Setting</span>
                    <i class="user-center-dashboard-nav-right"></i>
                </a>
            </li>
        </ul>

        <div class="be-pt-150">
            <a href="<?php echo beUrl('ShopFai.User.logout'); ?>" class="be-btn be-btn-lg be-w-100">Sign Out</a>
        </div>
    </div>

    <div class="be-d-none be-d-lg-block">
        <?php
        $option = [];
        $option['pageSize'] = 20;
        $option['page'] = 1;
        $option['statusIn'] = ['pending'];

        $orders = \Be\Be::getService('App.ShopFai.Order')->getOrders($option);
        if (count($orders) > 0) {
            ?>
            <div class="be-fc be-mt-200">
                <h5 class="be-h5 be-fl">Awaiting Payment Orders</h5>
                <a class="be-fr" href="<?php echo beUrl('ShopFai.Order.orders'); ?>">Show more</a>
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
                            Order No: <span><a href="<?php echo beURL('ShopFai.Order.detail', ['order_id' => $order->id]) ;?>"><?php echo $order->order_sn; ?></a></span>
                        </td>
                    </tr>
                    <?php
                    $n = count($order->products);
                    $i = 0;
                    foreach ($order->products as $product) {
                        ?>
                        <tr>
                            <td class="be-table-image">
                                <a href="<?php echo beUrl('ShopFai.Product.detail', ['id' => $product->product_id]); ?>" target="_blank">
                                    <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                                </a>
                            </td>
                            <td>
                                <a class="be-d-block be-t-ellipsis-2" href="<?php echo beUrl('ShopFai.Product.detail', ['id' => $product->product_id]); ?>" target="_blank">
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
                                            <a class="be-btn be-btn-sm be-w-100" href="<?php echo beURL('ShopFai.Cart.payment', ['order_id' => $order->id]) ;?>" target="_blank">Pay Now</a>
                                            <?php
                                            $moreAction[] = [
                                                'name' => 'Cancel',
                                                'url' => beURL('ShopFai.Order.cancel', ['order_id' => $order->id]),
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
                                            <li><a href="<?php echo beUrl('ShopFai.Order.detail', ['order_id' => $order->id]); ?>">Detail</a></li>
                                            <li><a href="<?php echo beUrl('ShopFai.Order.contact', ['order_id' => $order->id]); ?>">Contact</a></li>
                                            <?php
                                            if (count($moreAction) > 0) {
                                                foreach ($moreAction as $action) {
                                                    ?>
                                                    <li><a href="<?php echo $action['url'] ?>" target="<?php echo $action['target'] ?>"><?php echo $action['name'] ?></a></li>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <li><a href="<?php echo beUrl('ShopFai.Order.printOrder', ['order_id' => $order->id]); ?>" target="_blank">Print</a></li>
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