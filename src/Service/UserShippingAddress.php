<?php

namespace Be\App\ShopFai\Service;

use Be\App\ServiceException;
use Be\Be;

class UserShippingAddress
{

    /**
     * 获取用户收货地址列表
     *
     * @return array
     */
    public function getAddresses(): array
    {
        $my = Be::getUser();

        return Be::getTable('shopfai_user_shipping_address')
            ->where('user_id', $my->id)
            ->orderBy('create_time', 'ASC')
            ->getObjects();
    }

    /**
     * 获取用户收货地址
     *
     * @param string $my- >id
     * @param string $addressId
     * @return object
     */
    public function getAddress(string $addressId): object
    {
        $tuple = Be::getTuple('shopfai_user_shipping_address');
        try {
            $tuple->load($addressId);
        } catch (\Throwable $t) {
            throw new ServiceException('Shipping address (#' . $addressId . ') does not exist!');
        }

        $my = Be::getUser();
        if ($tuple->user_id !== $my->id) {
            throw new ServiceException('Shipping address (#' . $addressId . ') does not exist!');
        }

        return $tuple->toObject();
    }

    /**
     * 获取用户默认收货地址
     *
     * @return false|object
     */
    public function getDefaultAddress()
    {
        $my = Be::getUser();

        $defaultAddress = Be::getTable('shopfai_user_shipping_address')
            ->where('user_id', $my->id)
            ->where('is_default', 1)
            ->getObject();

        if (!$defaultAddress) {
            $defaultAddress = Be::getTable('shopfai_user_shipping_address')
                ->where('user_id', $my->id)
                ->orderBy('create_time', 'DESC')
                ->getObject();
        }

        return $defaultAddress;
    }

    /**
     * 新增/编辑 收货地址
     *
     * @param array $data
     * @return int
     * @throws \Throwable
     */
    public function edit(array $data): int
    {
        $my = Be::getUser();

        if (!isset($data['id']) || !is_string($data['id'])) {
            $data['id'] = '';
        }

        $isNew = $data['id'] === '';

        if ($isNew) {
            if (Be::getTable('shopfai_user_shipping_address')
                ->where('user_id', $my->id)
                ->count() >= 10) {
                throw new ServiceException('You can max add 10 shipping address!');
            }
        }

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

        if (!isset($data['is_default']) || !is_numeric($data['is_default'])) {
            $data['is_default'] = 0;
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

        $tupleUserAddress = Be::getTuple('shopfai_user_shipping_address');
        if ($data['id'] !== '') {
            try {
                $tupleUserAddress->load($data['id']);
            } catch (\Throwable $t) {
                throw new ServiceException('Shipping Address (#' . $data['id'] . ') does not exist');
            }

            if ($tupleUserAddress->user_id != $my->id) {
                throw new ServiceException('Shipping Address (#' . $data['id'] . ') does not exist');
            }
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $now = date('Y-m-d H:i:s');
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

            if (isset($data['is_default']) && $data['is_default']) {
                $table = Be::getTable('shopfai_user_shipping_address');
                $table->where('user_id', $my->id);
                $table->where('is_default', 1);
                if (!$isNew) {
                    $table->where('id', '!=',  $data['id']);
                }
                $table->update(['is_default' => 0]);

                $tupleUserAddress->is_default = 1;
            } else {
                $tupleUserAddress->is_default = 0;
            }

            if ($isNew) {
                $tupleUserAddress->create_time = $now;
            }

            $tupleUserAddress->update_time = $now;
            $tupleUserAddress->save();

            $this->updateDefault();

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException(($isNew ? 'Add' : 'Edit') . ' shipping address error (log id: ' . $logId . ') ' );
        }

        return $isNew ? 1 : 2;
    }

    /**
     * 删除收货地址
     *
     * @param string $addressId
     * @return bool
     * @throws ServiceException
     */
    public function delete(string $addressId): bool
    {
        $my = Be::getUser();

        $tupleUserAddress = Be::getTuple('shopfai_user_shipping_address');
        try {
            $tupleUserAddress->load($addressId);
        } catch (\Throwable $t) {
            throw new ServiceException('Shipping Address (#' . $addressId . ') does not exist');
        }

        if ($tupleUserAddress->user_id != $my->id) {
            throw new ServiceException('Shipping Address (#' . $addressId . ') does not exist');
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {
            $tupleUserAddress->delete();
            $this->updateDefault();
            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException('Delete shipping address error (log id: ' . $logId . ') ' );
        }

        return true;
    }

    /**
     * 更新默认地址
     *
     */
    private function updateDefault()
    {
        $my = Be::getUser();

        $n = Be::getTable('shopfai_user_shipping_address')
            ->where('user_id', $my->id)
            ->where('is_default', 1)
            ->count();

        if ($n === 1) return;

        if ($n === 0) {
            // 没有默认收货地址时，取一条标记为默认收货地址
            $addressId = Be::getTable('shopfai_user_shipping_address')
                ->where('user_id', $my->id)
                ->getValue('id');
            if ($addressId) {
                Be::getTable('shopfai_user_shipping_address')
                    ->where('id', $addressId)
                    ->update(['is_default' => 1]);
            }
        } elseif ($n > 1) {
            // 有多个收货地址时，只保留一个
            $addressId = Be::getTable('shopfai_user_shipping_address')
                ->where('user_id', $my->id)
                ->where('is_default', 1)
                ->getValue('id');

            Be::getTable('shopfai_user_shipping_address')
                ->where('id', '!=', $addressId)
                ->where('user_id', $my->id)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);
        }
    }

    /**
     * 设置为默认收货地址
     *
     * @param string $addressId
     * @return bool
     * @throws ServiceException
     */
    public function setDefault(string $addressId): bool
    {
        $my = Be::getUser();
        $tupleUserAddress = Be::getTuple('shopfai_user_shipping_address');
        try {
            $tupleUserAddress->load($addressId);
        } catch (\Throwable $t) {
            throw new ServiceException('Shipping Address (#' . $addressId . ') does not exist');
        }

        if ($tupleUserAddress->user_id != $my->id) {
            throw new ServiceException('Shipping Address (#' . $addressId . ') does not exist');
        }

        $db = Be::getDb();
        $db->startTransaction();
        try {
            Be::getTable('shopfai_user_shipping_address')
                ->where('user_id', $my->id)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);

            $tupleUserAddress->is_default = 1;
            $tupleUserAddress->update_time = date('Y-m-d H:i:s');
            $tupleUserAddress->update();

            $db->commit();
        } catch (\Throwable $t) {
            $db->rollback();

            $logId = Be::getLog()->error($t);
            throw new ServiceException('Set default shipping address erro (log id: ' . $logId . ') ' );
        }

        return true;
    }

}
