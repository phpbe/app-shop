<?php

namespace Be\App\Shop\Service\Admin;


use Be\Be;

class StatisticVisit extends Statistic
{

    /**
     * 获取访问报表
     *
     * 相当于 SELECT COUNT(*) GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Visit:Report',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,
        ]));
    }

    /**
     * 获取唯一访客访问报表
     *
     * 相当于 SELECT COUNT(DISTINCT(user_id)) GROUP BY create_time
     *
     * @param array $options 参数
     * @return array
     */
    public function getUniqueUserReport(array $options = []): array
    {
        return $this->_getDateHistogram(array_merge($options, [
            'cacheKey' => 'Visit:UniqueUserReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,

            // 按user_id 取唯一
            'cardinality' => 'user_token',
        ]));
    }

    /**
     * 获取 前10来源网址 报表
     *
     * 相当于 SELECT COUNT(*) GROUP BY referer OBDER BY COUNT(*) DESC LIMIT 5
     *
     * @param array $options 参数
     * @return array
     */
    public function getTop10RefererReport(array $options = []): array
    {
        return $this->_getGroup(array_merge($options, [
            'cacheKey' => 'Visit:Top10RefererReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,

            'group' => 'referer',
            'keyValues' => ['' => '直接输入网址'],
            'top' => 10,
        ]));
    }

    /**
     * 获取 前10国家 报表
     *
     * 相当于 SELECT COUNT(*) GROUP BY country_code OBDER BY COUNT(*) DESC LIMIT 5
     *
     * @param array $options 参数
     * @return array
     */
    public function getTop10CountryReport(array $options = []): array
    {
        $keyValues = Be::getService('App.Shop.Region')->getCountryCodeCnNameKeyValues();
        return $this->_getGroup(array_merge($options, [
            'cacheKey' => 'Visit:Top10CountryReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,

            'group' => 'country_code',
            'keyValues' => array_merge(['' => '未知'], $keyValues),
            'top' => 10,
        ]));
    }

    /**
     * 获取 前10浏览器 报表
     *
     * 相当于 SELECT COUNT(*) GROUP BY browser ORDER BY COUNT(*) DESC LIMIT 5
     *
     * @param array $options 参数
     * @return array
     */
    public function getTop10BrowserReport(array $options = []): array
    {
        return $this->_getGroup(array_merge($options, [
            'cacheKey' => 'Visit:Top10BrowserReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,

            'group' => 'browser',
            'top' => 10,
        ]));
    }

    /**
     * 获取 前10操作系统 报表
     *
     * 相当于 SELECT COUNT(*) GROUP BY os_with_version OBDER BY COUNT(*) DESC LIMIT 5
     *
     * @param array $options 参数
     * @return array
     */
    public function getTop10OsReport(array $options = []): array
    {
        return $this->_getGroup(array_merge($options, [
            'cacheKey' => 'Visit:Top10OsReport',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,

            'group' => 'os',
            'top' => 10,
        ]));
    }

    /**
     * 获取总访问量
     *
     * 相当于 SELECT COUNT(*)
     *
     * @param array $options 参数
     * @return int
     */
    public function getCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Visit:Count',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,
        ]));
    }

    /**
     * 获取总唯一访客数
     *
     * 相当于 SELECT COUNT(DISTINCT(user_id))
     *
     * @param array $options 参数
     * @return int
     */
    public function getUniqueUserCount(array $options = []): int
    {
        return $this->_getCount(array_merge($options, [
            'cacheKey' => 'Visit:UniqueUserCount',
            'esIndex' => Be::getConfig('App.Shop.Es')->indexStatisticVisit,

            // 按user_id 取唯一
            'cardinality' => 'user_token',
        ]));
    }


}
