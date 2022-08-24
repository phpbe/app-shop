<?php

namespace Be\App\ShopFai\Service;

use Be\App\ServiceException;
use Be\Be;

class UserBillingAddress
{

    /**
     * 获取用户账单地址
     *
     * @return false|object
     */
    public function getAddress()
    {
        $my = Be::getUser();
        $tupleUserAddress = Be::getTuple('shopfai_user_billing_address');
        try {
            $tupleUserAddress->loadBy('user_id', $my->id);
        } catch (\Throwable $t) {
            return false;
        }
        return $tupleUserAddress->toObject();
    }

    /**
     * 新增/编辑 账单地址
     *
     * @param array $data
     * @return int
     * @throws \Throwable
     */
    public function edit(array $data): int
    {
        $my = Be::getUser();

        if (!isset($data['first_name']) || !is_string($data['first_name'])) {
            $data['first_name'] = '';
        }

        if (!isset($data['last_name']) || !is_string($data['last_name'])) {
            $data['last_name'] = '';
        }

        if (!isset($data['country_id']) || !is_string($data['country_id'])) {
            $data['country_id'] = '';
        }

        if (!isset($data['state_id']) || !is_string($data['state_id'])) {
            $data['state_id'] = '';
        }

        if (!isset($data['city']) || !is_string($data['city'])) {
            $data['city'] = '';
        }

        if (!isset($data['address']) || !is_string($data['address'])) {
            $data['address'] = '';
        }

        if (!isset($data['address2']) || !is_string($data['address2'])) {
            $data['address2'] = '';
        }

        if (!isset($data['zip_code']) || !is_string($data['zip_code'])) {
            $data['zip_code'] = '';
        }

        if (!isset($data['mobile']) || !is_string($data['mobile'])) {
            $data['mobile'] = '';
        }

        if ($data['first_name'] === '') {
            throw new ServiceException('Please enter your first name!');
        }

        if ($data['last_name'] === '') {
            throw new ServiceException('Please enter your last name!');
        }

        if ($data['country_id'] === '') {
            throw new ServiceException('Please select your country!');
        }

        if ($data['city'] === '') {
            throw new ServiceException('Please enter your city!');
        }

        if ($data['address'] === '') {
            throw new ServiceException('Please enter your address!');
        }

        if ($data['zip_code'] === '') {
            throw new ServiceException('Please enter your zip code!');
        }

        if ($data['mobile'] === '') {
            throw new ServiceException('Please enter your mobile phone number!');
        }

        $data['country_name'] = '';
        $data['country_code'] = '';
        $tupleRegionCountry = Be::getTuple('shopfai_region_country');
        try {
            $tupleRegionCountry->load($data['country_id']);
            $data['country_name'] = $tupleRegionCountry->name;
            $data['country_code'] = $tupleRegionCountry->code;
        } catch (\Throwable $t) {
            throw new ServiceException('Country (#' . $data['country_id'] . ') does not exist!');
        }

        $data['state_name'] = '';
        if ($data['state_id'] !== '') {
            $tupleRegionState = Be::getTuple('shopfai_region_state');
            try {
                $tupleRegionState->load($data['state_id']);
                $data['state_name'] = $tupleRegionState->name;
            } catch (\Throwable $t) {
                throw new ServiceException('State (#' . $data['state_id'] . ') does not exist!');
            }
        }

        $isNew = false;
        $db = Be::getDb();
        $db->startTransaction();
        try {

            $now = date('Y-m-d H:i:s');
            $tupleUserAddress = Be::getTuple('shopfai_user_billing_address');

            try {
                $tupleUserAddress->loadBy([
                    'user_id' => $my->id
                ]);
            } catch (\Throwable $t) {
                $isNew = true;
            }

            $tupleUserAddress->user_id = $my->id;
            $tupleUserAddress->first_name = $data['first_name'];
            $tupleUserAddress->last_name = $data['last_name'];
            $tupleUserAddress->country_id = $data['country_id'];
            $tupleUserAddress->country_name = $data['country_name'];
            $tupleUserAddress->country_code = $data['country_code'];
            $tupleUserAddress->state_id = $data['state_id'];
            $tupleUserAddress->state_name = $data['state_name'];
            $tupleUserAddress->city = $data['city'];
            $tupleUserAddress->address = $data['address'];
            $tupleUserAddress->address2 = $data['address2'];
            $tupleUserAddress->zip_code = $data['zip_code'];
            $tupleUserAddress->mobile = $data['mobile'];

            if ($isNew) {
                $tupleUserAddress->create_time = $now;
            }

            $tupleUserAddress->update_time = $now;
            $tupleUserAddress->save();

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException(($isNew ? 'Add' : 'Edit') . ' billing address error (log id: ' . $logId . ') ' );
        }

        return $isNew ? 1 : 2;
    }

    /**
     * 删除账单地址
     *
     * @return bool
     * @throws ServiceException
     */
    public function delete(): bool
    {
        $my = Be::getUser();
        $tupleUserAddress = Be::getTuple('shopfai_user_billing_address');

        $exist = true;
        try {
            $tupleUserAddress->loadBy([
                'user_id' => $my->id
            ]);
        } catch (\Throwable $t) {
            $exist = false;
        }

        if ($exist) {
            $tupleUserAddress->delete();
        }

        return true;
    }


}
