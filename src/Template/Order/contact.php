<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/lib/lightbox/lightbox.min.js"></script>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/lib/lightbox/lightbox.min.css" />

    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />

    <script src="<?php echo $wwwUrl; ?>/js/order/contact.js"></script>
    <link rel="stylesheet" href="<?php echo $wwwUrl ?>/css/order/contact.css" />
</be-head>


<be-middle>
    <div class="be-container be-mt-200 be-mb-400">
        <div class="be-d-flex">
            <div class="be-west">
                <be-include>App.ShopFai.UserCenter.west</be-include>
            </div>
            <div class="be-center">
                <h4 class="be-h4">
                    <a href="<?php echo beURL('ShopFai.Order.orders') ;?>"><i class="user-center-back"></i></a>
                    Order Contact
                </h4>

                <div class="be-fc be-mt-200 be-p-100 be-bc-eee">
                    <div class="be-fl">
                        Order No. <?php echo $this->order->order_sn; ?>
                    </div>
                    <div class="be-fr">
                        <?php echo date('M j, Y', strtotime($this->order->create_time)); ?>
                    </div>
                </div>

                <?php
                $my = \Be\Be::getUser();
                foreach ($this->contacts as $contact) {
                    ?>
                    <div class="order-contact">
                        <div class="create-time">
                            <?php echo $contact->create_time; ?>
                        </div>

                        <?php
                        if ($contact->publisher == 'customer') {
                            ?>
                            <div class="be-row">
                                <div class="be-col">
                                    <div class="content">
                                        <?php echo $contact->content; ?>
                                        <?php
                                        if ($contact->image) {
                                            echo '<br/><a class="light-box-image" data-lightbox="roadtrip" href="' . $contact->image.'" target="_blank">';
                                            echo '<img src="' . $contact->image.'" />';
                                            echo '</a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="be-col-auto" style="width: 5rem">
                                    <div class="publisher-avatar">
                                        <img src="<?php
                                        if ($my->avatar) {
                                            echo $my->avatar;
                                        } else {
                                            echo \Be\Be::getConfig('App.ShopFai.Url')->cdn . '/image/user/avatar/default.png';
                                        }
                                        ?>" alt="<?php echo $my->first_name . ' ' . $my->last_name; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="be-row">
                                <div style="width: 5rem">
                                    <div class="publisher-avatar">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="content">
                                        <?php echo $contact->content; ?>
                                        <?php
                                        if ($contact->image) {
                                            echo '<br/><a class="light-box-image" data-lightbox="contact" href="'.$contact->image.'" target="_blank">';
                                            echo '<img src="'.$contact->image.'" />';
                                            echo '</a>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>

                <h5 class="be-mt-200">Reply Content</h5>

                <form id="order-contact-form" enctype='multipart/form-data'>
                    <input type="hidden" name="order_id" value="<?php echo $this->order->id; ?>" />

                    <div class="be-floating">
                        <textarea name="content" class="be-input" rows="3" placeholder="Content" style="min-height: 120px;"></textarea>
                        <label class="be-floating-label">Content</label>
                    </div>

                    <div class="be-mt-100">
                        <div class="upload_image_box">
                            <input class="be-input upload_image" id="upload_image" type="file" name="image" onchange="loadImg(this);" accept="image/gif,image/jpeg,image/png">
                            <div class="upload_image_preview" id="upload_image_preview"></div>
                        </div>
                    </div>

                    <div class="be-mt-200">
                        <input class="be-btn" type="submit" value="Submit">
                        <a class="be-btn be-btn-outline" href="<?php echo beURL('ShopFai.Order.orders') ;?>">Back</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

</be-middle>