<div class="user-center-nav-title">
    My Account
</div>

<div class="user-center-nav-items">
    <ul>
        <?php
        $route = \Be\Be::getRequest()->getRoute();
        $links = [
            'ShopFai.UserCenter.dashboard' => 'Dashboard',
            'ShopFai.Order.orders' => 'My Orders',
            'ShopFai.UserProductReview.reviews' => 'My Reviews',
            'ShopFai.UserFavorite.favorites' => 'Wish List',
            'ShopFai.UserAddress.addresses' => 'Address Book',
            'ShopFai.UserCenter.setting' => 'Setting',
            'ShopFai.User.logout' => 'Sign Out',
        ];
        foreach ($links as $key => $val) {
            echo '<li';
            if ($route == $key) {
                echo ' class="active"';
            }
            echo '>';
            echo '<a href="' . beUrl($key) . '">' . $val . '</a>';
            echo '</li>';
        }
        ?>
    </ul>
</div>
