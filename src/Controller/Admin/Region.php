<?php

namespace Be\App\Shop\Controller\Admin;

use Be\App\ControllerException;
use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 区域
 */
class Region extends Auth
{

    /**
     * 获取国家列表
     *
     * @BePermission("*")
     */
    public function getCountryKeyValues()
    {
        $response = Be::getResponse();
        try {
            $keyValues = Be::getService('App.Shop.Admin.Region')->getCountryKeyValues();
            $response->set('success', true);
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
     * @BePermission("*")
     */
    public function getStates()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();
        try {
            $countryCode = $request->json('country_code');
            if (!$countryCode) {
                throw new ControllerException('参数（country_code）缺失');
            }
            $states = Be::getService('App.Shop.Admin.Region')->getStates($countryCode);
            $response->set('success', true);
            $response->set('states', $states);
            $response->json();
        } catch (\Throwable $t) {
            $response->set('success', false);
            $response->set('message', $t->getMessage());
            $response->json();
        }
    }

}
