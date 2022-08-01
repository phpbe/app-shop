<?php

namespace Be\App\ShopFai\Service\Admin;

use Be\App\ServiceException;
use Be\Be;

/**
 * Class Shipping
 *
 * @package Be\App\ShopFai\Admin\Service
 */
class Shipping
{

    /**
     * 编辑运费
     *
     * @param array $data 运费数据
     * @return bool
     * @throws \Throwable
     */
    public function edit($data)
    {
        $db = Be::getDb();

        $isNew = true;
        $shippingId = null;
        if (isset($data['id']) && $data['id'] !== '') {
            $isNew = false;
            $shippingId = $data['id'];
        }

        $tupleShipping = Be::getTuple('shopfai_shipping');
        if (!$isNew) {
            try {
                $tupleShipping->load($shippingId);
            } catch (\Throwable $t) {
                throw new ServiceException('区域方案（# ' . $shippingId . '）不存在！');
            }
        }

        $name = $data['name'] ?? '';
        if (!$name) {
            throw new ServiceException('区域方案名称未填写！');
        }

        if (!isset($data['regions']) || !is_array($data['regions']) || count($data['regions']) === 0) {
            throw new ServiceException('未选择有效的配送区域！');
        }
        $regions = $data['regions'];

        $countryCodes = [];
        foreach ($regions as &$region) {
            if (!isset($region['id'])) {
                $region['id'] = '';
            }

            if (!isset($region['country_code']) || !$region['country_code']) {
                throw new ServiceException('未选择有效的配送区域！');
            }
            $countryCodes[] = $region['country_code'];

            if (!isset($region['assign_state']) || $region['assign_state'] !== '1') {
                $region['assign_state'] = '0';
            }

            if ($region['assign_state'] === '1') {
                if (!isset($region['states']) || !is_array($region['states']) || count($region['states']) === 0) {
                    throw new ServiceException('国家（' . $region['country_code'] . '）下的州/省数据缺失！');
                }

                $sql = 'SELECT `name` FROM `shopfai_region_state` WHERE country_code=\'' . $region['country_code'] . '\'';
                $availableStateNames = $db->getValues($sql);

                foreach ($region['states'] as &$state) {
                    if (!isset($state['id'])) {
                        $state['id'] = '';
                    }

                    if (!in_array($state['state_name'], $availableStateNames)) {
                        throw new ServiceException('配送区域（国家' . $region['country_code'] . '下的州/省' . $state['state_name'] . '）非法！');
                    }
                }
                unset($state);

                // 选中了全部
                if (count($region['states']) === count($availableStateNames)) {
                    $region['assign_state'] = '0';
                    $region['states'] = [];
                }
            }
        }
        unset($region);

        $sql = 'SELECT code FROM `shopfai_region_country` WHERE code IN(\'' . implode('\',\'', $countryCodes) . '\')';
        $availableCountryCodes = $db->getValues($sql);
        if (count($availableCountryCodes) != count($countryCodes)) {
            throw new ServiceException('配送区域（' . implode('、', array_diff($countryCodes, $availableCountryCodes)) . '）非法！');
        }

        $params = [];
        $sql = 'SELECT country_code FROM `shopfai_shipping_region` WHERE country_code IN (\'' . implode('\',\'', $countryCodes) . '\')';
        if (!$isNew) {
            $sql .= ' AND shipping_id!=?';
            $params[] = $shippingId;
        }
        $existCountryCodes = $db->getValues($sql, $params);
        if (count($existCountryCodes) > 0) {
            throw new ServiceException('配送区域（' . implode('、', $existCountryCodes) . '）重复！');
        }

        if (!isset($data['plans']) || !is_array($data['plans']) || count($data['plans']) === 0) {
            throw new ServiceException('未配置有效的运费方案！');
        }
        $plans = $data['plans'];
        $i = 1;
        foreach ($plans as &$plan) {
            if (!isset($plan['id'])) {
                $plan['id'] = '';
            }

            if (!isset($plan['name']) || !$plan['name']) {
                throw new ServiceException('第' . $i . '个运费方案名称缺失！');
            }

            if (!isset($plan['description'])) {
                $plan['description'] = '';
            }

            if (!isset($plan['limit']) || $plan['limit'] !== '1') {
                $plan['limit'] = '0';
            }

            if (!isset($plan['limit_type']) || !in_array($plan['limit_type'], ['amount', 'quantity', 'weight'])) {
                $plan['limit_type'] = 'amount';
            }

            switch ($plan['limit_type']) {
                case 'amount':
                    if (!isset($plan['limit_amount_from']) || !is_numeric($plan['limit_amount_from']) || $plan['limit_amount_from'] < 0) {
                        $plan['limit_amount_from'] = '0.00';
                    }
                    $plan['limit_amount_from'] = number_format($plan['limit_amount_from'], 2, '.', '');

                    if (!isset($plan['limit_amount_to']) || !is_numeric($plan['limit_amount_to']) || $plan['limit_amount_to'] < 0) {
                        $plan['limit_amount_to'] = '-1.00';
                    }
                    $plan['limit_amount_to'] = number_format($plan['limit_amount_to'], 2, '.', '');

                    $plan['limit_quantity_from'] = '0';
                    $plan['limit_quantity_to'] = '-1';
                    $plan['limit_weight_from'] = '0.00';
                    $plan['limit_weight_to'] = '-1.00';
                    $plan['limit_weight_unit'] = 'g';
                    break;
                case 'quantity':
                    if (!isset($plan['limit_quantity_from']) || !is_numeric($plan['limit_quantity_from']) || $plan['limit_quantity_from'] < 0) {
                        $plan['limit_quantity_from'] = '0';
                    }
                    $plan['limit_quantity_from'] = number_format($plan['limit_quantity_from'], 0, '.', '');

                    if (!isset($plan['limit_quantity_to']) || !is_numeric($plan['limit_quantity_to']) || $plan['limit_quantity_to'] < 0) {
                        $plan['limit_quantity_to'] = '-1';
                    }
                    $plan['limit_quantity_to'] = number_format($plan['limit_quantity_to'], 0, '.', '');

                    $plan['limit_amount_from'] = '0.00';
                    $plan['limit_amount_to'] = '-1.00';
                    $plan['limit_weight_from'] = '0.00';
                    $plan['limit_weight_to'] = '-1.00';
                    $plan['limit_weight_unit'] = 'g';
                    break;
                case 'weight':
                    if (!isset($plan['limit_weight_from']) || !is_numeric($plan['limit_weight_from']) || $plan['limit_weight_from'] < 0) {
                        $plan['limit_weight_from'] = '0.00';
                    }
                    $plan['limit_weight_from'] = number_format($plan['limit_weight_from'], 2, '.', '');

                    if (!isset($plan['limit_weight_to']) || !is_numeric($plan['limit_weight_to']) || $plan['limit_weight_to'] < 0) {
                        $plan['limit_weight_to'] = '-1.00';
                    }
                    $plan['limit_weight_to'] = number_format($plan['limit_weight_to'], 2, '.', '');

                    if (!isset($plan['limit_weight_unit']) || !in_array($plan['limit_weight_unit'], ['kg', 'g', 'lb', 'oz'])) {
                        $plan['limit_weight_unit'] = 'g';
                    }

                    $plan['limit_amount_from'] = '0.00';
                    $plan['limit_amount_to'] = '-1.00';
                    $plan['limit_quantity_from'] = '0';
                    $plan['limit_quantity_to'] = '-1';
                    break;
            }

            if (!isset($plan['cod']) || $plan['cod'] !== '1') {
                $plan['cod'] = '0';
            }

            if (!isset($plan['shipping_fee_type']) || !in_array($plan['shipping_fee_type'], ['fixed', 'weight'])) {
                $plan['shipping_fee_type'] = 'fixed';
            }

            switch ($plan['shipping_fee_type']) {
                case 'fixed':
                    if (!isset($plan['shipping_fee_fixed']) || !is_numeric($plan['shipping_fee_fixed']) || $plan['shipping_fee_fixed'] < 0) {
                        $plan['shipping_fee_fixed'] = '0.00';
                    }
                    $plan['shipping_fee_fixed'] = number_format($plan['shipping_fee_fixed'], 2, '.', '');

                    $plan['shipping_fee_first_weight_price'] = '0.00';
                    $plan['shipping_fee_first_weight'] = '0.00';
                    $plan['shipping_fee_first_weight_unit'] = 'g';
                    $plan['shipping_fee_additional_weight_price'] = '0.00';
                    $plan['shipping_fee_additional_weight'] = '0.00';
                    $plan['shipping_fee_additional_weight_unit'] = 'g';
                    break;
                case 'weight':
                    $plan['shipping_fee_fixed'] = '0.00';

                    if (!isset($plan['shipping_fee_first_weight_price']) || !is_numeric($plan['shipping_fee_first_weight_price']) || $plan['shipping_fee_first_weight_price'] < 0) {
                        $plan['shipping_fee_first_weight_price'] = '0.00';
                    }
                    $plan['shipping_fee_first_weight_price'] = number_format($plan['shipping_fee_first_weight_price'], 2, '.', '');

                    if (!isset($plan['shipping_fee_first_weight']) || !is_numeric($plan['shipping_fee_first_weight']) || $plan['shipping_fee_first_weight'] < 0) {
                        $plan['shipping_fee_first_weight'] = '0.00';
                    }
                    $plan['shipping_fee_first_weight'] = number_format($plan['shipping_fee_first_weight'], 2, '.', '');

                    if (!isset($plan['shipping_fee_first_weight_unit']) || !in_array($plan['shipping_fee_first_weight_unit'], ['kg', 'g', 'lb', 'oz'])) {
                        $plan['shipping_fee_first_weight_unit'] = 'g';
                    }

                    if (!isset($plan['shipping_fee_additional_weight_price']) || !is_numeric($plan['shipping_fee_additional_weight_price']) || $plan['shipping_fee_additional_weight_price'] < 0) {
                        $plan['shipping_fee_additional_weight_price'] = '0.00';
                    }
                    $plan['shipping_fee_additional_weight_price'] = number_format($plan['shipping_fee_additional_weight_price'], 2, '.', '');

                    if (!isset($plan['shipping_fee_additional_weight']) || !is_numeric($plan['shipping_fee_additional_weight']) || $plan['shipping_fee_additional_weight'] < 0) {
                        $plan['shipping_fee_additional_weight'] = '0.00';
                    }
                    $plan['shipping_fee_additional_weight'] = number_format($plan['shipping_fee_additional_weight'], 2, '.', '');

                    if (!isset($plan['shipping_fee_additional_weight_unit']) || !in_array($plan['shipping_fee_additional_weight_unit'], ['kg', 'g', 'lb', 'oz'])) {
                        $plan['shipping_fee_additional_weight_unit'] = 'g';
                    }
                    break;
            }
            $i++;
        }
        unset($plan);

        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $tupleShipping->name = $name;
            $tupleShipping->update_time = $now;
            if ($isNew) {
                $tupleShipping->create_time = $now;
                $tupleShipping->insert();
            } else {
                $tupleShipping->update();
            }


            if ($isNew) {
                foreach ($regions as $region) {
                    $tupleShippingRegion = Be::getTuple('shopfai_shipping_region');
                    $tupleShippingRegion->shipping_id = $tupleShipping->id;
                    $tupleShippingRegion->country_id = $region['country_id'];
                    $tupleShippingRegion->country_code = $region['country_code'];
                    $tupleShippingRegion->assign_state = $region['assign_state'];
                    $tupleShippingRegion->create_time = $now;
                    $tupleShippingRegion->update_time = $now;
                    $tupleShippingRegion->insert();

                    if ($region['assign_state'] === '1') {
                        foreach ($region['states'] as $state) {
                            $tupleShippingRegionState = Be::getTuple('shopfai_shipping_region_state');
                            $tupleShippingRegionState->shipping_id = $tupleShipping->id;
                            $tupleShippingRegionState->shipping_region_id = $tupleShippingRegion->id;
                            $tupleShippingRegionState->state_id = $state['state_id'];
                            $tupleShippingRegionState->state_name = $state['state_name'];
                            $tupleShippingRegionState->create_time = $now;
                            $tupleShippingRegionState->update_time = $now;
                            $tupleShippingRegionState->insert();
                        }
                    }
                }
            } else {
                $keepIds = [];
                foreach ($regions as $region) {
                    if (isset($region['id']) && $region['id'] !== '') {
                        $keepIds[] = $region['id'];
                    }
                }

                if (count($keepIds) > 0) {
                    Be::getTable('shopfai_shipping_region')
                        ->where('shipping_id', $tupleShipping->id)
                        ->where('id', 'NOT IN', $keepIds)
                        ->delete();

                    Be::getTable('shopfai_shipping_region_state')
                        ->where('shipping_id', $tupleShipping->id)
                        ->where('shipping_region_id', 'NOT IN', $keepIds)
                        ->delete();
                } else {
                    Be::getTable('shopfai_shipping_region')
                        ->where('shipping_id', $tupleShipping->id)
                        ->delete();

                    Be::getTable('shopfai_shipping_region_state')
                        ->where('shipping_id', $tupleShipping->id)
                        ->delete();
                }

                foreach ($regions as $region) {
                    $isNewRegion = true;
                    if (isset($region['id']) && $region['id'] !== '') {
                        $isNewRegion = false;
                    }

                    $tupleShippingRegion = Be::getTuple('shopfai_shipping_region');

                    if (!$isNewRegion) {
                        try {
                            $tupleShippingRegion->load($region['id']);
                        } catch (\Throwable $t) {
                            throw new ServiceException('区域方案（' . $tupleShipping->name . '）下的配送区域（# ' . $region['id'] . ' ' . $region['country_code'] . '）不存在！');
                        }
                    } else {
                        $tupleShippingRegion->shipping_id = $tupleShipping->id;
                    }

                    $tupleShippingRegion->country_id = $region['country_id'];
                    $tupleShippingRegion->country_code = $region['country_code'];
                    $tupleShippingRegion->assign_state = $region['assign_state'];

                    $tupleShippingRegion->update_time = $now;

                    if ($isNewRegion) {
                        $tupleShippingRegion->create_time = $now;
                        $tupleShippingRegion->insert();
                    } else {
                        $tupleShippingRegion->update();
                    }

                    if ($region['assign_state'] === '1') {

                        if ($isNewRegion) {

                            // 新增的区域
                            foreach ($region['states'] as $state) {
                                $tupleShippingRegionState = Be::getTuple('shopfai_shipping_region_state');
                                $tupleShippingRegionState->shipping_id = $tupleShipping->id;
                                $tupleShippingRegionState->shipping_region_id = $tupleShippingRegion->id;
                                $tupleShippingRegionState->state_id = $state['state_id'];
                                $tupleShippingRegionState->state_name = $state['state_name'];
                                $tupleShippingRegionState->create_time = $now;
                                $tupleShippingRegionState->update_time = $now;
                                $tupleShippingRegionState->insert();
                            }

                        } else {

                            $keepIds = [];
                            foreach ($region['states'] as $state) {
                                if (isset($state['id']) && $state['id'] !== '') {
                                    $keepIds[] = $state['id'];
                                }
                            }

                            if (count($keepIds) > 0) {
                                Be::getTable('shopfai_shipping_region_state')
                                    ->where('shipping_id', $tupleShipping->id)
                                    ->where('shipping_region_id', $tupleShippingRegion->id)
                                    ->where('id', 'NOT IN', $keepIds)
                                    ->delete();
                            } else {
                                Be::getTable('shopfai_shipping_region_state')
                                    ->where('shipping_id', $tupleShipping->id)
                                    ->where('shipping_region_id', $tupleShippingRegion->id)
                                    ->delete();
                            }

                            foreach ($region['states'] as $state) {
                                $isNewRegionState = true;
                                if (isset($state['id']) && $state['id'] !== '') {
                                    $isNewRegionState = false;
                                }

                                $tupleShippingRegionState = Be::getTuple('shopfai_shipping_region_state');

                                if (!$isNewRegionState) {
                                    try {
                                        $tupleShippingRegionState->load($state['id']);
                                    } catch (\Throwable $t) {
                                        throw new ServiceException('区域方案（' . $tupleShipping->name . '）下的配送区域（# ' . $tupleShippingRegion->country_code . '）下的州/省（# ' . $state['id'] . ' ' . $state['state_name'] . '）不存在！');
                                    }

                                    if ($tupleShippingRegionState->shipping_id !== $tupleShipping->id || $tupleShippingRegionState->shipping_region_id !== $tupleShippingRegion->id) {
                                        throw new ServiceException('区域方案（' . $tupleShipping->name . '）下的配送区域（# ' . $tupleShippingRegion->country_code . '）下的州/省（# ' . $state['id'] . ' ' . $state['state_name'] . '）不存在！');
                                    }
                                } else {
                                    $tupleShippingRegionState->shipping_id = $tupleShipping->id;
                                    $tupleShippingRegionState->shipping_region_id = $tupleShippingRegion->id;
                                }

                                $tupleShippingRegionState->state_name = $state['state_name'];

                                $tupleShippingRegionState->update_time = $now;
                                if ($isNewRegionState) {
                                    $tupleShippingRegionState->create_time = $now;
                                    $tupleShippingRegionState->insert();
                                } else {
                                    $tupleShippingRegionState->update();
                                }
                            }
                        }
                    }
                }
            }


            if ($isNew) {
                foreach ($plans as $plan) {
                    $tupleShippingPlan = Be::getTuple('shopfai_shipping_plan');
                    $tupleShippingPlan->shipping_id = $tupleShipping->id;
                    $tupleShippingPlan->name = $plan['name'];
                    $tupleShippingPlan->description = $plan['description'];
                    $tupleShippingPlan->limit = $plan['limit'];
                    $tupleShippingPlan->limit_type = $plan['limit_type'] ?? 'amount';
                    $tupleShippingPlan->limit_amount_from = $plan['limit_amount_from'];
                    $tupleShippingPlan->limit_amount_to = $plan['limit_amount_to'];
                    $tupleShippingPlan->limit_quantity_from = $plan['limit_quantity_from'];
                    $tupleShippingPlan->limit_quantity_to = $plan['limit_quantity_to'];
                    $tupleShippingPlan->limit_weight_from = $plan['limit_weight_from'];
                    $tupleShippingPlan->limit_weight_to = $plan['limit_weight_to'];
                    $tupleShippingPlan->limit_weight_unit = $plan['limit_weight_unit'];
                    $tupleShippingPlan->cod = $plan['cod'];
                    $tupleShippingPlan->shipping_fee_type = $plan['shipping_fee_type'];
                    $tupleShippingPlan->shipping_fee_fixed = $plan['shipping_fee_fixed'];
                    $tupleShippingPlan->shipping_fee_first_weight_price = $plan['shipping_fee_first_weight_price'];
                    $tupleShippingPlan->shipping_fee_first_weight = $plan['shipping_fee_first_weight'];
                    $tupleShippingPlan->shipping_fee_first_weight_unit = $plan['shipping_fee_first_weight_unit'];
                    $tupleShippingPlan->shipping_fee_additional_weight_price = $plan['shipping_fee_additional_weight_price'];
                    $tupleShippingPlan->shipping_fee_additional_weight = $plan['shipping_fee_additional_weight'];
                    $tupleShippingPlan->shipping_fee_additional_weight_unit = $plan['shipping_fee_additional_weight_unit'];
                    $tupleShippingPlan->create_time = $now;
                    $tupleShippingPlan->update_time = $now;
                    $tupleShippingPlan->insert();
                }
            } else {
                $keepIds = [];
                foreach ($plans as $plan) {
                    if (isset($plan['id']) && $plan['id'] !== '') {
                        $keepIds[] = $plan['id'];
                    }
                }

                if (count($keepIds) > 0) {
                    Be::getTable('shopfai_shipping_plan')
                        ->where('shipping_id', $tupleShipping->id)
                        ->where('id', 'NOT IN', $keepIds)
                        ->delete();
                } else {
                    Be::getTable('shopfai_shipping_plan')
                        ->where('shipping_id', $tupleShipping->id)
                        ->delete();
                }

                foreach ($plans as $plan) {
                    $isNewPlan = true;
                    if (isset($plan['id']) && $plan['id'] !== '') {
                        $isNewPlan = false;
                    }

                    $tupleShippingPlan = Be::getTuple('shopfai_shipping_plan');

                    if (!$isNewPlan) {
                        try {
                            $tupleShippingPlan->load($plan['id']);
                        } catch (\Throwable $t) {
                            throw new ServiceException('区域方案（' . $tupleShipping->name . '）下的运费方案（# ' . $plan['id'] . ' ' . $plan['name'] . '）不存在！');
                        }

                        if ($tupleShippingRegion->shipping_id !== $tupleShipping->id) {
                            throw new ServiceException('区域方案（' . $tupleShipping->name . '）下的运费方案（# ' . $plan['id'] . ' ' . $plan['name'] . '）不存在！');
                        }
                    } else {
                        $tupleShippingPlan->shipping_id = $tupleShipping->id;
                    }

                    $tupleShippingPlan->name = $plan['name'];
                    $tupleShippingPlan->description = $plan['description'];
                    $tupleShippingPlan->limit = $plan['limit'];
                    $tupleShippingPlan->limit_type = $plan['limit_type'] ?? 'amount';
                    $tupleShippingPlan->limit_amount_from = $plan['limit_amount_from'];
                    $tupleShippingPlan->limit_amount_to = $plan['limit_amount_to'];
                    $tupleShippingPlan->limit_quantity_from = $plan['limit_quantity_from'];
                    $tupleShippingPlan->limit_quantity_to = $plan['limit_quantity_to'];
                    $tupleShippingPlan->limit_weight_from = $plan['limit_weight_from'];
                    $tupleShippingPlan->limit_weight_to = $plan['limit_weight_to'];
                    $tupleShippingPlan->limit_weight_unit = $plan['limit_weight_unit'];
                    $tupleShippingPlan->cod = $plan['cod'];
                    $tupleShippingPlan->shipping_fee_type = $plan['shipping_fee_type'];
                    $tupleShippingPlan->shipping_fee_fixed = $plan['shipping_fee_fixed'];
                    $tupleShippingPlan->shipping_fee_first_weight_price = $plan['shipping_fee_first_weight_price'];
                    $tupleShippingPlan->shipping_fee_first_weight = $plan['shipping_fee_first_weight'];
                    $tupleShippingPlan->shipping_fee_first_weight_unit = $plan['shipping_fee_first_weight_unit'];
                    $tupleShippingPlan->shipping_fee_additional_weight_price = $plan['shipping_fee_additional_weight_price'];
                    $tupleShippingPlan->shipping_fee_additional_weight = $plan['shipping_fee_additional_weight'];
                    $tupleShippingPlan->shipping_fee_additional_weight_unit = $plan['shipping_fee_additional_weight_unit'];

                    $tupleShippingPlan->update_time = $now;

                    if ($isNewPlan) {
                        $tupleShippingPlan->create_time = $now;
                        $tupleShippingPlan->insert();
                    } else {
                        $tupleShippingPlan->update();
                    }
                }
            }

            Be::getService('App.ShopFai.Admin.Store')->setUp(2);

            $db->commit();

        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException(($isNew ? '添加' : '编辑') . '区域方案发生异常！');
        }

        return true;
    }

    /**
     * 获取运费方案列表
     *
     * @return array
     */
    public function getShippingList()
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM `shopfai_shipping` ORDER BY `create_time` ASC';
        $shippingList = $db->getObjects($sql);
        foreach ($shippingList as $shipping) {

            $sql = 'SELECT country_code FROM `shopfai_shipping_region` WHERE shipping_id=?';
            $countryCodes = $db->getValues($sql, [$shipping->id]);
            $count = count($countryCodes);
            if ($count > 3) {
                $countryCodes = array_slice($countryCodes, 0, 3);
            }

            $sql = 'SELECT `name_cn` FROM `shopfai_region_country` WHERE code IN(\'' . implode('\',\'', $countryCodes) . '\')';
            $countryNames = $db->getValues($sql);

            $regionDescription = null;
            if ($count > 3) {
                $regionDescription = '包含' . implode('、', $countryNames) . '等' . $count . '个国家/地区';
            } else {
                $regionDescription = '包含' . implode('、', $countryNames);
            }
            $shipping->region_description = $regionDescription;


            $sql = 'SELECT * FROM `shopfai_shipping_plan` WHERE shipping_id=?';
            $plans = $db->getObjects($sql, [$shipping->id]);
            $shipping->plans = $plans;
        }

        return $shippingList;
    }

    /**
     * 获取运费方案
     *
     * @param string $shippingId 运费方案ID
     * @return object
     */
    public function getShipping($shippingId)
    {
        $db = Be::getDb();
        $sql = 'SELECT * FROM `shopfai_shipping` WHERE id=?';
        $shipping = $db->getObject($sql, [$shippingId]);

        if (!$shipping) {
            throw new ServiceException('运费方案（# ' . $shippingId . '）不存在！');
        }

        $sql = 'SELECT * FROM `shopfai_shipping_region` WHERE shipping_id=?';
        $regions = $db->getObjects($sql, [$shipping->id]);
        foreach ($regions as $region) {

            $sql = 'SELECT * FROM `shopfai_region_country` WHERE code=?';
            $country = $db->getObject($sql, [$region->country_code]);
            $region->country = $country;

            $region->state_description = '';
            if ($region->assign_state === '0') {
                $region->states = [];
                if ($country->state_count === '0') {
                    // 该国无洲
                    $region->state_description = '无';
                } else {
                    $region->state_description = '全部';
                }
            } elseif ($region->assign_state === '1') {
                $sql = 'SELECT * FROM `shopfai_shipping_region_state` WHERE shipping_id =? AND shipping_region_id =?';
                $states = $db->getObjects($sql, [$shippingId, $region->id]);
                $region->states = $states;

                $count = count($states);
                $region->state_description = $count . '/' . $country->state_count . ' 个州/省';
            }
        }
        $shipping->regions = $regions;

        $sql = 'SELECT * FROM `shopfai_shipping_plan` WHERE shipping_id=?';
        $plans = $db->getObjects($sql, [$shipping->id]);
        $shipping->plans = $plans;

        return $shipping;
    }


    /**
     * 获取区域树
     *
     * @param string $shippingId 运费方案ID
     * @return array
     */
    public function getRegionTree($shippingId = null)
    {
        $db = Be::getDb();
        $params = [];
        $sql = 'SELECT country_code FROM `shopfai_shipping_region` WHERE 1';
        if ($shippingId !== null) {
            $sql .= ' AND shipping_id!=?';
            $params[] = $shippingId;
        }
        $existCountryCodes = $db->getValues($sql, $params);

        $db = Be::getDb();
        $sql = 'SELECT * FROM `shopfai_region_continent` ORDER BY `ordering` ASC';
        $continents = $db->getObjects($sql);
        foreach ($continents as $continent) {
            $sql = 'SELECT * FROM `shopfai_region_country` WHERE continent_code = \'' . $continent->code . '\' ORDER BY `name` ASC';
            $countries = $db->getObjects($sql);
            foreach ($countries as $country) {
                if (in_array($country->code, $existCountryCodes)) {
                    $country->disabled = true;
                }
            }

            $continent->countries = $countries;
        }

        return $continents;
    }

    /**
     * 删除
     *
     * @return void
     */
    public function delete($shippingId)
    {
        $db = Be::getDb();

        $sql = 'SELECT * FROM `shopfai_shipping` WHERE id=?';
        $shipping = $db->getObject($sql, [$shippingId]);

        if (!$shipping) {
            throw new ServiceException('运费方案（# ' . $shippingId . '）不存在！');
        }

        $db->startTransaction();
        try {
            $sql = 'DELETE FROM `shopfai_shipping` WHERE id=?';
            $db->query($sql, [$shippingId]);

            $sql = 'DELETE FROM `shopfai_shipping_region` WHERE shipping_id=?';
            $db->query($sql, [$shippingId]);

            $sql = 'DELETE FROM `shopfai_shipping_region_state` WHERE shipping_id=?';
            $db->query($sql, [$shippingId]);

            $sql = 'DELETE FROM `shopfai_shipping_plan` WHERE shipping_id=?';
            $db->query($sql, [$shippingId]);

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();
            Be::getLog()->error($t);

            throw new ServiceException('删除区域方案发生异常！');
        }

    }

}
