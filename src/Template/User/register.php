<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/js/user/register.js"></script>
    <script>
        const userRegister_registerSaveUrl = "<?php echo beUrl('Shop.User.registerSave'); ?>";
    </script>
</be-head>


<be-page-content>
    <div class="be-row">
        <div class="be-col-0 be-md-col-2 be-lg-col-4 be-xl-col-6">
        </div>
        <div class="be-col-24 be-md-col-20 be-lg-col-16 be-xl-col-12">

            <h4 class="be-h4">Create Account</h4>

            <form id="user-register-form">

                <div class="be-floating be-mt-200">
                    <input type="text" name="first_name" class="be-input" placeholder="First Name" />
                    <label class="be-floating-label">First Name <span class="be-c-red">*</span></label>
                </div>

                <div class="be-floating be-mt-150">
                    <input type="text" name="last_name" class="be-input"  placeholder="Last Name" />
                    <label class="be-floating-label">Last Name <span class="be-c-red">*</span></label>
                </div>

                <div class="be-floating be-mt-150">
                    <input type="text" name="email" class="be-input" placeholder="Email">
                    <label class="be-floating-label">Email <span class="be-c-red">*</span></label>
                </div>

                <div class="be-floating be-mt-150">
                    <input type="password" name="password" id="password" class="be-input" placeholder="Create Your Password" />
                    <label class="be-floating-label">Password <span class="be-c-red">*</span></label>
                </div>

                <div class="be-floating be-mt-150">
                    <input type="password" name="password2" class="be-input" placeholder="Confirm Password">
                    <label class="be-floating-label">Confirm Password <span class="be-c-red">*</span></label>
                </div>

                <div class="be-mt-150 be-row">
                    <div class="be-col-24 be-md-col-12">
                        <button type="submit" class="be-btn be-btn-major be-btn-lg be-w-100">Create</button>
                    </div>
                </div>

                <div class="be-mt-150 be-ta-right">Already have an account? <a href="<?php echo beUrl('Shop.User.login'); ?>">Click here to log in</a></div>

            </form>
        </div>
    </div>
</be-page-content>