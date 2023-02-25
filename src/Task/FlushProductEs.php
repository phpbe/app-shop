<?php
namespace Be\App\Shop\Task;

use Be\Be;
use Be\Task\Task;

/**
 * 清空商品ES数据
 *
 * @BeTask("清空商品ES数据")
 */
class FlushProductEs extends Task
{

    public function execute()
    {
        $configSystemEs = Be::getConfig('App.System.Es');
        if ($configSystemEs->enable === 0) {
            return;
        }

        $config = Be::getConfig('App.Shop.Es');
        $es = Be::getEs();
        $query = [
            'index' => $config->indexProduct,
            'body' => [
                'query' => [
                    'match_all' => [
                        'boost' => 1.0
                    ]
                ]
            ]
        ];
        $es->deleteByQuery($query);
    }


}
