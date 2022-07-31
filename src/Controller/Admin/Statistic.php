<?php

namespace Be\App\ShopFai\Controller\Admin;

use Be\App\System\Controller\Admin\Auth;
use Be\Be;

/**
 * 访问统计
 *
 * @BePermissionGroup("分析",  ordering="6")
 */
class Statistic extends Auth
{

    /**
     * 批量获取报表
     *
     * @BePermission("获取报表数据", ordering="6.0")
     */
    public function getReports()
    {
        $request = Be::getRequest();
        $response = Be::getResponse();

        $formData = $request->json('formData');
        $reports = $request->json('reports');

        $returnReports = [];
        if (is_array($reports)) {
            foreach ($reports as $report) {
                if (!isset($report['type']) || !in_array($report['type'], ['Visit', 'Sales', 'Cart']) || !isset($report['name'])) {
                    $response->error('报告参数错误！');
                    return;
                }

                $name = $report['name'];
                $reportFormData = null;
                if (isset($report['formData'])) {
                    $reportFormData = $report['formData'];
                } else {
                    $reportFormData = $formData;
                }

                $returnReports[] = Be::getService('App.ShopFai.Admin.Statistic' . $report['type'])->$name($reportFormData);
            }
        }

        $response->set('success', true);
        $response->set('reports', $returnReports);
        $response->json();
    }


}
