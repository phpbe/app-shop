<be-html>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $this->title; ?></title>
        <link rel="stylesheet" href="https://cdn.phpbe.com/scss/be.css" />
        <style>
            html {
                font-size: 16px;
                background-color: #fff;
                color: #000;
            }

            a {
                text-decoration: none;
                color: #000;
            }

            a:active, a:hover {
                color: #000;
                outline: none;
                text-decoration: underline;
            }
        </style>

    </head>
    <body>
        <div style="width:700px;padding:0 30px;margin:0 auto; color: #000;">

            <div class="be-bb be-pt-300 be-pb-100">
                <a class="be-fs-200 be-lh-250 be-td-none" href="https://www.max.hk">Max</a>
            </div>

            <div class="be-fs-200 be-lh-300 be-mt-300">
                Your order is <?php echo $this->order->status_name; ?>
            </div>

            <div class="be-mt-200">
                Order No. <?php echo $this->order->order_sn; ?>
            </div>

            <?php
            $my = \Be\Be::getUser();
            ?>
            <div class="be-mt-200 be-c-666">
                Hi <?php echo $my->first_name . ' ' . $my->last_name; ?>, Your order is <?php echo $this->order->status_name; ?>.
            </div>


            <div class="be-mt-400 be-fs-125 be-lh-150">Order summary</div>

            <?php
            foreach ($this->order->products as $product) {
                ?>
                <div class="be-row be-mt-150">
                    <div class="be-col-auto" style="width: 70px;">
                        <img class="be-mw-100" src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                    </div>
                    <div class="be-col">
                        <div class="be-px-50">
                            <a class="be-d-block" href="<?php echo beUrl('ShopFai.Product.detail', ['id' => $product->product_id]); ?>">
                                <?php echo $product->name; ?>
                            </a>

                            <div class="be-c-999">
                                <?php echo $product->style; ?>
                            </div>
                        </div>
                    </div>
                    <div class="be-col-auto">
                        <div class="be-ta-right"><?php echo $this->configStore->currencySymbol . $product->price; ?></div>
                        <div class="be-ta-right be-c-999">x<?php echo $product->quantity; ?></div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="be-bt be-mt-150">

                <div class="be-mt-150 be-row">
                    <div class="be-col-16 be-c-666">Subtotal:</div>
                    <div class="be-col-8 be-c-999 be-ta-right"><?php echo $this->configStore->currencySymbol . $this->order->product_amount; ?></div>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-16 be-c-666">Discount:</div>
                    <div class="be-col-8 be-c-999 be-ta-right"><?php echo $this->configStore->currencySymbol . $this->order->discount_amount; ?></div>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-16 be-c-666">Shipping Charges & Insurance:</div>
                    <div class="be-col-8 be-c-999 be-ta-right"><?php echo $this->configStore->currencySymbol . $this->order->shipping_fee; ?></div>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-16 be-c-666">Grand Total:</div>
                    <div class="be-col-8 be-c-999 be-ta-right"><?php echo $this->configStore->currencySymbol . $this->order->amount; ?></div>
                </div>

            </div>

        </div>>

        <script type="text/javascript">window.print();</script>
    </body>
    </html>
</be-html>