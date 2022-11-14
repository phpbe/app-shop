<?php

namespace Be\App\Shop\Service;

use Be\Be;

/**
 * Class Region
 *
 * @package Be\App\Shop\Service
 */
class Region
{

    /**
     * 获取 洲/区域
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return object|false
     */
    public function getContinent(string $continentIdOrCode): object
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Continent:' . $continentIdOrCode;
        $continent = $cache->get($key);
        if ($continent) {
            return $continent;
        }

        return false;
    }

    /**
     * 获取 洲/区域- id -> name 键值对
     *
     * @return array
     */
    public function getContinentIdNameKeyValues(): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Continent:idNameKeyValues';
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 洲/区域- code -> name 键值对
     *
     * @return array
     */
    public function getContinentCodeNameKeyValues(): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Continent:codeNameKeyValues';
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 洲/区域- id -> name_cn 键值对
     *
     * @return array
     */
    public function getContinentIdCnNameKeyValues(): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Continent:idCnNameKeyValues';
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 洲/区域- code -> name_cn 键值对
     *
     * @return array
     */
    public function getContinentCodeCnNameKeyValues(): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Continent:codeCnNameKeyValues';
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 跟据ID或编码获取 洲/区域-名称
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return string
     */
    public function getContinentName(string $continentIdOrCode): string
    {
        $continent = $this->getContinent($continentIdOrCode);
        return $continent->name ?? '';
    }

    /**
     * 跟据ID或编码获取 洲/区域-中文名称
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return string
     */
    public function getContinentCnName(string $continentIdOrCode): string
    {
        $continent = $this->getContinent($continentIdOrCode);
        return $continent->name ?? '';
    }


    /**
     * 获取 国家
     *
     * @param string $countryIdOrCode 国家的ID或二字码
     * @return object|false
     */
    public function getCountry(string $countryIdOrCode): object
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Country:' . $countryIdOrCode;
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return false;
    }

    /**
     * 获取 国家- id -> name 键值对
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return array
     */
    public function getCountryIdNameKeyValues(string $continentIdOrCode = ''): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Country:idNameKeyValues';
        if ($continentIdOrCode) {
            $key .= ':' . $continentIdOrCode;
        }
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 国家- code -> name 键值对
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return array
     */
    public function getCountryCodeNameKeyValues(string $continentIdOrCode = ''): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Country:codeNameKeyValues';
        if ($continentIdOrCode) {
            $key .= ':' . $continentIdOrCode;
        }
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 国家- id -> name_cn 键值对
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return array
     */
    public function getCountryIdCnNameKeyValues(string $continentIdOrCode = ''): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Country:idCnNameKeyValues';
        if ($continentIdOrCode) {
            $key .= ':' . $continentIdOrCode;
        }
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 国家- code -> name_cn 键值对
     *
     * @param string $continentIdOrCode 洲/区域的ID或编码
     * @return array
     */
    public function getCountryCodeCnNameKeyValues(string $continentIdOrCode = ''): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:Country:codeCnNameKeyValues';
        if ($continentIdOrCode) {
            $key .= ':' . $continentIdOrCode;
        }
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 跟据ID获取 国家-名称
     *
     * @param string $countryIdOrCode 国家的ID或二字码
     * @return string
     */
    public function getCountryName(string $countryIdOrCode): string
    {
        $country = $this->getCountry($countryIdOrCode);
        return $country->name ?? '';
    }

    /**
     * 跟据ID获取 国家-中文名称
     *
     * @param string $countryIdOrCode 国家的ID或二字码
     * @return string
     */
    public function getCountryCnName(string $countryIdOrCode): string
    {
        $country = $this->getCountry($countryIdOrCode);
        return $country->name_cn ?? '';
    }

    /**
     * 获取 州
     *
     * @param string $stateId 州的ID
     * @return object|false
     */
    public function getState(string $stateId): object
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:State:' . $stateId;
        $state = $cache->get($key);
        if ($state) {
            return $state;
        }

        return false;
    }

    /**
     * 获取 州- id -> name 键值对
     *
     * @param string $countryIdOrCode 国家的ID或二字码
     * @return array
     */
    public function getStateIdNameKeyValues(string $countryIdOrCode): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:State:idNameKeyValues:' . $countryIdOrCode;
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }

    /**
     * 获取 州- id -> name_cn 键值对
     *
     * @param string $countryIdOrCode 国家的ID或二字码
     * @return array
     */
    public function getStateIdCnNameKeyValues(string $countryIdOrCode): array
    {
        $cache = Be::getCache();
        $key = 'Shop:Region:State:idCnNameKeyValues:' . $countryIdOrCode;
        $keyValues = $cache->get($key);
        if ($keyValues) {
            return $keyValues;
        }

        return [];
    }


    /**
     * 跟据ID获取 州-名称
     *
     * @param string $stateId 州的ID
     * @return string
     */
    public function getStateName(string $stateId): string
    {
        $state = $this->getState($stateId);
        return $state->name ?? '';
    }

    /**
     * 跟据ID获取 州-中文名称
     *
     * @param string $stateId 州的ID
     * @return string
     */
    public function getStateCnName(string $stateId): string
    {
        $state = $this->getState($stateId);
        return $state->name_cn ?? '';
    }



}
