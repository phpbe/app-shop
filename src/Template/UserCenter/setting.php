<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
    <script src="<?php echo $wwwUrl; ?>/js/user-center/setting.js"></script>
</be-head>



<be-page-content>
    <div class="be-d-block be-d-lg-none">
        <h4 class="be-h4">
            <a href="<?php echo beURL('ShopFai.UserCenter.dashboard') ;?>"><i class="user-center-back"></i></a>
            Account Settings
        </h4>
    </div>
    <div class="be-d-none be-d-lg-block">
        <h4 class="be-h4">Account Settings</h4>
    </div>

    <div class="be-row be-mt-100">
        <div class="be-col-24 be-col-md-8 be-mt-150">
            Update your Profile
        </div>
        <div class="be-col-24 be-col-md-16">

            <form id="user-center-setting-Update-profile-form">
                <div class="be-row">
                    <div class="be-col-24 be-col-md-11 be-mt-150">
                        <div class="be-floating">
                            <input type="text" name="first_name" class="be-input" placeholder="First Name" value="<?php echo $this->profile->first_name; ?>">
                            <label class="be-floating-label">First Name</label>
                        </div>
                    </div>
                    <div class="be-col-0 be-col-md-2"></div>
                    <div class="be-col-24 be-col-md-11 be-mt-150">
                        <div class="be-floating">
                            <input type="text" name="last_name" class="be-input"  placeholder="Last Name" value="<?php echo $this->profile->last_name; ?>">
                            <label class="be-floating-label">Last Name</label>
                        </div>
                    </div>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-24 be-col-md-12 be-col-lg-6">
                        <input type="submit" class="be-btn be-btn-lg be-w-100" value="Save">
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div class="be-row be-mt-200">
        <div class="be-col-24 be-col-md-8 be-mt-150">
            Change your Email address
        </div>
        <div class="be-col-24 be-col-md-16">
            <form id="user-center-setting-change-email-form">
                <div class="be-floating be-mt-150">
                    <input type="password" name="password" class="be-input" placeholder="Existing Password">
                    <label class="be-floating-label">Existing Password</label>
                </div>
                <div class="be-floating be-mt-150">
                    <input type="text" name="email" class="be-input" placeholder="New E-mail Address" value="<?php echo $this->profile->email; ?>">
                    <label class="be-floating-label">New E-mail Address</label>
                </div>
                <div class="be-mt-150 be-row">
                    <div class="be-col-24 be-col-md-12 be-col-lg-6">
                        <input type="submit" class="be-btn be-btn-lg be-w-100" value="Save">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="be-row be-mt-200">
        <div class="be-col-24 be-col-md-8 be-mt-150">
            Change your password
        </div>
        <div class="be-col-24 be-col-md-16">
            <form id="user-center-setting-change-password-form">
                <div class="be-floating be-mt-150">
                    <input type="password" name="password" class="be-input" placeholder="Existing Password">
                    <label class="be-floating-label">Existing Password</label>
                </div>
                <div class="be-floating be-mt-150">
                    <input type="password" name="new_password" id="new_password" class="be-input" placeholder="New Password">
                    <label class="be-floating-label">New Password</label>
                </div>
                <div class="be-floating be-mt-150">
                    <input type="password" name="new_password2" class="be-input" placeholder="Re-enter Password">
                    <label class="be-floating-label">Re-enter Password</label>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-24 be-col-md-12 be-col-lg-6">
                        <input type="submit" class="be-btn be-btn-lg be-w-100" value="Save">
                    </div>
                </div>

                <div class="be-d-block be-d-lg-none">
                    <div class="be-mt-150 be-row">
                        <div class="be-col-24 be-col-md-12 be-col-lg-6">
                            <a href="<?php echo beURL('ShopFai.UserCenter.dashboard') ;?>" class="be-btn be-btn-outline be-btn-lg be-w-100">Back</a>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</be-page-content>