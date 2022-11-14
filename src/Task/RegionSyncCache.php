<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * @BeTask("区域信息同步到缓存")
 */
class RegionSyncCache extends Task
{


    public function execute()
    {
        $cache = Be::getCache();
        $db = Be::getDb();

        $sql = 'SELECT * FROM shop_region_continent';
        $continents = $db->getObjects($sql);

        $continentIdNameKeyValues = [];
        $continentCodeNameKeyValues = [];
        $continentIdCnNameKeyValues = [];
        $continentCodeCnNameKeyValues = [];
        foreach ($continents as $continent) {
            $key = 'Shop:Region:Continent:' . $continent->id;
            $cache->set($key, $continent);

            $key = 'Shop:Region:Continent:' . $continent->code;
            $cache->set($key, $continent);

            $continentIdNameKeyValues[$continent->id] = $continent->name;
            $continentCodeNameKeyValues[$continent->code] = $continent->name;
            $continentIdCnNameKeyValues[$continent->id] = $continent->name_cn;
            $continentCodeCnNameKeyValues[$continent->code] = $continent->name_cn;
        }
        $cache->set('Shop:Region:Continent:idNameKeyValues', $continentIdNameKeyValues);
        $cache->set('Shop:Region:Continent:codeNameKeyValues', $continentCodeNameKeyValues);
        $cache->set('Shop:Region:Continent:idCnNameKeyValues', $continentIdCnNameKeyValues);
        $cache->set('Shop:Region:Continent:codeCnNameKeyValues', $continentCodeCnNameKeyValues);


        $sql = 'SELECT * FROM shop_region_country';
        $countries = $db->getObjects($sql);

        $countryIdNameKeyValues = [];
        $countryCodeNameKeyValues = [];
        $countryIdCnNameKeyValues = [];
        $countryCodeCnNameKeyValues = [];
        foreach ($countries as $country) {
            $key = 'Shop:Region:Country:' . $country->id;
            $cache->set($key, $country);

            $key = 'Shop:Region:Country:' . $country->code;
            $cache->set($key, $country);

            $countryIdNameKeyValues[$country->id] = $country->name;
            $countryCodeNameKeyValues[$country->code] = $country->name;
            $countryIdCnNameKeyValues[$country->id] = $country->name_cn;
            $countryCodeCnNameKeyValues[$country->code] = $country->name_cn;
        }
        $cache->set('Shop:Region:Country:idNameKeyValues', $countryIdNameKeyValues);
        $cache->set('Shop:Region:Country:codeNameKeyValues', $countryCodeNameKeyValues);
        $cache->set('Shop:Region:Country:idCnNameKeyValues', $countryIdCnNameKeyValues);
        $cache->set('Shop:Region:Country:codeCnNameKeyValues', $countryCodeCnNameKeyValues);


        foreach ($continents as $continent) {
            $sql = 'SELECT * FROM shop_region_country WHERE continent_id = ?';
            $continentCountries = $db->getObjects($sql, [$continent->id]);
            if (count($continentCountries) > 0) {
                $countryIdNameKeyValues = [];
                $countryCodeNameKeyValues = [];
                $countryIdCnNameKeyValues = [];
                $countryCodeCnNameKeyValues = [];
                foreach ($continentCountries as $country) {
                    $countryIdNameKeyValues[$country->id] = $country->name;
                    $countryCodeNameKeyValues[$country->code] = $country->name;
                    $countryIdCnNameKeyValues[$country->id] = $country->name_cn;
                    $countryCodeCnNameKeyValues[$country->code] = $country->name_cn;
                }
                $cache->set('Shop:Region:Country:idNameKeyValues:' . $continent->id, $countryIdNameKeyValues);
                $cache->set('Shop:Region:Country:codeNameKeyValues:' . $continent->id, $countryCodeNameKeyValues);
                $cache->set('Shop:Region:Country:idCnNameKeyValues:' . $continent->id, $countryIdCnNameKeyValues);
                $cache->set('Shop:Region:Country:codeCnNameKeyValues:' . $continent->id, $countryCodeCnNameKeyValues);

                $cache->set('Shop:Region:Country:idNameKeyValues:' . $continent->code, $countryIdNameKeyValues);
                $cache->set('Shop:Region:Country:codeNameKeyValues:' . $continent->code, $countryCodeNameKeyValues);
                $cache->set('Shop:Region:Country:idCnNameKeyValues:' . $continent->code, $countryIdCnNameKeyValues);
                $cache->set('Shop:Region:Country:codeCnNameKeyValues:' . $continent->code, $countryCodeCnNameKeyValues);
            }
        }

        $sql = 'SELECT * FROM shop_region_state';
        $states = $db->getObjects($sql);
        foreach ($states as $state) {
            $key = 'Shop:Region:State:' . $state->id;
            $cache->set($key, $state);
        }

        foreach ($countries as $country) {
            $sql = 'SELECT * FROM shop_region_state WHERE country_id = ?';
            $states = $db->getObjects($sql, [$country->id]);
            if (count($states) > 0) {
                $stateIdNameKeyValues = [];
                $stateIdCnNameKeyValues = [];
                foreach ($states as $state) {
                    $stateIdNameKeyValues[$state->id] = $state->name;
                    $stateIdCnNameKeyValues[$state->id] = $state->name_cn;
                }
                $cache->set('Shop:Region:State:idNameKeyValues:' . $country->id, $stateIdNameKeyValues);
                $cache->set('Shop:Region:State:idCnNameKeyValues:' . $country->id, $stateIdCnNameKeyValues);

                $cache->set('Shop:Region:State:idNameKeyValues:' . $country->code, $stateIdNameKeyValues);
                $cache->set('Shop:Region:State:idCnNameKeyValues:' . $country->code, $stateIdCnNameKeyValues);
            }
        }

    }

}
