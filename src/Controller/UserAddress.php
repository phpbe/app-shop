<?php

namespace Be\App\ShopFai\Controller;

use Be\Be;

class UserAddress extends Auth
{

    /**
     * 收货地址
     *
     * @BeMenu("用户 - 收货地址")
     * @BeRoute("/addresses")
     */
    public function addresses()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $shippingAddresses = Be::getService('App.ShopFai.UserShippingAddress')->getAddresses();
        $response->set('shippingAddresses', $shippingAddresses);

        $billingAddress = Be::getService('App.ShopFai.UserBillingAddress')->getAddress();
        $response->set('billingAddress', $billingAddress);

        $response->display();
    }

    /**
     * @BeRoute("/edit-shipping-address")
     */
    public function editShippingAddress()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $addressId = $request->get('id');
        if ($addressId) {
            $address = Be::getService('App.ShopFai.UserShippingAddress')->getAddress($addressId);
            $response->set('address', $address);
        } else {
            $response->set('address', false);
        }

        $response->display();
    }

    /**
     *
     * @BeRoute("/edit-shipping-address-save")
     */
    public function editShippingAddressSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $result = Be::getService('App.ShopFai.UserShippingAddress')->edit($request->post());
            $response->set('success', true);
            $response->set('message', ($result === 1 ? 'Add' : 'Edit') . ' shipping address success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     *
     * @BeRoute("/delete-shipping-address")
     */
    public function deleteShippingAddress()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $addressId = $request->post('id');
            Be::getService('App.ShopFai.UserShippingAddress')->delete($addressId);
            $response->set('success', true);
            $response->set('message', 'Delete shipping address success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     *
     * @BeRoute("/set-default-shipping-address")
     */
    public function setDefaultShippingAddress()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $addressId = $request->post('id');
            $result = Be::getService('App.ShopFai.UserShippingAddress')->setDefault($addressId);
            $response->set('success', true);
            $response->set('message', 'Set default shipping address success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * @BeRoute("/edit-billing-address")
     */
    public function editBillingAddress()
    {
        $response = Be::getResponse();

        $address = Be::getService('App.ShopFai.UserBillingAddress')->getAddress();
        $response->set('address', $address);

        $response->display();
    }

    /**
     * @BeRoute("/edit-billing-address-save");
     */
    public function editBillingAddressSave()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $result = Be::getService('App.ShopFai.UserBillingAddress')->edit($request->post());
            $response->set('success', true);
            $response->set('message', ($result === 1 ? 'Add' : 'Edit') . ' billing address success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     *
     * @BeRoute("/delete-billing-address")
     */
    public function deleteBillingAddress()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        try {
            $result = Be::getService('App.ShopFai.UserBillingAddress')->delete();
            $response->set('success', true);
            $response->set('message', 'Delete billing address success!');
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }


}
