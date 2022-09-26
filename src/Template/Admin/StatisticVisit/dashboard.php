<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.ShopFai')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/lib/echarts/5.3.2/echarts.min.js"></script>
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
                <div class="be-fs-125 be-lh-300">访问统计</div>
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
                        <div class="be-fs-110">访问量</div>

                        <div class="be-mt-100">
                            <div id="visit-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="be-col-24 be-md-col-12">
                <div class="be-pl-100">
                    <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">访客</div>

                        <div class="be-mt-100">
                            <div id="visit-unique-user-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col-24 be-md-col-12">
                <div class="be-pr-100">
                    <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">来路分析</div>

                        <div class="be-mt-100">
                            <div id="visit-top-10-referer-chart" style="height: 400px;" v-loading="loading">
                                <template v-if="chartData">

                                    <div class="be-row be-c-999 be-fw-bold">
                                        <div class="be-col-14">
                                            来源域名
                                        </div>
                                        <div class="be-col-10">
                                            次数
                                        </div>
                                    </div>
                                    <div class="be-row be-mt-50 be-bt-eee be-pt-50" v-for="refererData in chartData[4]">
                                        <div class="be-col-14">
                                            {{refererData[0]}}
                                        </div>
                                        <div class="be-col-10">
                                            {{refererData[1]}}
                                            <template v-if="compare && refererData[2] !== undefined && refererData[1] > 0 && refererData[2] > 0 && refererData[1] !== refererData[2]">
                                                <template v-if="refererData[2] > refererData[1]">
                                                    <span class="be-c-red">（下降：{{((refererData[2] - refererData[1]) * 100 / refererData[2]) . toFixed(0)}}%）</span>
                                                </template>
                                                <template v-else>
                                                    <span class="be-c-green">（上升：{{((refererData[1] - refererData[2]) * 100 / refererData[2]) . toFixed(0)}}%）</span>
                                                </template>
                                            </template>
                                        </div>
                                    </div>

                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="be-col-24 be-md-col-12">
                <div class="be-pl-100">
                    <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">国家</div>

                        <div class="be-mt-100">
                            <div id="visit-top-10-country-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="be-row be-mt-200">
            <div class="be-col-24 be-md-col-12">
                <div class="be-pr-100">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">浏览器</div>

                        <div class="be-mt-100">
                            <div id="visit-top-10-browser-chart" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="be-col-24 be-md-col-12">
                <div class="be-pl-100">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">操作系统</div>

                        <div class="be-mt-100">
                            <div id="visit-top-10-os-chart" style="height: 400px;"></div>
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

                visitChart: false,
                visitUniqueUserChart: false,
                visitTop10RefererChart: false,
                visitTop10CountryChart: false,
                visitTop10BrowserChart: false,
                visitTop10OsChart: false,

                chartData: false,

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
                    _this.visitChart.showLoading(_this.chartLoading);
                    _this.visitUniqueUserChart.showLoading(_this.chartLoading);
                    // _this.visitTop10RefererChart.showLoading(_this.chartLoading);
                    _this.visitTop10CountryChart.showLoading(_this.chartLoading);
                    _this.visitTop10BrowserChart.showLoading(_this.chartLoading);
                    _this.visitTop10OsChart.showLoading(_this.chartLoading);

                    _this.$http.post("<?php echo beAdminUrl('ShopFai.Statistic.getReports'); ?>", {
                        formData: {
                            dateRangeType: 'custom',
                            startDate: _this.dateRange[0],
                            endDate: _this.dateRange[1],
                        },
                        reports: [
                            {
                                type: "Visit",
                                name: "getReport"
                            },
                            {
                                type: "Visit",
                                name: "getCount"
                            },
                            {
                                type: "Visit",
                                name: "getUniqueUserReport"
                            },
                            {
                                type: "Visit",
                                name: "getUniqueUserCount"
                            },
                            {
                                type: "Visit",
                                name: "getTop10RefererReport"
                            },
                            {
                                type: "Visit",
                                name: "getTop10CountryReport"
                            },
                            {
                                type: "Visit",
                                name: "getTop10BrowserReport"
                            },
                            {
                                type: "Visit",
                                name: "getTop10OsReport"
                            },
                        ]
                    }).then(function (response) {
                        if (!_this.compare) {

                            _this.loading = false;
                            _this.visitChart.hideLoading();
                            _this.visitUniqueUserChart.hideLoading();
                            //_this.visitTop10RefererChart.hideLoading();
                            _this.visitTop10CountryChart.hideLoading();
                            _this.visitTop10BrowserChart.hideLoading();
                            _this.visitTop10OsChart.hideLoading();
                        }

                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {

                                if (_this.compare) {
                                    _this.chartData = responseData.reports;
                                    _this.loadCompareData();
                                } else {
                                    _this.visitChart.setOption({
                                        title: {
                                            text: "访问量总计：" + responseData.reports[1]
                                        },
                                        dataset: {
                                            source: responseData.reports[0]
                                        }
                                    });

                                    _this.visitUniqueUserChart.setOption({
                                        title: {
                                            text: "唯一访客总计：" + responseData.reports[3]
                                        },
                                        dataset: {
                                            source: responseData.reports[2]
                                        }
                                    });

                                    _this.visitTop10CountryChart.setOption({
                                        dataset: {
                                            source: responseData.reports[5]
                                        }
                                    });

                                    _this.visitTop10BrowserChart.setOption({
                                        dataset: {
                                            source: responseData.reports[6]
                                        }
                                    });

                                    _this.visitTop10OsChart.setOption({
                                        dataset: {
                                            source: responseData.reports[7]
                                        }
                                    });
                                }

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
                        _this.visitChart.hideLoading();
                        _this.visitUniqueUserChart.hideLoading();
                        //_this.visitTop10RefererChart.hideLoading();
                        _this.visitTop10CountryChart.hideLoading();
                        _this.visitTop10BrowserChart.hideLoading();
                        _this.visitTop10OsChart.hideLoading();

                        _this.$message.error(error);
                    });
                },
                loadCompareData: function () {
                    let _this = this;

                    _this.$http.post("<?php echo beAdminUrl('ShopFai.Statistic.getReports'); ?>", {
                        formData: {
                            dateRangeType: 'custom',
                            startDate: _this.compareDateRange[0],
                            endDate: _this.compareDateRange[1],
                        },
                        reports: [
                            {
                                type: "Visit",
                                name: "getReport"
                            },
                            {
                                type: "Visit",
                                name: "getCount"
                            },
                            {
                                type: "Visit",
                                name: "getUniqueUserReport"
                            },
                            {
                                type: "Visit",
                                name: "getUniqueUserCount"
                            },
                            {
                                type: "Visit",
                                name: "getTop10RefererReport"
                            },
                            {
                                type: "Visit",
                                name: "getTop10CountryReport"
                            },
                            {
                                type: "Visit",
                                name: "getTop10BrowserReport"
                            },
                            {
                                type: "Visit",
                                name: "getTop10OsReport"
                            },
                        ]
                    }).then(function (response) {

                        _this.loading = false;
                        _this.visitChart.hideLoading();
                        _this.visitUniqueUserChart.hideLoading();
                        //_this.visitTop10RefererChart.hideLoading();
                        _this.visitTop10CountryChart.hideLoading();
                        _this.visitTop10BrowserChart.hideLoading();
                        _this.visitTop10OsChart.hideLoading();

                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {

                                _this.visitChart.setOption({
                                    title: {
                                        text: "访问量总计：" + _this.chartData[1] + " （上期：" + responseData.reports[1] + "）"
                                    },
                                    dataset: {
                                        source: _this.mergeCompareData(_this.chartData[0], responseData.reports[0])
                                    }
                                });

                                _this.visitUniqueUserChart.setOption({
                                    title: {
                                        text: "唯一访客总计：" + _this.chartData[3] + " （上期：" + responseData.reports[3] + "）"
                                    },
                                    dataset: {
                                        source: _this.mergeCompareData(_this.chartData[2], responseData.reports[2])
                                    }
                                });

                                _this.chartData[4] = _this.mergeIndisciplineCompareData(_this.chartData[4], responseData.reports[4])

                                _this.visitTop10CountryChart.setOption({
                                    dataset: {
                                        source: _this.mergeIndisciplineCompareData(_this.chartData[5], responseData.reports[5])
                                    }
                                });

                                _this.visitTop10BrowserChart.setOption({
                                    dataset: {
                                        source: _this.mergeIndisciplineCompareData(_this.chartData[6], responseData.reports[6])
                                    }
                                });

                                _this.visitTop10OsChart.setOption({
                                    dataset: {
                                        source: _this.mergeIndisciplineCompareData(_this.chartData[7], responseData.reports[7])
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
                        _this.visitChart.hideLoading();
                        _this.visitUniqueUserChart.hideLoading();
                        //_this.visitTop10RefererChart.hideLoading();
                        _this.visitTop10CountryChart.hideLoading();
                        _this.visitTop10BrowserChart.hideLoading();
                        _this.visitTop10OsChart.hideLoading();

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

                // 访问量报表
                this.visitChart = echarts.init(document.getElementById('visit-chart'));
                this.visitChart.setOption({
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

                            let str = params[0].marker + params[0].data[0] + " 访问量：";

                            if (params[0].data[1] === null) {
                                str += "-";
                            } else {
                                str += params[0].data[1];
                            }

                            if (_this.compare) {
                                str += "<br>";
                                str += params[1].marker + params[1].data[3] + " 访问量：";
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

                // 访客报表
                this.visitUniqueUserChart = echarts.init(document.getElementById('visit-unique-user-chart'));
                this.visitUniqueUserChart.setOption({
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
                            let str = params[0].marker + params[0].data[0] + " 访客数：";

                            if (params[0].data[1] === null) {
                                str += "-";
                            } else {
                                str += params[0].data[1];
                            }

                            if (_this.compare) {
                                str += "<br>";
                                str += params[1].marker + params[1].data[3] + " 访客数：";
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

                // 国家
                this.visitTop10CountryChart = echarts.init(document.getElementById('visit-top-10-country-chart'));
                this.visitTop10CountryChart.setOption({
                    title: {
                        right: 20
                    },
                    legend: {
                        orient: 'vertical',
                        left: 0,
                        top: "center",
                    },
                    label: {
                        formatter: function(params) {
                            return params.data[0] + "：" + params.data[1] + "（" + params.percent + "%）"
                        }
                    },
                    tooltip: {
                        formatter: function(params) {
                            let str = params.marker + "<span class='be-fw-bold'>" + params.data[0] + "</span>";
                            str += "<div class='be-row be-mt-100'><div class='be-col'>访问量：</div><div class='be-col-auto'>" + params.data[1] + "</div></div>";
                            str += "<div class='be-row'><div class='be-col'>占比：</div><div class='be-col-auto'>" + params.percent + "%</div></div>"
                            if (_this.compare) {
                                if (params.data[2] !== undefined) {
                                    str += "<div class='be-row'><div class='be-col'>上期访问量：</div><div class='be-col-auto'>" + params.data[2] + "</div></div>"

                                    if (params.data[1] > 0 && params.data[2] > 0 && params.data[1] !== params.data[2]) {
                                        if (params.data[2] > params.data[1]) {
                                            let percent = ((params.data[2] - params.data[1]) * 100 / params.data[2]) . toFixed(0);
                                            str += "<div class='be-row be-c-red'><div class='be-col'>下降：</div><div class='be-col-auto'>" + percent + "%</div></div>"
                                        } else {
                                            let percent = ((params.data[1] - params.data[2]) * 100 / params.data[2]) . toFixed(0);
                                            str += "<div class='be-row be-c-green'><div class='be-col'>上升：</div><div class='be-col-auto'>" + percent + "%</div></div>"
                                        }
                                    }
                                }
                            }
                            return str;
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            center: ['60%', '50%']
                        },
                    ]
                });


                this.visitTop10BrowserChart = echarts.init(document.getElementById('visit-top-10-browser-chart'));
                this.visitTop10BrowserChart.setOption({
                    //legend: {},
                    title: {
                        right: 20
                    },
                    legend: {
                        orient: 'vertical',
                        left: 0,
                        top: "center",
                    },
                    label: {
                        formatter: function(params) {
                            return params.data[0] + "：" + params.data[1] + "（" + params.percent + "%）"
                        }
                    },
                    tooltip: {
                        formatter: function(params) {
                            let str = params.marker + "<span class='be-fw-bold'>" + params.data[0] + "</span>";
                            str += "<div class='be-row be-mt-100'><div class='be-col'>访问量：</div><div class='be-col-auto'>" + params.data[1] + "</div></div>";
                            str += "<div class='be-row'><div class='be-col'>占比：</div><div class='be-col-auto'>" + params.percent + "%</div></div>"
                            if (_this.compare) {
                                if (params.data[2] !== undefined) {
                                    str += "<div class='be-row'><div class='be-col'>上期访问量：</div><div class='be-col-auto'>" + params.data[2] + "</div></div>"

                                    if (params.data[1] > 0 && params.data[2] > 0 && params.data[1] !== params.data[2]) {
                                        if (params.data[2] > params.data[1]) {
                                            let percent = ((params.data[2] - params.data[1]) * 100 / params.data[2]) . toFixed(0);
                                            str += "<div class='be-row be-c-red'><div class='be-col'>下降：</div><div class='be-col-auto'>" + percent + "%</div></div>"
                                        } else {
                                            let percent = ((params.data[1] - params.data[2]) * 100 / params.data[2]) . toFixed(0);
                                            str += "<div class='be-row be-c-green'><div class='be-col'>上升：</div><div class='be-col-auto'>" + percent + "%</div></div>"
                                        }
                                    }
                                }
                            }
                            return str;
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            center: ['60%', '50%']
                        },
                    ]
                });

                this.visitTop10OsChart = echarts.init(document.getElementById('visit-top-10-os-chart'));
                this.visitTop10OsChart.setOption({
                    //legend: {},
                    title: {
                        right: 20
                    },
                    legend: {
                        orient: 'vertical',
                        left: 0,
                        top: "center",
                    },
                    label: {
                        formatter: function(params) {
                            return params.data[0] + "：" + params.data[1] + "（" + params.percent + "%）"
                        }
                    },
                    tooltip: {
                        formatter: function(params) {
                            let str = params.marker + "<span class='be-fw-bold'>" + params.data[0] + "</span>";
                            str += "<div class='be-row be-mt-100'><div class='be-col'>访问量：</div><div class='be-col-auto'>" + params.data[1] + "</div></div>";
                            str += "<div class='be-row'><div class='be-col'>占比：</div><div class='be-col-auto'>" + params.percent + "%</div></div>"
                            if (_this.compare) {
                                if (params.data[2] !== undefined) {
                                    str += "<div class='be-row'><div class='be-col'>上期访问量：</div><div class='be-col-auto'>" + params.data[2] + "</div></div>"

                                    if (params.data[1] > 0 && params.data[2] > 0 && params.data[1] !== params.data[2]) {
                                        if (params.data[2] > params.data[1]) {
                                            let percent = ((params.data[2] - params.data[1]) * 100 / params.data[2]) . toFixed(0);
                                            str += "<div class='be-row be-c-red'><div class='be-col'>下降：</div><div class='be-col-auto'>" + percent + "%</div></div>"
                                        } else {
                                            let percent = ((params.data[1] - params.data[2]) * 100 / params.data[2]) . toFixed(0);
                                            str += "<div class='be-row be-c-green'><div class='be-col'>上升：</div><div class='be-col-auto'>" + percent + "%</div></div>"
                                        }
                                    }
                                }
                            }
                            return str;
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            center: ['60%', '50%']
                        },
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