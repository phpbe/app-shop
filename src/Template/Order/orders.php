<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/order/orders.css" />
</be-head>


<be-page-content>

    <div class="be-d-block be-d-lg-none">
        <h4 class="be-h4">
            <a href="<?php echo beURL('Shop.UserCenter.dashboard') ;?>"><i class="bi-chevron-left"></i></a>
            My Orders
        </h4>
    </div>

    <div class="be-d-none be-d-lg-block">
        <div class="be-fc">
            <h4 class="be-fl be-h4">My Orders</h4>
            <div class="be-fr">
                <?php
                foreach ([
                             'pending' => 'Awaiting Payment',
                             'shipped' => 'Shipped',
                             'received' => 'Awaiting Review',
                         ] as $k => $v) {
                    echo '<a class="be-btn';
                    if ($this->status != $k) {
                        echo ' be-btn-outline';
                    }
                    echo ' be-btn-sm" href="' . beUrl('Shop.Order.orders', ['status' => $k]) . '">'.$v.'</a> ';
                }
                ?>
            </div>
        </div>
    </div>


    <div class="be-d-block be-d-lg-none">
        <?php
        foreach ($this->orders as $order) {
            ?>
            <div class="be-fc be-mt-100 be-py-100 be-bb">
                <div class="be-fl">
                    No. <span><a href="<?php echo beURL('Shop.Order.detail', ['order_id' => $order->id]) ;?>"><?php echo $order->order_sn; ?></a></span>
                </div>
                <div class="be-fr">
                    <a href="<?php echo beURL('Shop.Order.detail', ['order_id' => $order->id]) ;?>" class="order-mobile-status">
                        <?php echo $order->status_name; ?>
                    </a>
                </div>
            </div>

            <?php
            $items = 0;
            foreach ($order->products as $product) {
                $items += $product->quantity;
                ?>
                <div class="be-row be-mt-100">
                    <div class="be-col-auto" style="width: 6rem;">
                        <img class="be-mw-100" src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                    </div>
                    <div class="be-col">
                        <div class="be-mx-50">
                            <a class="be-fw-lighter be-t-ellipsis-2" href="<?php echo beUrl('Shop.Product.detail', ['id' => $product->product_id]); ?>">
                                <?php echo $product->name; ?>
                            </a>
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

            <div class="be-ta-right be-mt-100 be-mb-200 be-py-100 be-bb">
                <?php echo $items; ?> items. Amount: <?php echo $this->configStore->currencySymbol . $order->amount; ?>
            </div>

            <?php
        }
        ?>
    </div>

    <div class="be-d-none be-d-lg-block">

        <table class="be-mt-200 be-table">
            <thead>
            <tr>
                <th class="be-ta-center" colspan="2">Order Information</th>
                <th class="be-ta-center" style="max-width: 80px;">Price</th>
                <th class="be-ta-center" style="max-width: 80px;">Qty</th>
                <th class="be-ta-center" style="max-width: 80px;">Amount</th>
                <th class="be-ta-center" style="max-width: 150px;">
                    <div class="be-dropdown">
                        <a class="be-dropdown-toggle" href="javascript:void(0);" onclick="$(this).parent().toggleClass('be-dropdown-open')"  onblur="var _this = this; setTimeout(function() {$(_this).parent().removeClass('be-dropdown-open');}, 300)">Status</a>
                        <ul>
                            <li><a href="<?php echo beUrl('Shop.Order.orders'); ?>">All</a></li>
                            <?php
                            foreach ($this->statusKeyValues as $key => $val) {
                                echo '<li><a href="'.beUrl('Shop.Order.orders', ['status' => $key]).'">'.$val.'</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </th>
                <th style="width: 120px; text-align: center;">Action</th>
            </tr>
            </thead>
            <?php
            if (count($this->orders) > 0) {
                ?>
                <tbody>
                <tr class="order-table-head-space">
                    <td colspan="7"></td>
                </tr>
                <?php
                foreach ($this->orders as $order) {
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
                                    <span class="be-c-999"><?php echo $order->status_name; ?></span>
                                </td>
                                <td class="be-ta-center" rowspan="<?php echo $n; ?>">
                                    <?php
                                    $moreAction = [];
                                    switch ($order->status) {
                                        case 'pending':
                                            ?>
                                            <a class="be-btn be-btn-sm be-w-100" href="<?php echo beURL('Shop.Payment.pay', ['order_id' => $order->id]) ;?>" target="_blank">Pay Now</a>
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
                <?php
            } else {
                ?>
                <tbody>
                <tr>
                    <td class="be-table-no-record" colspan="7">No record</td>
                </tr>
                </tbody>
                <?php
            }
            ?>
        </table>
    </div>

    <?php
    if ($this->total > 0) {
        $paginationUrl = [
            'route' => 'Shop.Order.orders',
            'params' => [
                'status' => $this->status,
                'keywords' => urlencode($this->keywords),
            ]
        ];

        $total = $this->total;
        $pageSize = $this->pageSize;
        $page = $this->page;
        $pages = $this->pages;

        echo '<nav class="be-mt-150">';
        echo '<ul class="be-pagination" style="justify-content: center;">';
        echo '<li>';
        if ($page > 1) {
            $url = beUrl($paginationUrl['route'], array_merge($paginationUrl['params'], ['page' => ($page - 1)]));
            echo '<a href="' . $url . '">Previous</a>';
        } else {
            echo '<span>Previous</span>';
        }
        echo '</li>';

        $from = null;
        $to = null;
        if ($pages < 9) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $page - 4;
            if ($from < 1) {
                $from = 1;
            }

            $to = $from + 8;
            if ($to > $pages) {
                $to = $pages;
            }
        }

        if ($from > 1) {
            echo '<li><span>...</span></li>';
        }

        for ($i = $from; $i <= $to; $i++) {
            if ($i == $page) {
                echo '<li class="active">';
                echo '<span>' . $i . '</span>';
                echo '</li>';
            } else {
                $url = beUrl($paginationUrl['route'], array_merge($paginationUrl['params'], ['page' => $i]));
                echo '<li>';
                echo '<a href="' . $url . '">' . $i . '</a>';
                echo '</li>';
            }
        }

        if ($to < $pages) {
            echo '<li><span>...</span></li>';
        }

        echo '<li>';
        if ($page < $pages) {
            $url = beUrl($paginationUrl['route'], array_merge($paginationUrl['params'], ['page' => ($page + 1)]));
            echo '<a href="' . $url . '">Next</a>';
        } else {
            echo '<span>Next</span>';
        }
        echo '</li>';
        echo '</ul>';
        echo '</nav>';
    }
    ?>
</be-page-content>