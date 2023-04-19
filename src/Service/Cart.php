<?php

namespace Be\App\Shop\Service;

use Be\App\ServiceException;
use Be\Be;

class Cart
{


    /**
     * 获取购物车商品列表
     *
     * @return array(object)
     * @throws \Be\Runtime\RuntimeException
     */
    public function getProducts(): array
    {
        $my = Be::getUser();

        $cartProducts = [];

        $cache = Be::getCache();
        $key = 'Shop:Cart:' . $my->id;
        $carts = $cache->get($key);
        if (is_array($carts) && count($carts) > 0) {
            $removeCarts = [];
            foreach ($carts as $cart) {
                try {
                    $cartProducts[] = $this->loadProductDetails($cart);
                } catch (\Throwable $t) {
                    $removeCarts[] = $cart;
                    continue;
                }
            }

            if (count($removeCarts) > 0) {
                $newCarts = array_diff($carts, $removeCarts);
                if (count($newCarts) > 0) {
                    $cache->set($key, $newCarts);
                } else {
                    $cache->delete($key);
                }
            }

            if (count($cartProducts) > 0) {
                // 游客购物车保留 60 天， 注册用户 180 天， 有访问时自动延期，超时未访问网站将清除
                $expire = $my->isGuest() ? 60 * 86400 : 180 * 86400;
                $cache->setExpire($key, $expire);
            }
        }

        return $cartProducts;
    }

    /**
     * 格式化购物车商品进行结算
     *
     * @param array $cart 购物车
     * @return array(object)
     */
    public function formatProducts(array $cart, $withDetails = false): array
    {
        if (!isset($cart['product_id'])) {
            throw new ServiceException('Cart parameter (product_id) is missing!');
        }

        if (!isset($cart['product_item_id'])) {
            throw new ServiceException('Cart parameter (product_item_id) is missing!');
        }

        if (!isset($cart['quantity'])) {
            throw new ServiceException('Cart parameter (quantity) is missing!');
        }

        $productIds = $cart['product_id'];
        $productItemIds = $cart['product_item_id'];
        $quantityList = $cart['quantity'];

        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        if (!is_array($productItemIds)) {
            $productItemIds = [$productItemIds];
        }

        if (!is_array($quantityList)) {
            $quantityList = [$quantityList];
        }

        $n1 = count($productIds);
        if ($n1 === 0) {
            throw new ServiceException('Cart parameter (product_id) is missing!');
        }

        $n2 = count($productItemIds);
        if ($n2 === 0) {
            throw new ServiceException('Cart parameter (product_item_id) is missing!');
        }

        $n3 = count($quantityList);
        if ($n1 != $n2 || $n2 != $n3) {
            throw new ServiceException('Cart parameters does not match！');
        }

        $products = [];
        for ($i = 0; $i < $n1; $i++) {
            $productId = $productIds[$i];
            $productItemId = $productItemIds[$i];
            $quantity = $quantityList[$i];

            if (!is_string($productId) || strlen($productId) != 36) {
                throw new ServiceException('Cart parameter (product_id) error!');
            }

            if (!is_string($productItemId) || strlen($productItemId) != 36) {
                throw new ServiceException('Cart parameter (product_item_id) error!');
            }

            if (!is_numeric($quantity)) {
                throw new ServiceException('Cart parameter (quantity) error!');
            }

            $quantity = (int)$quantity;
            if ($quantity < 1) {
                throw new ServiceException('Cart parameter (quantity) error!');
            }

            $product = (object)[
                'product_id' => $productId,
                'product_item_id' => $productItemId,
                'quantity' => $quantity,
            ];

            if ($withDetails) {
                try {
                    $products[] = $this->loadProductDetails($product);
                } catch (\Throwable $t) {
                }
            } else {
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * 格式化购物车商品，载入详情
     *
     * @param object $cartProduct
     * @return array
     */
    public function loadProductDetails(object $cartProduct): object
    {
        $serviceProduct = Be::getService('App.Shop.Product');
        try {
            $product = $serviceProduct->getProduct($cartProduct->product_id);
        } catch (\Throwable $t) {
            throw new ServiceException('Product (#' . $cartProduct->product_id . ') does not exist！');
        }

        $productItem = null;
        foreach ($product->items as $item) {
            if ($item->id === $cartProduct->product_item_id) {
                $productItem = $item;
                break;
            }
        }

        if ($productItem === null) {
            throw new ServiceException('Product (#' . $product->name . ') item (#' . $cartProduct->product_item_id . ') does not exist！');
        }

        $imageUrl = '';
        if (count($productItem->images) > 0) {
            foreach ($productItem->images as $image) {
                if ($image->is_main === 1) {
                    $imageUrl = $image->url;
                    break;
                }
            }
        }

        if (!$imageUrl && count($product->images) > 0) {
            foreach ($product->images as $image) {
                if ($image->is_main === 1) {
                    $imageUrl = $image->url;
                    break;
                }
            }

            if (!$imageUrl) {
                $imageUrl = $product->images[0]->url;
            }
        }

        return (object)[
            'product_id' => $cartProduct->product_id,
            'product_item_id' => $cartProduct->product_item_id,
            'quantity' => $cartProduct->quantity,

            'price' => $productItem->price,
            'amount' => bcmul($productItem->price, $cartProduct->quantity, 2),

            'name' => $product->name,
            'spu' => $product->spu,

            'image' => $imageUrl,

            'sku' => $productItem->sku,
            'style' => $productItem->style,
            'weight' => $productItem->weight,
            'weight_unit' => $productItem->weight_unit,
            'stock' => $productItem->stock,

            'category_ids' => $product->category_ids,

            'url' => beUrl('Shop.Product.detail', ['id' => $product->id]),
        ];
    }

    /**
     * 添加商品
     *
     * @param string $productItemId 商品子项ID
     * @param int $quantity
     * @return int 购物车中商品数量
     */
    public function add(string $productId, string $productItemId, int $quantity = 1): int
    {
        $my = Be::getUser();
        $cache = Be::getCache();

        $key = 'Shop:Cart:' . $my->id;
        $carts = $cache->get($key);
        if (!is_array($carts)) {
            $carts = [];
        }

        $exist = false;
        foreach ($carts as $cart) {
            if ($cart->product_item_id === $productItemId) {
                $cart->quantity += $quantity;
                $exist = true;
                $quantity = $cart->quantity;
                break;
            }
        }

        if (!$exist) {
            $carts[] = (object)[
                'product_id' => $productId,
                'product_item_id' => $productItemId,
                'quantity' => $quantity,
            ];
        }

        if (count($carts) > 99) {
            throw new ServiceException('You can max add 100 items to the cart！');
        }

        // 游客购物车保留 60 天， 注册用户 180 天， 有访问时自动延期，超时未访问网站将清除
        $expire = $my->isGuest() ? 60 * 86400 : 180 * 86400;
        $cache->set($key, $carts, $expire);

        return $quantity;
    }

    /**
     * 修改购物车中的商品数量
     *
     * @param string $productId 商品ID
     * @param string $productItemId 商品子项ID
     * @param string $quantity
     * @return int 购物车中商品数量
     */
    public function change(string $productId, string $productItemId, int $quantity = 1): int
    {
        $my = Be::getUser();
        $cache = Be::getCache();

        $key = 'Shop:Cart:' . $my->id;
        $carts = $cache->get($key);
        if (!is_array($carts)) {
            $carts = [];
        }

        $newCarts = [];
        $exist = false;
        foreach ($carts as $cart) {
            if ($cart->product_item_id === $productItemId) {
                $exist = true;

                if ($quantity > 0) {
                    $cart->quantity = $quantity;
                    $newCarts[] = $cart;
                }
                break;
            }

            $newCarts[] = $cart;
        }

        if (!$exist && $quantity > 0) {
            $newCarts[] = (object)[
                'product_id' => $productId,
                'product_item_id' => $productItemId,
                'quantity' => $quantity,
            ];
        }

        // 游客购物车保留 60 天， 注册用户 180 天， 有访问时自动延期，超时未访问网站将清除
        $expire = $my->isGuest() ? 60 * 86400 : 180 * 86400;
        $cache->set($key, $newCarts, $expire);

        return $quantity;
    }

    /**
     * 删除商品
     *
     * @param string $productId 商品ID
     * @param string $productItemId 商品子项ID
     * @return bool
     */
    public function remove(string $productId, string $productItemId): bool
    {
        $my = Be::getUser();
        $cache = Be::getCache();

        $key = 'Shop:Cart:' . $my->id;
        $carts = $cache->get($key);
        if (!is_array($carts)) {
            $carts = [];
        }

        $newCarts = [];
        foreach ($carts as $cart) {
            if ($cart->product_item_id !== $productItemId) {
                $newCarts[] = $cart;
            }
        }

        if (count($newCarts) > 0) {
            // 游客购物车保留 60 天， 注册用户 180 天， 有访问时自动延期，超时未访问网站将清除
            $expire = $my->isGuest() ? 60 * 86400 : 180 * 86400;
            $cache->set($key, $newCarts, $expire);
        } else {
            $cache->delete($key);
        }

        return true;
    }

    /**
     * 删除商品
     *
     * @param array $cart 购物车
     * @return object
     * @throws \Throwable
     */
    public function checkout(array $cart): object
    {
        $my = Be::getUser();

        $cache = Be::getCache();
        $db = Be::getDb();

        if (!isset($cart['from']) || !is_string($cart['from'])) {
            $cart['from'] = 'cart';
        }

        $user_id = null;
        $email = null;
        if ($my->isGuest()) {
            $user_id = '';
            if (!isset($cart['email']) || !is_string($cart['email'])) {
                throw new ServiceException('Please enter your email!');
            }
            $email = $cart['email'];
        } else {
            $user_id = $my->id;
            $tupleUser = Be::getTuple('shop_user');
            try {
                $tupleUser->load($my->id);
            } catch (\Throwable $t) {
                throw new ServiceException('User (#' . $my->id . ') not exists!');
            }

            $email = $tupleUser->email;
        }

        if (!isset($cart['first_name']) || !is_string($cart['first_name'])) {
            $cart['first_name'] = '';
        }

        if (!isset($cart['last_name']) || !is_string($cart['last_name'])) {
            $cart['last_name'] = '';
        }

        if (!isset($cart['country_id']) || !is_string($cart['country_id'])) {
            $cart['country_id'] = '';
        }

        if (!isset($cart['state_id']) || !is_string($cart['state_id'])) {
            $cart['state_id'] = '';
        }

        if (!isset($cart['city']) || !is_string($cart['city'])) {
            $cart['city'] = '';
        }

        if (!isset($cart['address']) || !is_string($cart['address'])) {
            $cart['address'] = '';
        }

        if (!isset($cart['address2']) || !is_string($cart['address2'])) {
            $cart['address2'] = '';
        }

        if (!isset($cart['zip_code']) || !is_string($cart['zip_code'])) {
            $cart['zip_code'] = '';
        }

        if (!isset($cart['mobile']) || !is_string($cart['mobile'])) {
            $cart['mobile'] = '';
        }

        if (!isset($cart['shipping_plan_id']) || !is_string($cart['shipping_plan_id'])) {
            $cart['shipping_plan_id'] = '';
        }

        if (!isset($cart['payment_id']) || !is_string($cart['payment_id'])) {
            $cart['payment_id'] = '';
        }

        if (!isset($cart['payment_item_id']) || !is_string($cart['payment_item_id'])) {
            $cart['payment_item_id'] = '';
        }

        if ($cart['first_name'] === '') {
            throw new ServiceException('Please enter your first name!');
        }

        if ($cart['last_name'] === '') {
            throw new ServiceException('Please enter your last name!');
        }

        if ($cart['country_id'] === '') {
            throw new ServiceException('Please select your country!');
        }

        if ($cart['city'] === '') {
            throw new ServiceException('Please enter your city!');
        }

        if ($cart['address'] === '') {
            throw new ServiceException('Please enter your address!');
        }

        if ($cart['zip_code'] === '') {
            throw new ServiceException('Please enter your zip code!');
        }

        if ($cart['mobile'] === '') {
            throw new ServiceException('Please enter your mobile phone number!');
        }

        if ($cart['shipping_plan_id'] === '') {
            throw new ServiceException('Please select your shipping method!');
        }

        if ($cart['payment_id'] === '' || $cart['payment_item_id'] === '') {
            throw new ServiceException('Please select your payment method!');
        }

        $storePayment = Be::getService('App.Shop.Payment')->getStorePayment($cart['payment_id'], $cart['payment_item_id']);
        $cart['is_cod'] = $storePayment->name === 'cod' ? 1 : 0;

        $cart['country_name'] = '';
        $cart['country_code'] = '';
        $tupleRegionCountry = Be::getTuple('shop_region_country');
        try {
            $tupleRegionCountry->load($cart['country_id']);
            $cart['country_name'] = $tupleRegionCountry->name;
            $cart['country_code'] = $tupleRegionCountry->code;
        } catch (\Throwable $t) {
            throw new ServiceException('Country (#' . $cart['country_id'] . ') does not exist!');
        }

        $cart['state_name'] = '';
        if ($cart['state_id'] !== '') {
            $tupleRegionState = Be::getTuple('shop_region_state');
            try {
                $tupleRegionState->load($cart['state_id']);
                $cart['state_name'] = $tupleRegionState->name;
            } catch (\Throwable $t) {
                throw new ServiceException('State (#' . $cart['state_id'] . ') does not exist!');
            }
        }

        if (!isset($cart['products']) || !is_array($cart['products']) || count($cart['products']) === 0) {
            $cart['products'] = $this->formatProducts($cart, true);
        }

        $productTotalAmount = '0.00';
        foreach ($cart['products'] as $product) {
            $productTotalAmount = bcadd($productTotalAmount, $product->amount, 2);
        }

        $discountAmount = '0.00';
        $discount = Be::getService('App.Shop.Promotion')->getDiscount($cart);
        if ($discount !== false) {
            $discountAmount = $discount['amount'];
        }

        $totalAmount = bcsub($productTotalAmount, $discountAmount, 2);

        // 计算运费
        $serviceShipping = Be::getService('App.Shop.Shipping');
        $shippingFee = $serviceShipping->getShippingFee($cart);
        $totalAmount = bcadd($totalAmount, $shippingFee, 2);

        $serviceOrder = Be::getService('App.Shop.Order');
        $orderConfig = $serviceOrder->getConfig();

        $tupleOrder = Be::getTuple('shop_order');
        $db->startTransaction();
        try {
            $orderSn = null;
            $orderSnExist = null;
            do {
                $orderSn = $orderConfig->sn_prefix . date('ymdHis') . rand(1000, 9999);
                $sql = 'SELECT COUNT(*) FROM shop_order WHERE order_sn = ?';
                $orderSnExist = $db->getValue($sql, [$orderSn]);
            } while ($orderSnExist);

            $tupleOrder->order_sn = $orderSn;
            $tupleOrder->user_id = $user_id;
            $tupleOrder->user_token = $my->token;
            $tupleOrder->email = $email;
            $tupleOrder->product_amount = $productTotalAmount;
            $tupleOrder->discount_amount = $discountAmount;
            $tupleOrder->shipping_fee = $shippingFee;
            $tupleOrder->amount = $totalAmount;
            $tupleOrder->shipping_plan_id = $cart['shipping_plan_id'];
            $tupleOrder->payment_id = $cart['payment_id'];
            $tupleOrder->payment_item_id = $cart['payment_item_id'];
            $tupleOrder->is_paid = 0;
            $tupleOrder->is_cod = $cart['is_cod'];
            $tupleOrder->is_shipped = 0;
            $tupleOrder->status = 'pending';

            $t = time();
            $now = date('Y-m-d H:i:s', $t);

            $tupleOrder->pay_expire_time = date('Y-m-d H:i:s', $t + $orderConfig->pay_expire_time);
            $tupleOrder->is_delete = 0;
            $tupleOrder->create_time = $now;
            $tupleOrder->update_time = $now;
            $tupleOrder->insert();

            $tupleOrderShippingAddress = Be::getTuple('shop_order_shipping_address');
            $tupleOrderShippingAddress->order_id = $tupleOrder->id;
            $tupleOrderShippingAddress->first_name = $cart['first_name'];
            $tupleOrderShippingAddress->last_name = $cart['last_name'];
            $tupleOrderShippingAddress->country_id = $cart['country_id'];
            $tupleOrderShippingAddress->country_name = $cart['country_name'];
            $tupleOrderShippingAddress->country_code = $cart['country_code'];
            $tupleOrderShippingAddress->state_id = $cart['state_id'];
            $tupleOrderShippingAddress->state_name = $cart['state_name'];
            $tupleOrderShippingAddress->city = $cart['city'];
            $tupleOrderShippingAddress->address = $cart['address'];
            $tupleOrderShippingAddress->address2 = $cart['address2'];
            $tupleOrderShippingAddress->zip_code = $cart['zip_code'];
            $tupleOrderShippingAddress->mobile = $cart['mobile'];
            $tupleOrderShippingAddress->insert();

            $tupleOrderBillingAddress = Be::getTuple('shop_order_billing_address');
            $tupleOrderBillingAddress->order_id = $tupleOrder->id;
            $tupleOrderBillingAddress->first_name = $cart['first_name'];
            $tupleOrderBillingAddress->last_name = $cart['first_name'];
            $tupleOrderBillingAddress->country_id = $cart['country_id'];
            $tupleOrderBillingAddress->country_name = $cart['country_name'];
            $tupleOrderBillingAddress->country_code = $cart['country_code'];
            $tupleOrderBillingAddress->state_id = $cart['state_id'];
            $tupleOrderBillingAddress->state_name = $cart['state_name'];
            $tupleOrderBillingAddress->city = $cart['city'];
            $tupleOrderBillingAddress->address = $cart['address'];
            $tupleOrderBillingAddress->address2 = $cart['address2'];
            $tupleOrderBillingAddress->zip_code = $cart['zip_code'];
            $tupleOrderBillingAddress->mobile = $cart['mobile'];
            $tupleOrderBillingAddress->insert();

            foreach ($cart['products'] as $product) {
                $tupleOrderProduct = Be::getTuple('shop_order_product');
                $tupleOrderProduct->order_id = $tupleOrder->id;
                $tupleOrderProduct->user_id = $my->id;
                $tupleOrderProduct->product_id = $product->product_id;
                $tupleOrderProduct->product_item_id = $product->product_item_id;
                $tupleOrderProduct->quantity = $product->quantity;
                $tupleOrderProduct->price = $product->price;
                $tupleOrderProduct->amount = $product->amount;
                $tupleOrderProduct->name = $product->name;
                $tupleOrderProduct->spu = $product->spu;
                $tupleOrderProduct->image = $product->image;
                $tupleOrderProduct->sku = $product->sku;
                $tupleOrderProduct->style = $product->style;
                $tupleOrderProduct->weight = $product->weight;
                $tupleOrderProduct->weight_unit = $product->weight_unit;
                $tupleOrderProduct->insert();
            }

            if ($discount !== false) {
                $tupleOrderPromotion = Be::getTuple('shop_order_promotion');
                $tupleOrderPromotion->order_id = $tupleOrder->id;
                $tupleOrderPromotion->promotion_type = $discount['promotion_type'];
                $tupleOrderPromotion->promotion_id = $discount['promotion_id'];
                $tupleOrderPromotion->discount_amount = $discount['amount'];

                $servicePromotion = Be::getService('App.Shop.' . \Be\Util\Str\CaseConverter::underline2CamelUcFirst($discount['promotion_type']));
                $promotionDetails = $servicePromotion->getDetails($discount['promotion_id']);
                $tupleOrderPromotion->promotion_details = json_encode($promotionDetails);

                $tupleOrderPromotion->insert();
            }

            $db->commit();

            // 清除购物车中的商品
            if ($cart['from'] === 'cart') {

                $key = 'Shop:Cart:' . $my->id;
                $carts = $cache->get($key);
                if (!is_array($carts)) {
                    $carts = [];
                }

                $newCarts = [];
                foreach ($carts as $cart) {
                    $exist = false;
                    foreach ($cart['products'] as $product) {
                        if ($cart->product_item_id === $product->product_item_id) {
                            $exist = true;
                            break;
                        }
                    }

                    if (!$exist) {
                        $newCarts[] = $cart;
                    }
                }

                if (count($newCarts) > 0) {
                    // 游客购物车保留 60 天， 注册用户 180 天， 有访问时自动延期，超时未访问网站将清除
                    $expire = $my->isGuest() ? 60 * 86400 : 180 * 86400;
                    $cache->set($key, $newCarts, $expire);
                } else {
                    $cache->delete($key);
                }
            }
        } catch (\Throwable $t) {
            $db->rollback();
            $logId = Be::getLog()->error($t);
            throw new ServiceException('Check out exception (log id: ' . $logId . ')');
        }

        return $tupleOrder->toObject();
    }

}
