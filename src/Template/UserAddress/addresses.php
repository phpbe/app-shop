<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <script src="<?php echo $wwwUrl; ?>/js/user-center/be-tab.js"></script>

    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-address/addresses.css" />

    <script>
        const userAddress_setDefaultShippingAddressUrl = "<?php echo beUrl('Shop.UserAddress.setDefaultShippingAddress'); ?>";
        const userAddress_deleteShippingAddressUrl = "<?php echo beUrl('Shop.UserAddress.deleteShippingAddress'); ?>";
        const userAddress_deleteBillingAddressUrl = "<?php echo beUrl('Shop.UserAddress.deleteBillingAddress'); ?>";
    </script>
    <script src="<?php echo $wwwUrl; ?>/js/user-address/addresses.js"></script>
</be-head>


<be-page-content>
    <div class="be-d-block be-md-d-none">
        <h4 class="be-h4">
            <a href="<?php echo beURL('Shop.UserCenter.dashboard') ;?>"><i class="bi-chevron-left"></i></a>
            Address Book
        </h4>

        <div class="be-ta-center be-p-75 be-mt-200" style="background-color: #f9f9f9;">Shipping Address</div>

        <?php
        if (count($this->shippingAddresses) > 0) {
            $i = 0;
            foreach ($this->shippingAddresses as $shippingAddress) {
                ?>
                <div class="be-p-100 be-mt-200" style="border: #eee 1px solid;">
                    <div>
                        <?php echo $shippingAddress->first_name . ' ' . $shippingAddress->last_name; ?>
                        <?php
                        if ($shippingAddress->is_default) {
                            echo ' &nbsp; <span class="user-address-is-default">default</span>';
                        }
                        ?>
                    </div>

                    <div class="be-c-999 be-mt-30"><?php echo $shippingAddress->address . ' ' . $shippingAddress->address2; ?></div>
                    <div class="be-c-999 be-mt-30"><?php echo $shippingAddress->city . ' ' . $shippingAddress->country_name . ' ' . $shippingAddress->state_name . ' ' . $shippingAddress->zip_code; ?></div>
                    <div class="be-c-999 be-mt-30"><?php echo $shippingAddress->mobile; ?></div>

                    <div class="be-mt-100 be-pt-100 be-pr-100 be-ta-right" style="border-top: #eee 1px solid;">
                        <?php
                        if (!$shippingAddress->is_default) {
                            ?>
                            <a class="be-btn be-btn-sm be-btn-outline" href="javascript:void(0);" onclick="setDefaultShippingAddress('<?php echo $shippingAddress->id; ?>')">
                                <i class="user-address-set-default"></i>
                                Set Default
                            </a>
                            <?php
                        }
                        ?>
                        <a class="be-btn be-btn-sm be-btn-outline" href="<?php echo beUrl('Shop.UserAddress.editShippingAddress', ['id' => $shippingAddress->id]); ?>">
                            <i class="user-address-edit"></i>
                            Edit
                        </a>
                        <a class="be-btn be-btn-sm be-btn-outline" href="javascript:void(0);" onclick="deleteShippingAddress('<?php echo $shippingAddress->id; ?>')">
                            <i class="user-address-delete"></i>
                            Delete
                        </a>
                    </div>
                </div>
                <?php
                $i++;
            }
        }
        ?>

        <?php if (count($this->shippingAddresses) < 10) { ?>
            <div class="be-mt-150">
                <a href="<?php echo beUrl('Shop.UserAddress.editShippingAddress'); ?>" class="be-btn be-btn-major be-btn-lg be-w-100">
                    <i class="user-address-add"></i> Add a new address
                </a>
            </div>
        <?php } ?>

        <div class="be-ta-center be-p-75 be-mt-200" style="background-color: #f9f9f9;">Billing Address</div>

        <?php
        if ($this->billingAddress)
        {
            ?>
            <div class="be-p-100 be-mt-200 be-b">
                <div>
                    <?php echo $this->billingAddress->first_name . ' ' . $this->billingAddress->last_name; ?>
                </div>

                <div class="be-c-999 be-mt-30"><?php echo $this->billingAddress->address . ' ' . $this->billingAddress->address2; ?></div>
                <div class="be-c-999 be-mt-30"><?php echo $this->billingAddress->city . ' ' . $this->billingAddress->country_name . ' ' . $this->billingAddress->state_name . ' ' . $this->billingAddress->zip_code; ?></div>
                <div class="be-c-999 be-mt-30"><?php echo $this->billingAddress->mobile; ?></div>

                <div class="be-mt-100 be-pt-100 be-pr-100 be-ta-right" style="border-top: #eee 1px solid;">
                    <a class="be-btn be-btn-sm be-btn-outline" href="<?php echo beUrl('Shop.UserAddress.editBillingAddress'); ?>">
                        <i class="user-address-edit"></i>
                        Edit
                    </a>
                    <a class="be-btn be-btn-sm be-btn-outline" href="javascript:void(0);" onclick="deleteBillingAddress()">
                        <i class="user-address-delete"></i>
                        Delete
                    </a>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="be-mt-150">
                <a href="<?php echo beUrl('Shop.UserAddress.editBillingAddress'); ?>" class="be-btn be-btn-major be-btn-lg be-w-100">
                    <i class="user-address-add"></i> Add a new address
                </a>
            </div>
            <?php
        }
        ?>

        <div class="be-mt-150 be-row">
            <a href="<?php echo beURL('Shop.UserCenter.dashboard') ;?>" class="be-btn be-btn-lg be-w-100">Back</a>
        </div>

    </div>

    <div class="be-d-none be-md-d-block">
        <h4 class="be-h4">Address Book</h4>

        <div class="be-tab">

            <div class="be-tab-nav be-mt-150">
                <a class="be-tab-nav-active" data-be-target="#be-tab-pane-shipping-address">Shipping Address</a>
                <a data-be-target="#be-tab-pane-billing-address">Billing Address</a>
            </div>

            <div class="be-tab-content">

                <div class="be-tab-pane" id="be-tab-pane-shipping-address">

                    <div class="be-row">
                        <?php
                        if (count($this->shippingAddresses) > 0) {
                            $i = 0;
                            foreach ($this->shippingAddresses as $shippingAddress) {
                                ?>
                                <div class="be-col-24 be-xxl-col-12">

                                    <div class="be-p-relative be-p-100 be-mt-150" style="border: #eee 1px solid; margin-right: 1rem;">
                                        <div>
                                            <?php echo $shippingAddress->first_name . ' ' . $shippingAddress->last_name; ?>
                                            <?php
                                            if ($shippingAddress->is_default) {
                                                echo ' &nbsp; <span class="user-address-is-default">default</span>';
                                            }
                                            ?>
                                        </div>

                                        <div class="be-c-999 be-mt-30"><?php echo $shippingAddress->address . ' ' . $shippingAddress->address2; ?></div>
                                        <div class="be-c-999 be-mt-30"><?php echo $shippingAddress->city . ' ' . $shippingAddress->country_name . ' ' . $shippingAddress->state_name . ' ' . $shippingAddress->zip_code; ?></div>
                                        <div class="be-c-999 be-mt-30"><?php echo $shippingAddress->mobile; ?></div>

                                        <div class="be-p-absolute be-t-0 be-r-0 be-pt-50 be-pr-50">
                                            <div class="be-dropdown">
                                                <button class="be-btn be-btn-sm be-dropdown-toggle" type="button" onclick="$(this).parent().toggleClass('be-dropdown-open')"  onblur="var _this = this; setTimeout(function() {$(_this).parent().removeClass('be-dropdown-open');}, 300)">Action</button>
                                                <ul>
                                                    <li>
                                                        <a href="<?php echo beUrl('Shop.UserAddress.editShippingAddress', ['id' => $shippingAddress->id]); ?>">
                                                            <i class="user-address-edit"></i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="deleteShippingAddress('<?php echo $shippingAddress->id; ?>')">
                                                            <i class="user-address-delete"></i>
                                                            Delete
                                                        </a>
                                                    </li>
                                                    <?php
                                                    if (!$shippingAddress->is_default) {
                                                        ?>
                                                        <li>
                                                            <a href="javascript:void(0);" onclick="setDefaultShippingAddress('<?php echo $shippingAddress->id; ?>')">
                                                                <i class="user-address-set-default"></i>
                                                                Set Default
                                                            </a>
                                                        </li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </div>

                    <?php if (count($this->shippingAddresses) < 10) { ?>
                    <div class="be-mt-150">
                        <a href="<?php echo beUrl('Shop.UserAddress.editShippingAddress'); ?>" class="be-btn be-btn-major be-btn-lg">
                            <i class="user-address-add"></i> Add a new address
                        </a>
                    </div>
                    <?php } ?>

                </div>

                <div class="be-tab-pane" id="be-tab-pane-billing-address">
                    <?php
                    if ($this->billingAddress)
                    {
                        ?>
                        <div class="be-row">
                            <div class="be-col-24 be-xxl-col-12">
                                <div class="be-p-relative be-p-100 be-mt-150" style="border: #eee 1px solid; margin-right: 1rem;">
                                    <div>
                                        <?php echo $this->billingAddress->first_name . ' ' . $this->billingAddress->last_name; ?>
                                    </div>

                                    <div class="be-c-999 be-mt-30"><?php echo $this->billingAddress->address . ' ' . $this->billingAddress->address2; ?></div>
                                    <div class="be-c-999 be-mt-30"><?php echo $this->billingAddress->city . ' ' . $this->billingAddress->country_name . ' ' . $this->billingAddress->state_name . ' ' . $this->billingAddress->zip_code; ?></div>
                                    <div class="be-c-999 be-mt-30"><?php echo $this->billingAddress->mobile; ?></div>

                                    <div class="be-p-absolute be-t-0 be-r-0 be-pt-50 be-pr-50">
                                        <div class="be-dropdown">
                                            <button class="be-btn be-btn-sm be-dropdown-toggle" type="button" onclick="$(this).parent().toggleClass('be-dropdown-open')"  onblur="var _this = this; setTimeout(function() {$(_this).parent().removeClass('be-dropdown-open');}, 300)">Action</button>
                                            <ul>
                                                <li>
                                                    <a href="<?php echo beUrl('Shop.UserAddress.editBillingAddress'); ?>">
                                                        <i class="user-address-edit"></i>
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" onclick="deleteBillingAddress()">
                                                        <i class="user-address-delete"></i>
                                                        Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="be-mt-150">
                            <a href="<?php echo beUrl('Shop.UserAddress.editBillingAddress'); ?>" class="be-btn be-btn-major be-btn-lg">
                                <i class="user-address-add"></i> Add a new address
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                </div>

            </div>
        </div>

    </div>
</be-page-content>