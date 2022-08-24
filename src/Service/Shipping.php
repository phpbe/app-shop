<?php

namespace Be\App\ShopFai\Service;

use Be\App\ControllerException;
use Be\App\ServiceException;
use Be\Be;

/**
 * Class Shipping
 *
 * @package Be\App\ShopFai\Service
 */
class Shipping
{

    /**
     * 获取国家键值对
     *
     * @return array
     */
    public function getCountryIdNameKeyValues(): array
    {
        $countryIds = Be::getTable('shopfai_shipping_region')
            ->getValues('country_id');

        if (count($countryIds) === 0) {
            return [];
        }

        $countryKeyValues = Be::getTable('shopfai_region_country')
            ->where('id', 'in', $countryIds)
            ->getKeyValues('id', 'name');

        return $countryKeyValues;
    }

    /**
     * 获取州/省份列表
     *
     * @param string $countryId 国家ID
     * @return array
     */
    public function getStateIdNameKeyValues(string $countryId): array
    {
        $tupleCountry = Be::getTuple('shopfai_region_country');
        try {
            $tupleCountry->load($countryId);
        } catch (\Throwable $t) {
            throw new ServiceException('Country(#' . $countryId . ') does not exist!');
        }

        // 国家不存在，或该国家没有州设置
        if ($tupleCountry->state_count === 0) {
            return [];
        }

        $shippingRegions = Be::getTable('shopfai_shipping_region')
            ->where('country_id', $countryId)
            ->getObjects();

        $serviceRegion = Be::getService('App.ShopFai.Region');

        $stateIdNameKeyValues = [];
        foreach ($shippingRegions as $shippingRegion) {
            if ($shippingRegion->assign_state === '0') {
                return $serviceRegion->getStateIdNameKeyValues($countryId);
            } else {
                $shippingRegionStates = Be::getTable('shopfai_shipping_region_state')
                    ->where('shipping_region_id', $shippingRegion->id)
                    ->getObjects();

                foreach ($shippingRegionStates as $shippingRegionState) {
                    $stateIdNameKeyValues[$shippingRegionState->state_id] = $serviceRegion->getStateName($shippingRegionState->state_id);
                }
            }
        }

        return $stateIdNameKeyValues;
    }

    /**
     * 获取物流方案列表
     *
     * @param array $cart 购物车
     * @return array
     */
    public function getShippingPlans(array $cart = []): array
    {
        if (!isset($cart['country_id'])) {
            throw new ControllerException('Parameter (country_id) is missing!');
        }
        $countryId = $cart['country_id'];

        $tupleCountry = Be::getTuple('shopfai_region_country');
        try {
            $tupleCountry->load($cart['country_id']);
        } catch (\Throwable $t) {
            throw new ServiceException('Country (#' . $cart['country_id'] . ') does not exist!');
        }

        $stateId = $cart['state_id'] ?? '';

        $shippingIds = [];
        $shippingRegions = Be::getTable('shopfai_shipping_region')
            ->where('country_id', $countryId)
            ->getObjects();
        foreach ($shippingRegions as $shippingRegion) {
            if ($stateId === '') {
                // 该国家没有州
                if ($tupleCountry->state_count === 0) {
                    $shippingIds[] = $shippingRegion->shipping_id;
                }
            } else {
                if ($shippingRegion->assign_state === '0') {
                    $shippingIds[] = $shippingRegion->shipping_id;
                } else {
                    if (Be::getTable('shopfai_shipping_region_state')
                            ->where('shipping_region_id', $shippingRegion->id)
                            ->where('state_id', $stateId)
                            ->count() > 0) {
                        $shippingIds[] = $shippingRegion->shipping_id;
                    }
                }
            }
        }

        if (count($shippingIds) === 0) {
            return [];
        }

        $shippingPlans = Be::getTable('shopfai_shipping_plan')
            ->where('shipping_id', 'in', $shippingIds)
            ->getObjects();
        foreach ($shippingPlans as $shippingPlan) {
            try {
                $shippingFee = $this->calcShippingFee($shippingPlan, $cart);
            } catch (\Throwable $t) {
                continue;
            }

            $matchedShippingPlans[] = (object)[
                'id' => $shippingPlan->id,
                'cod' => $shippingPlan->cod,
                'name' => $shippingPlan->name,
                'description' => $shippingPlan->description,
                'shipping_fee' => $shippingFee,
            ];
        }

        return $matchedShippingPlans;
    }

    /**
     * 获取运费
     *
     * @param array $cart 购物车
     *
     * @return string
     */
    public function getShippingFee(array $cart = []): string
    {
        if (!isset($cart['shipping_plan_id'])) {
            throw new ControllerException('Parameter (shipping_plan_id) is missing!');
        }
        $shippingPlanId = $cart['shipping_plan_id'];

        if (!isset($cart['country_id'])) {
            throw new ControllerException('Parameter (country_id) is missing!');
        }
        $countryId = $cart['country_id'];

        $tupleCountry = Be::getTuple('shopfai_region_country');
        try {
            $tupleCountry->load($cart['country_id']);
        } catch (\Throwable $t) {
            throw new ServiceException('Country (#' . $cart['country_id'] . ') does not exist!');
        }

        $stateId = $cart['state_id'] ?? '';

        // 讯取物流方案
        $tupleShippingPlan = Be::getTuple('shopfai_shipping_plan');
        try {
            $tupleShippingPlan->load($cart['shipping_plan_id']);
        } catch (\Throwable $t) {
            throw new ServiceException('Shipping plan (#' . $shippingPlanId . ') does not exist!');
        }

        // 检查国家是否可以寄达
        $tupleShippingRegion = Be::getTuple('shopfai_shipping_region');
        try {
            $tupleShippingRegion->loadBy([
                'shipping_id' => $tupleShippingPlan->shipping_id,
                'country_id' => $countryId,
            ]);
        } catch (\Throwable $t) {
            throw new ServiceException('Shipping plan (#' . $shippingPlanId . ') does not support your region!');
        }

        if ($tupleShippingRegion->assign_state === 1) {
            // 检查州是否可以寄达
            $tupleShippingRegionState = Be::getTuple('shopfai_shipping_region_state');
            try {
                $tupleShippingRegionState->loadBy([
                    'shipping_id' => $tupleShippingPlan->shipping_id,
                    'shipping_region_id' => $tupleShippingRegion->id,
                    'state_id' => $stateId,
                ]);
            } catch (\Throwable $t) {
                throw new ServiceException('Shipping plan (#' . $shippingPlanId . ') does not support your region!');
            }
        }

        return $this->calcShippingFee($tupleShippingPlan->toObject(), $cart);
    }

    /**
     * 计算运费逻辑
     *
     * @param $tupleShippingPlan
     * @param array $cart
     * @return string
     * @throws ServiceException
     * @throws \Be\Runtime\RuntimeException
     */
    private function calcShippingFee($shippingPlan, array $cart = []): string
    {
        $configStore = Be::getConfig('App.ShopFai.Store');

        if (!isset($cart['products']) || !is_array($cart['products']) || count($cart['products']) === 0) {
            $cart['products'] = Be::getService('App.ShopFai.Cart')->formatProducts($cart, true);
        }

        $productTotalAmount = '0.00';
        $productTotalQuantity = 0;
        foreach ($cart['products'] as $product) {
            $productTotalAmount = bcadd($productTotalAmount, bcmul($product->price, $product->quantity, 2), 2);
            $productTotalQuantity = $productTotalQuantity + $product->quantity;
        }

        if ($shippingPlan->limit) {
            if ($shippingPlan->limit_type === 'amount') {

                // 下单限制类型：amount:商品总金额，
                if (bccomp($shippingPlan->limit_amount_from, $productTotalAmount, 2) === 1) {
                    throw new ServiceException('Shipping plan (' . $shippingPlan->name . ') require total amount ' . $configStore->currencySymbol . ' ' . $shippingPlan->limit_amount_from . ' at least!');
                }

                if ($shippingPlan->limit_amount_to !== '-1.00') {
                    if (bccomp($shippingPlan->limit_amount_to, $productTotalAmount, 2) === -1) {
                        throw new ServiceException('Shipping plan (' . $shippingPlan->name . ') require total amount ' . $configStore->currencySymbol . ' ' . $shippingPlan->limit_amount_to . ' at most!');
                    }
                }
            } elseif ($shippingPlan->limit_type === 'quantity') {

                // 下单限制类型：quantity:商品总件数，
                $limitQuantityFrom = (int)$shippingPlan->limit_quantity_from;
                if ($limitQuantityFrom > $productTotalQuantity) {
                    throw new ServiceException('Shipping plan (' . $shippingPlan->name . ') require total quantity ' . $shippingPlan->limit_quantity_from . ' at least!');
                }

                $limitQuantityTo = (int)$shippingPlan->limit_quantity_to;
                if ($limitQuantityTo !== -1) {
                    if ($limitQuantityTo < $productTotalQuantity) {
                        throw new ServiceException('Shipping plan (' . $shippingPlan->name . ') require total quantity ' . $shippingPlan->limit_quantity_to . ' at most!');
                    }
                }

            } elseif ($shippingPlan->limit_type === 'weight') {
                // 下单限制类型：amount:订单金额，

                $productTotalWeight = '0.00';
                foreach ($cart['products'] as $product) {
                    $weight = $this->weightConvert($product->weight, $product->weight_unit, $shippingPlan->limit_weight_unit);
                    $productTotalWeight = bcadd($productTotalWeight, bcmul($weight, $product->quantity, 2), 2);
                }

                // 下单限制类型：amount:商品总金额，
                if (bccomp($shippingPlan->limit_weight_from, $productTotalWeight, 2) === 1) {
                    throw new ServiceException('Shipping plan (' . $shippingPlan->name . ') require total weight ' . $shippingPlan->limit_weight_from . ' ' . $shippingPlan->limit_weight_unit . ' at least!');
                }

                if ($shippingPlan->limit_weight_to !== '-1.00') {
                    if (bccomp($shippingPlan->limit_weight_to, $productTotalWeight, 2) === -1) {
                        throw new ServiceException('Shipping plan (' . $shippingPlan->name . ') require total quantity ' . $shippingPlan->limit_weight_to . ' ' . $shippingPlan->limit_weight_unit . ' at most!');
                    }
                }
            }
        }

        // 运费
        if ($shippingPlan->shipping_fee_type === 'fixed') {
            // 固定运费
            $shippingFee = $shippingPlan->shipping_fee_fixed;
        } else {

            // 首重运费
            $shippingFee = $shippingPlan->shipping_fee_first_weight_price;

            $productTotalWeight = '0.00';
            foreach ($cart['products'] as $product) {
                $weight = $this->weightConvert($product->weight, $product->weight_unit, $shippingPlan->shipping_fee_first_weight_unit);
                $productTotalWeight = bcadd($productTotalWeight, bcmul($weight, $product->quantity, 2), 2);
            }

            //  超过了首重
            if (bccomp($productTotalWeight, $shippingPlan->shipping_fee_first_weight, 2) === 1) {
                // 续重
                $additionalWeight = bcsub($productTotalWeight - $shippingPlan->shipping_fee_first_weight, 2);
                $additionalWeight = $this->weightConvert($additionalWeight, $shippingPlan->shipping_fee_first_weight_unit, $shippingPlan->shipping_fee_additional_weight_unit);

                // 续重费用
                $additionalWeightParts = ceil((float)bcdiv($additionalWeight, $shippingPlan->shipping_fee_additional_weight, 1));
                $additionalFee = bcmul($shippingPlan->shipping_fee_additional_weight_price, $additionalWeightParts, 2);

                // 总运费
                $shippingFee = bcadd($shippingFee, $additionalFee, 2);
            }
        }

        return $shippingFee;
    }

    /**
     * 岳量转换
     *
     * @param string $weightFrom 源重量
     * @param string $weightUnitFrom 源重量单位 kg/g/lb/oz
     * @param string $weightUnitTo 目标重量单位 kg/g/lb/oz
     * @return string
     */
    public function weightConvert(string $weightFrom, string $weightUnitFrom, string $weightUnitTo): string
    {
        switch ($weightUnitFrom) {
            case 'kg':
                switch ($weightUnitTo) {
                    case 'kg':
                        return $weightFrom;
                    case 'g':
                        return bcmul($weightFrom, '1000', 2);
                    case 'lb':
                        return bcdiv($weightFrom, '0.45359237', 2);
                    case 'oz':
                        return bcdiv($weightFrom, '0.02835', 2);
                    default:
                        throw new ServiceException('Unknown weight unit: ' . $weightUnitTo);
                }
            case 'g':
                switch ($weightUnitTo) {
                    case 'kg':
                        return bcdiv($weightFrom, '1000', 2);
                    case 'g':
                        return $weightFrom;
                    case 'lb':
                        return bcdiv($weightFrom, '453.59237', 2);
                    case 'oz':
                        return bcdiv($weightFrom, '28.35', 2);
                    default:
                        throw new ServiceException('Unknown weight unit: ' . $weightUnitTo);
                }
            case 'lb':
                switch ($weightUnitTo) {
                    case 'kg':
                        return bcmul($weightFrom, '0.45359237', 2);
                    case 'g':
                        return bcmul($weightFrom, '453.59237', 2);
                    case 'lb':
                        return $weightFrom;
                    case 'oz':
                        return bcmul($weightFrom, '16', 2);
                    default:
                        throw new ServiceException('Unknown weight unit: ' . $weightUnitTo);
                }
            case 'oz':
                switch ($weightUnitTo) {
                    case 'kg':
                        return bcmul($weightFrom, '0.02835', 2);
                    case 'g':
                        return bcmul($weightFrom, '28.35', 2);
                    case 'lb':
                        return bcdiv($weightFrom, '16', 2);
                    case 'oz':
                        return $weightFrom;
                    default:
                        throw new ServiceException('Unknown weight unit: ' . $weightUnitTo);
                }
            default:
                throw new ServiceException('Unknown weight unit: ' . $weightUnitFrom);
        }
    }

}
