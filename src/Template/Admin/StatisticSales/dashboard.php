<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/lib/echarts/5.3.2/echarts.min.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo $wwwUrl; ?>/admin/statistic-sales/css/conversion-rate.css" />
</be-head>


<be-page-content>
    <?php
    $js = [];
    $css = [];
    $formData = [];
    $vueData = [];
    $vueMethods = [];
    $vueHooks = [];


    $t = time();
    $now = date('Y-m-d H:i:s', $t);

    $today = date('Y-m-d', $t);
    $todayBeginning = date('Y-m-d 00:00:00', $t);
    $todayBeginningTimestamp = strtotime($todayBeginning);
    $todayEnding = date('Y-m-d 23:59:59', $t);
    $todayEndingTimestamp = strtotime($todayEnding);

    $yesterdayEnding = date('Y-m-d H:i:s', $todayBeginningTimestamp - 1);
    $yesterdayEndingTimestamp = strtotime($yesterdayEnding);
    $yesterday = date('Y-m-d', $yesterdayEndingTimestamp);
    ?>
    <div id="app" v-cloak>
        <div class="be-row be-mt-100">
            <div class="be-col-auto">
                <div class="be-fs-125 be-lh-300">销售统计</div>
            </div>
            <div class="be-col-auto">
                <div class="be-pl-100">
                    <el-date-picker
                            v-model="dateRange"
                            type="daterange"
                            size="medium"
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期"
                            :picker-options="pickerOptions"
                            @change="loadData">
                    </el-date-picker>
                </div>
            </div>
            <div class="be-col-auto">
                <div class="be-pl-100 be-lh-300">
                    <el-checkbox v-model="compare" @change="loadData">上期环比</el-checkbox>
                </div>
            </div>
            <div class="be-col-auto" v-if="compare">
                <div class="be-pl-100 be-lh-300">
                    {{compareDateRange[0]}} - {{compareDateRange[1]}}
                </div>
            </div>
        </div>

        <div class="be-row be-mt-100">
            <div class="be-col-24 be-md-col-12">
                <div class="be-pr-100">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">销售额</div>

                        <div class="be-mt-100">
                            <div id="sales-paid-sum-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="be-col-24 be-md-col-12">
                <div class="be-pl-100">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">订单量</div>

                        <div class="be-mt-100">
                            <div id="sales-paid-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col-24 be-md-col-12">
                <div class="be-pr-100">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">平均客单价</div>

                        <div class="be-mt-100">
                            <div id="sales-paid-avg-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="be-col-24 be-md-col-12">
                <div class="be-pl-100">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">&nbsp;</div>

                        <div class="be-mt-100">
                            <div style="height: 400px;">
                                <div class="conversion-rates">
                                    <div class="be-row be-p-100 be-fs-125">
                                        <div class="be-col-auto">
                                            转化率：
                                        </div>
                                        <div class="be-col">
                                            <div class="be-pl-100">{{rate0}}</div>
                                        </div>
                                    </div>

                                    <div class="be-row be-mt-100 be-p-100 be-fs-125 conversion-rate-item">
                                        <div class="be-col-8 be-c-999">
                                            访客数：
                                        </div>
                                        <div class="be-col-8">
                                            {{uniqueUserVisitCount}}
                                        </div>
                                        <div class="be-col-8">
                                            <div class="conversion-rate">
                                                <div class="conversion-rate-top"></div>
                                                <div class="conversion-rate-body">{{rate1}}</div>
                                                <div class="conversion-rate-bottom"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="be-row be-mt-150 be-p-100 be-fs-125 conversion-rate-item">
                                        <div class="be-col-8 be-c-999">
                                            加入购物车：
                                        </div>
                                        <div class="be-col-8">
                                            {{uniqueUserCartCount}}
                                        </div>
                                        <div class="be-col-8">
                                            <div class="conversion-rate">
                                                <div class="conversion-rate-top"></div>
                                                <div class="conversion-rate-body">{{rate2}}</div>
                                                <div class="conversion-rate-bottom"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="be-row be-mt-150 be-p-100 be-fs-125 conversion-rate-item">
                                        <div class="be-col-8 be-c-999">
                                            下单：
                                        </div>
                                        <div class="be-col-8">
                                            {{uniqueUserCheckoutCount}}
                                        </div>
                                        <div class="be-col-8">
                                            <div class="conversion-rate">
                                                <div class="conversion-rate-top"></div>
                                                <div class="conversion-rate-body">{{rate3}}</div>
                                                <div class="conversion-rate-bottom"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="be-row be-mt-150 be-p-100 be-fs-125 conversion-rate-item">
                                        <div class="be-col-8 be-c-999">
                                            付款：
                                        </div>
                                        <div class="be-col-8">
                                            {{uniqueUserPaidCount}}
                                        </div>
                                        <div class="be-col-8">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {

                loading: false,
                chartLoading: {
                    text: '加载中...',
                    color: '#409EFF',
                    textColor: '#409EFF'
                },

                pickerOptions: {
                    disabledDate(time) {
                        return time.getTime() > <?php echo $todayEndingTimestamp; ?> * 1000;
                    },
                    shortcuts: [{
                        text: '今天',
                        onClick(picker) {
                            let start = new Date();
                            let end = new Date();
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '昨天',
                        onClick(picker) {
                            let now = new Date();
                            let start = new Date();
                            let end = new Date();

                            start.setTime(now.getTime() - 86400000);
                            end.setTime(now.getTime() - 86400000);

                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '过去7天',
                        onClick(picker) {
                            let now = new Date();
                            let start = new Date();
                            let end = new Date();

                            start.setTime(now.getTime() - 86400000 * 7);
                            end.setTime(now.getTime() - 86400000);

                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '过去30天',
                        onClick(picker) {
                            let now = new Date();
                            let start = new Date();
                            let end = new Date();

                            start.setTime(now.getTime() - 86400000 * 30);
                            end.setTime(now.getTime() - 86400000);

                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },

                dateRange: ["<?php echo $today; ?>", "<?php echo $today; ?>"],
                compareDateRange: ["<?php echo $yesterday; ?>", "<?php echo $yesterday; ?>"],
                compare: true,

                salesPaidSumChart: false,
                salesPaidChart: false,
                salesPaidAvgChart: false,

                chartData: false,

                uniqueUserVisitCount: 0,
                uniqueUserCartCount: 0,
                uniqueUserCheckoutCount: 0,
                uniqueUserPaidCount: 0,

                rate0: "0.00%",
                rate1: "0.00%",
                rate2: "0.00%",
                rate3: "0.00%",

                t: false
                <?php
                if ($vueData) {
                    foreach ($vueData as $k => $v) {
                        echo ',' . $k . ':' . json_encode($v);
                    }
                }
                ?>
            },
            methods: {
                loadData: function () {
                    let _this = this;

                    _this.updateCompareDateRange();

                    _this.loading = true;
                    _this.salesPaidSumChart.showLoading(_this.chartLoading);
                    _this.salesPaidChart.showLoading(_this.chartLoading);
                    _this.salesPaidAvgChart.showLoading(_this.chartLoading);

                    _this.$http.post("<?php echo beAdminUrl('Shop.Statistic.getReports'); ?>", {
                        formData: {
                            dateRangeType: 'custom',
                            startDate: _this.dateRange[0],
                            endDate: _this.dateRange[1],
                        },
                        reports: [
                            {
                                type: "Sales",
                                name: "getPaidSumReport"
                            },
                            {
                                type: "Sales",
                                name: "getPaidSum"
                            },
                            {
                                type: "Sales",
                                name: "getReport"
                            },
                            {
                                type: "Sales",
                                name: "getCount"
                            },
                            {
                                type: "Sales",
                                name: "getPaidAvgReport"
                            },
                            {
                                type: "Sales",
                                name: "getPaidAvg"
                            },
                            {
                                type: "Visit",
                                name: "getUniqueUserCount"
                            },
                            {
                                type: "Cart",
                                name: "getUniqueUserCount"
                            },
                            {
                                type: "Sales",
                                name: "getUniqueUserCount"
                            },
                            {
                                type: "Sales",
                                name: "getUniqueUserPaidCount"
                            }
                        ]
                    }).then(function (response) {
                        if (!_this.compare) {
                            _this.loading = false;
                            _this.salesPaidSumChart.hideLoading();
                            _this.salesPaidChart.hideLoading();
                            _this.salesPaidAvgChart.hideLoading();
                        }

                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {

                                if (_this.compare) {
                                    _this.chartData = responseData.reports;
                                    _this.loadCompareData();
                                } else {
                                    _this.salesPaidSumChart.setOption({
                                        title: {
                                            text: "销售额总计：<?php echo $this->configStore->currencySymbol; ?>" + responseData.reports[1]
                                        },
                                        dataset: {
                                            source: responseData.reports[0]
                                        }
                                    });

                                    _this.salesPaidChart.setOption({
                                        title: {
                                            text: "订单量总计：" + responseData.reports[3]
                                        },
                                        dataset: {
                                            source: responseData.reports[2]
                                        }
                                    });

                                    _this.salesPaidAvgChart.setOption({
                                        title: {
                                            text: "平均客单价：<?php echo $this->configStore->currencySymbol; ?>" + responseData.reports[5]
                                        },
                                        dataset: {
                                            source: responseData.reports[4]
                                        }
                                    });
                                }

                                _this.uniqueUserVisitCount = responseData.reports[6];
                                _this.uniqueUserCartCount = responseData.reports[7];
                                _this.uniqueUserCheckoutCount = responseData.reports[8];
                                _this.uniqueUserPaidCount = responseData.reports[9];

                                _this.updateConversionRate();

                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                } else {
                                    _this.$message.error("服务器返回数据异常！");
                                }
                            }

                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.salesPaidSumChart.hideLoading();
                        _this.salesPaidChart.hideLoading();
                        _this.salesPaidAvgChart.hideLoading();
                        _this.$message.error(error);
                    });
                },
                loadCompareData: function () {
                    let _this = this;

                    _this.$http.post("<?php echo beAdminUrl('Shop.Statistic.getReports'); ?>", {
                        formData: {
                            dateRangeType: 'custom',
                            startDate: _this.compareDateRange[0],
                            endDate: _this.compareDateRange[1],
                        },
                        reports: [
                            {
                                type: "Sales",
                                name: "getPaidSumReport"
                            },
                            {
                                type: "Sales",
                                name: "getPaidSum"
                            },
                            {
                                type: "Sales",
                                name: "getReport"
                            },
                            {
                                type: "Sales",
                                name: "getCount"
                            },
                            {
                                type: "Sales",
                                name: "getPaidAvgReport"
                            },
                            {
                                type: "Sales",
                                name: "getPaidAvg"
                            },
                        ]
                    }).then(function (response) {

                        _this.loading = false;
                        _this.salesPaidSumChart.hideLoading();
                        _this.salesPaidChart.hideLoading();
                        _this.salesPaidAvgChart.hideLoading();

                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {

                                _this.salesPaidSumChart.setOption({
                                    title: {
                                        text: "销售额总计：<?php echo $this->configStore->currencySymbol; ?>" + _this.chartData[1] + " （上期：<?php echo $this->configStore->currencySymbol; ?>" + responseData.reports[1] + "）"
                                    },
                                    dataset: {
                                        source: _this.mergeCompareData(_this.chartData[0], responseData.reports[0])
                                    }
                                });

                                _this.salesPaidChart.setOption({
                                    title: {
                                        text: "订单量总计：" + responseData.reports[3] + " （上期：" + responseData.reports[3] + "）"
                                    },
                                    dataset: {
                                        source: _this.mergeCompareData(_this.chartData[2], responseData.reports[2])
                                    }
                                });

                                _this.salesPaidAvgChart.setOption({
                                    title: {
                                        text: "平均客单价：<?php echo $this->configStore->currencySymbol; ?>" + responseData.reports[5] + " （上期：<?php echo $this->configStore->currencySymbol; ?>" + responseData.reports[5] + "）"
                                    },
                                    dataset: {
                                        source: _this.mergeCompareData(_this.chartData[4], responseData.reports[4])
                                    }
                                });

                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                } else {
                                    _this.$message.error("服务器返回数据异常！");
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.salesPaidSumChart.hideLoading();
                        _this.salesPaidChart.hideLoading();
                        _this.salesPaidAvgChart.hideLoading();

                        _this.$message.error(error);
                    });
                },
                updateCompareDateRange() {
                    let startDate = new Date(this.dateRange[0])
                    let endDate = new Date(this.dateRange[1]);
                    let startDateTimestamp = startDate.getTime();
                    let endDateTimestamp = endDate.getTime();

                    let compareEndDateTimestamp = startDateTimestamp - 86400000;
                    let compareStartDateTimestamp = compareEndDateTimestamp - (endDateTimestamp - startDateTimestamp);

                    let compareStartDate = new Date(compareStartDateTimestamp);
                    let compareEndDate = new Date(compareEndDateTimestamp);

                    this.compareDateRange = [this.formatDate(compareStartDate), this.formatDate(compareEndDate)];
                },
                formatDate(date) {
                    let y = date.getFullYear();
                    let m = date.getMonth() + 1;
                    let d = date.getDate();

                    let str = y + "-";
                    if (m < 10) str += "0";
                    str += m + "-";
                    if (d < 10) str += "0";
                    str += d;
                    return str;
                },
                mergeCompareData(chartData, compareChartData) { // 将严格匹配的报表数据进行合并
                    let len = chartData.length;
                    for (let i=0; i<len; i++) {
                        if (chartData[i][1] === undefined) {
                            chartData[i][1] = null;
                        }

                        chartData[i][2] = compareChartData[i][1];
                        chartData[i][3] = compareChartData[i][0];
                    }
                    return chartData;
                },
                mergeIndisciplineCompareData(chartData, compareChartData) { // 将不是严格匹配的报表对比数据合并， 如国家
                    let len1 = chartData.length;
                    let len2 = compareChartData.length;
                    for (let i=0; i<len1; i++) {
                        for (let j=0; j<len2; j++) {
                            if (chartData[i][0] === compareChartData[j][0]) {
                                chartData[i][2] = compareChartData[j][1];
                                break;
                            }
                        }
                    }
                    return chartData;
                },
                updateConversionRate() {
                    if (this.uniqueUserVisitCount > 0) {
                        this.rate0 = ((this.uniqueUserPaidCount / this.uniqueUserVisitCount) * 100).toFixed(2) + "%";
                        this.rate1 = ((this.uniqueUserCartCount / this.uniqueUserVisitCount) * 100).toFixed(2) + "%";
                    } else {
                        this.rate0 = "0.00%";
                        this.rate1 = "0.00%";
                    }

                    if (this.uniqueUserCartCount > 0) {
                        this.rate2 = ((this.uniqueUserCheckoutCount / this.uniqueUserCartCount) * 100).toFixed(2) + "%";
                    } else {
                        this.rate2 = "0.00%";
                    }

                    if (this.uniqueUserCheckoutCount > 0) {
                        this.rate3 = ((this.uniqueUserPaidCount / this.uniqueUserCheckoutCount) * 100).toFixed(2) + "%";
                    } else {
                        this.rate3 = "0.00%";
                    }
                },
                yAxisMax(max) {
                    if (isNaN(max)) {
                        return 5;
                    }

                    let newMax = Math.ceil(max * 1.2);
                    if (newMax < 5) {
                        return 5;
                    } else if (newMax < 10) {
                        return 10;
                    }

                    let mod = Math.pow(10, newMax.toString().length - 1);
                    newMax = newMax + (mod  - newMax % mod);
                    return newMax;
                }
                <?php
                if ($vueMethods) {
                    foreach ($vueMethods as $k => $v) {
                        echo ',' . $k . ':' . $v;
                    }
                }
                ?>
            },
            created: function () {
                <?php
                if (isset($vueHooks['created'])) {
                    echo $vueHooks['created'];
                }
                ?>
            },
            mounted: function () {
                let _this = this;

                this.salesPaidSumChart = echarts.init(document.getElementById('sales-paid-sum-chart'));
                this.salesPaidSumChart.setOption({
                    title: {
                        right: 20
                    },
                    grid: {
                        left: 20,
                        right: 20,
                        top: 40,
                        bottom: 40
                    },
                    tooltip: {
                        trigger: "axis",
                        formatter: function(params) {
                            let str = params[0].marker + params[0].data[0] + " 销售额：";
                            if (params[0].data[1] === null) {
                                str += "-";
                            } else {
                                str += "<?php echo $this->configStore->currencySymbol; ?>" + params[0].data[1].toFixed(2);
                            }

                            if (_this.compare) {
                                str += "<br>";
                                str += params[1].marker + params[1].data[3] + " 销售额：";
                                str += "<?php echo $this->configStore->currencySymbol; ?>" + params[1].data[2].toFixed(2);
                            }
                            return str;
                        }
                    },
                    color: [
                        "#409EFF",
                        "#CCCCCC"
                    ],
                    xAxis: {
                        type: 'category'
                    },
                    yAxis: {
                        type: 'value',
                        minInterval: 1,
                        max: function (value)  {
                            return _this.yAxisMax(value.max);
                        }
                    },
                    series: [
                        {
                            type: 'line',
                            z: 2,
                            lineStyle: {
                                width: 3
                            }
                        },
                        {
                            type: 'line',
                            z: 1,
                            lineStyle: {
                                width: 3
                            }
                        }
                    ]
                });

                this.salesPaidChart = echarts.init(document.getElementById('sales-paid-chart'));
                this.salesPaidChart.setOption({
                    title: {
                        right: 20
                    },
                    grid: {
                        left: 20,
                        right: 20,
                        top: 40,
                        bottom: 40
                    },
                    tooltip: {
                        trigger: "axis",
                        formatter: function(params) {
                            let str = params[0].marker + params[0].data[0] + " 订单量：";

                            if (params[0].data[1] === null) {
                                str += "-";
                            } else {
                                str += params[0].data[1];
                            }

                            if (_this.compare) {
                                str += "<br>";
                                str += params[1].marker + params[1].data[3] + " 订单量：";
                                str += params[1].data[2];
                            }

                            return str;
                        }
                    },
                    color: [
                        "#409EFF",
                        "#CCCCCC"
                    ],
                    xAxis: {
                        type: 'category',
                    },
                    yAxis: {
                        type: 'value',
                        minInterval: 1,
                        max: function (value)  {
                            return _this.yAxisMax(value.max);
                        }
                    },
                    series: [
                        {
                            type: 'line',
                            z: 2,
                            lineStyle: {
                                width: 3
                            }
                        },
                        {
                            type: 'line',
                            z: 1,
                            lineStyle: {
                                width: 3
                            }
                        }
                    ]
                });

                this.salesPaidAvgChart = echarts.init(document.getElementById('sales-paid-avg-chart'));
                this.salesPaidAvgChart.setOption({
                    title: {
                        right: 20
                    },
                    grid: {
                        left: 20,
                        right: 20,
                        top: 40,
                        bottom: 40
                    },
                    tooltip: {
                        trigger: "axis",
                        formatter: function(params) {
                            let str = params[0].marker + params[0].data[0] + " 平均客单价：";

                            if (params[0].data[1] === null) {
                                str += "-";
                            } else {
                                str += "<?php echo $this->configStore->currencySymbol; ?>" + params[0].data[1];
                            }

                            if (_this.compare) {
                                str += "<br>";
                                str += params[1].marker + params[1].data[3] + " 平均客单价：";
                                str += "<?php echo $this->configStore->currencySymbol; ?>" + params[1].data[2];
                            }

                            return str;
                        }
                    },
                    color: [
                        "#409EFF",
                        "#CCCCCC"
                    ],
                    xAxis: {
                        type: 'category',
                    },
                    yAxis: {
                        type: 'value',
                        minInterval: 1,
                        max: function (value)  {
                            return _this.yAxisMax(value.max);
                        }
                    },
                    series: [
                        {
                            type: 'line',
                            z: 2,
                            lineStyle: {
                                width: 3
                            }
                        },
                        {
                            type: 'line',
                            z: 1,
                            lineStyle: {
                                width: 3
                            }
                        }
                    ]
                });

                this.loadData();
                <?php
                if (isset($vueHooks['mounted'])) {
                    echo $vueHooks['mounted'];
                }
                ?>
            },
            updated: function () {
                <?php
                if (isset($vueHooks['updated'])) {
                    echo $vueHooks['updated'];
                }
                ?>
            }
            <?php
            if (isset($vueHooks['beforeCreate'])) {
                echo ',beforeCreate: function () {' . $vueHooks['beforeCreate'] . '}';
            }

            if (isset($vueHooks['beforeMount'])) {
                echo ',beforeMount: function () {' . $vueHooks['beforeMount'] . '}';
            }

            if (isset($vueHooks['beforeUpdate'])) {
                echo ',beforeUpdate: function () {' . $vueHooks['beforeUpdate'] . '}';
            }

            if (isset($vueHooks['beforeDestroy'])) {
                echo ',beforeDestroy: function () {' . $vueHooks['beforeDestroy'] . '}';
            }

            if (isset($vueHooks['destroyed'])) {
                echo ',destroyed: function () {' . $vueHooks['destroyed'] . '}';
            }
            ?>
        });
    </script>
</be-page-content>