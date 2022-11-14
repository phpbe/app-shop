<?php

namespace Be\App\Shop\Service\Admin;

use Be\Be;

/**
 * Class Region
 *
 * @package Be\App\Shop\Admin\Service
 */
class Region
{

    /**
     * 获取区域列表
     *
     * @return array
     */
    public function getContinentCountryTree()
    {
        $db = Be::getDb();
        $sql = 'SELECT code, `name`, name_cn FROM `shop_region_continent` ORDER BY `ordering` ASC';
        $continents = $db->getObjects($sql);
        foreach ($continents as $continent) {
            $sql = 'SELECT continent_code, code, `name`, name_cn, flag FROM `shop_region_country` WHERE continent_code = \'' . $continent->code . '\' ORDER BY `name` ASC';
            $countries = $db->getObjects($sql);
            $continent->countries = $countries;
        }

        return $continents;
    }


    /**
     * 获取洲列表
     *
     * @return array
     */
    public function getContinents()
    {
        $db = Be::getDb();
        $sql = 'SELECT code, `name`, name_cn FROM `shop_region_continent` ORDER BY `ordering` ASC';
        $continents = $db->getObjects($sql);
        return $continents;
    }

    /**
     * 获取国家列表
     *
     * @return array
     */
    public function getCountries(string $continentCode = null)
    {
        $db = Be::getDb();
        $sql = 'SELECT id, continent_code, code, `name`, name_cn, flag FROM `shop_region_country`';
        if ($continentCode !== null) {
            $sql .= ' WHERE continent_code = \'' . $continentCode . '\'';
        }
        $sql .= ' ORDER BY `name` ASC';
        $countries = $db->getObjects($sql);
        return $countries;
    }

    /**
     * 获取国家键值对
     *
     * @return array
     */
    public function getCountryKeyValues()
    {
        $db = Be::getDb();
        $sql = 'SELECT code, `name_cn` FROM `shop_region_country` ORDER BY `name` ASC';
        $keyValues = $db->getKeyValues($sql);
        return $keyValues;
    }

    /**
     * 获取指定国家编码的州/省份 键值对
     *
     * @param string $countryCode
     * @return array
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getStates(string $countryCode)
    {
        $db = Be::getDb();
        $sql = 'SELECT id, `name`, name_cn FROM `shop_region_state` WHERE  country_code = \'' . $countryCode . '\' ORDER BY `name` ASC';
        $states = $db->getObjects($sql);
        return $states;
    }

    /**
     * 获取指定国家编码的州/省份 中文名称列表
     *
     * @param string $countryCode 国家二字编码
     * @return array
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getStateCnNames(string $countryCode)
    {
        $db = Be::getDb();
        $sql = 'SELECT `name_cn` FROM `shop_region_states` WHERE  country_id = \'' . $countryCode . '\' ORDER BY `name` ASC';
        $values = $db->getValues($sql);
        return $values;
    }

    /**
     * 获取指定国家编码的州/省份 名称列表
     *
     * @param string $countryCode 国家二字编码
     * @return array
     * @throws \Be\Db\DbException
     * @throws \Be\Runtime\RuntimeException
     */
    public function getStateNames(string $countryCode)
    {
        $db = Be::getDb();
        $sql = 'SELECT `name` FROM `shop_region_states` WHERE  country_id = \'' . $countryCode . '\' ORDER BY `name` ASC';
        $values = $db->getValues($sql);
        return $values;
    }

}
