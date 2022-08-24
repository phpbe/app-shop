<?php

namespace Be\App\ShopFai\Controller;

use Be\App\ControllerException;
use Be\Be;

/**
 * Class Shipping
 * @package Be\App\ShopFai\Controller
 */
class Shipping extends Base
{

    /**
     * 获取国家列表
     *
     * @BeRoute("/shipping/get-country-key-values")
     */
    public function getCountryKeyValues()
    {
        $response = Be::getResponse();
        try {
            $keyValues = Be::getService('App.ShopFai.Region')->getCountryKeyValues();
            $response->set('success', true);
            $response->set('message', 'Get country key values data success!');
            $response->set('countryKeyValues', $keyValues);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 获取州/省份列表
     *
     * @BeRoute("/shipping/get-state-key-values")
     */
    public function getStateKeyValues()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $countryId = $request->post('country_id');
            if (!$countryId) {
                throw new ControllerException('Parameter(country_id) missed!');
            }
            $keyValues = Be::getService('App.ShopFai.Shipping')->getStateIdNameKeyValues($countryId);
            $response->set('success', true);
            $response->set('message', 'Get state key values data success!');
            $response->set('stateKeyValues', $keyValues);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

    /**
     * 获取符合条件的物流方式
     *
     * @BeRoute("/shipping/get-shipping-plans")
     */
    public function getShippingPlans()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $shippingPlans = Be::getService('App.ShopFai.Shipping')->getShippingPlans($request->post());

            $response->set('success', true);
            $response->set('message', 'Get shipping plans data success!');
            $response->set('shippingPlans', $shippingPlans);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }




}
