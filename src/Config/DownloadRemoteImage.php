<?php
namespace Be\App\Shop\Config;

/**
 * @BeConfig("下载远程图像")
 */
class DownloadRemoteImage
{

    /**
     * @BeConfigItem("存放位置根路径",
     *     description="以 / 开头，以 / 结尾",
     *     driver="FormItemInput"
     * )
     */
    public string $rootPath = '/shop/product/auto-download/';

    /**
     * @BeConfigItem("文件夹命名",,
     *     description="转小写，移除特殊字符",
     *     driver="FormItemSelect",
     *     keyValues = "return ['id' => '商品ID', 'url' => '伪静态网址', 'spu' => 'SPU'];")
     * )
     */
    public string $dirname = 'name';

    /**
     * @BeConfigItem("文件命名",
     *     description="远程文件下载到服务器时使用的名称",
     *     driver="FormItemSelect",
     *     keyValues = "return ['original' => '原始名称', 'md5' => '文件哈希（md5）', 'sha1' => '文件哈希（sha1）', 'timestamp' => '时间戳命名'];")
     * )
     */
    public string $fileName = 'md5';

    /**
     * @BeConfigItem("下载间隔最小值（秒）",
     *     description="下载完一张图片后，间隔一段时间再次下载。",
     *     driver="FormItemInputNumberInt"
     * )
     */
    public int $intervalMin = 1;

    /**
     * @BeConfigItem("下载间隔最大值（秒）",
     *     description="最小值和最大值不等时，间隔时间在两者间取随机值",
     *     driver="FormItemInputNumberInt"
     * )
     */
    public int $intervalMax = 3;

    /**
     * @BeConfigItem("重试间隔最小值（秒）",
     *     description="下载失败时，等待些时间后重试",
     *     driver="FormItemInputNumberInt"
     * )
     */
    public int $retryIntervalMin = 1;

    /**
     * @BeConfigItem("重试间隔最大值（秒）",
     *     description="最小值和最大值不等时，重试间隔时间在两者间取随机值",
     *     driver="FormItemInputNumberInt"
     * )
     */
    public int $retryIntervalMax = 3;

    /**
     * @BeConfigItem("重试次数",
     *     driver="FormItemInputNumberInt"
     * )
     */
    public int $retryTimes = 3;

}

