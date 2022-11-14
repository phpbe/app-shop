<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/lib/echarts/5.3.2/echarts.min.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo $wwwUrl; ?>/admin/statistic-sales/css/conversion-rate.css" />
</be-head>

<be-page-title>
    <?php
    $now = date('Y-m-d H:i:s');
    $storeNow = \Be\Util\Time\Timezone::convert('Y-m-d H:i', $this->configStore->timezone, $now);
    ?>
    店铺时间：<?php echo $storeNow; ?>（时区：<?php echo $this->configStore->timezone; ?>）
</be-page-title>

<be-page-content>
    <div id="app" v-cloak>
        <div class="be-row">
            <div class="be-col-24 be-md-col-18">
                 <div class="be-p-150 be-bc-fff">
                    <div class="be-fs-110 be-pl-100">今日数据</div>

                    <div class="be-row be-mt-100">
                        <div class="be-col">
                            <div class="be-p-100">
                                <div class="be-p-100" style="background-color: #fafafa;">
                                    <div class="be-fs-300 be-c-blue"><i class="el-icon-money"></i></div>
                                    <div class="be-mt-50 be-fs-120">销售额</div>
                                    <div class="be-mt-100 be-fs-200"><?php echo $this->configStore->currencySymbol . $this->todaySalesPaidSum; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="be-col">
                            <div class="be-p-100">
                                <div class="be-p-100" style="background-color: #fafafa;">
                                    <div class="be-fs-300 be-c-blue"><i class="el-icon-s-order"></i></div>
                                    <div class="be-mt-50 be-fs-120">订单量</div>
                                    <div class="be-mt-100 be-fs-200"><?php echo $this->todaySalesPaidCount; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="be-col">
                            <div class="be-p-100">
                                <div class="be-p-100" style="background-color: #fafafa;">
                                    <div class="be-fs-300 be-c-blue"><i class="el-icon-s-custom"></i></div>
                                    <div class="be-mt-50 be-fs-120">访客数</div>
                                    <div class="be-mt-100 be-fs-200"><?php echo $this->todayVisitUniqueUserCount; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="be-col-24 be-md-col-6 be-pl-150">
                 <div class="be-p-150 be-bc-fff">
                    <div class="be-fs-110 be-pl-100">待办事项</div>

                    <div class="be-mt-100">
                        <div class="be-p-100">
                            <div class="be-p-100" style="background-color: #fafafa;">
                                <div class="be-fs-300 be-c-blue"><i class="el-icon-s-claim"></i></div>
                                <div class="be-mt-50 be-fs-120">待发货订单</div>
                                <div class="be-mt-100 be-fs-200"><?php echo $this->todaySalesPaidNotShippedCount; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="be-mt-150">
            <div class="be-p-150 be-bc-fff" style="padding-top: .5rem;">
                <el-tabs v-model="uniqueUserVisitChartDateRangeType" @tab-click="loadUniqueVisitorChartData">
                    <el-tab-pane label="今天" name="today"></el-tab-pane>
                    <el-tab-pane label="昨天" name="yesterday"></el-tab-pane>
                    <el-tab-pane label="过去7天" name="last_7_days"></el-tab-pane>
                    <el-tab-pane label="过去30天" name="last_30_days"></el-tab-pane>
                </el-tabs>

                <div class="be-row be-mt-100">
                    <div class="be-col-12">
                        <div id="unique-visitor-chart" style="height: 400px;"></div>
                    </div>
                    <div class="be-col-1"></div>
                    <div class="be-col-11 conversion-rates">
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
    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                chartLoading: {
                    text: '加载中...',
                    color: '#409EFF',
                    textColor: '#409EFF'
                },

                uniqueUserVisitChart: false,
                uniqueUserVisitChartDateRangeType: "today",

                uniqueUserVisitCount: 0,
                uniqueUserCartCount: 0,
                uniqueUserCheckoutCount: 0,
                uniqueUserPaidCount: 0,

                rate0: "0.00%",
                rate1: "0.00%",
                rate2: "0.00%",
                rate3: "0.00%",

                t: false
            },
            methods: {
                loadUniqueVisitorChartData: function () {
                    let _this = this;
                    _this.uniqueUserVisitChart.showLoading(_this.chartLoading);

                    _this.$http.post("<?php echo beAdminUrl('Shop.Statistic.getReports'); ?>", {
                        formData: {
                            dateRangeType: _this.uniqueUserVisitChartDateRangeType
                        },
                        reports: [
                            {
                                type: "Visit",
                                name: "getUniqueUserReport"
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
                        _this.uniqueUserVisitChart.hideLoading();

                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {

                                _this.uniqueUserVisitCount = responseData.reports[1];
                                _this.uniqueUserCartCount = responseData.reports[2];
                                _this.uniqueUserCheckoutCount = responseData.reports[3];
                                _this.uniqueUserPaidCount = responseData.reports[4];

                                _this.uniqueUserVisitChart.setOption({
                                    title: {
                                        text: "唯一访客总计：" + responseData.reports[1]
                                    },
                                    dataset: {
                                        source: responseData.reports[0]
                                    }
                                });

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
                        _this.uniqueUserVisitChart.hideLoading();
                        _this.$message.error(error);
                    });
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

                this.uniqueUserVisitChart = echarts.init(document.getElementById('unique-visitor-chart'));
                this.uniqueUserVisitChart.setOption({
                    //legend: {},
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
                            if (params[0].data[1] === undefined) {
                                str += "-";
                            } else {
                                str += params[0].data[1];
                            }
                            return str;
                        }
                    },
                    color: [
                        "#409EFF"
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
                            lineStyle: {
                                width: 3
                            }
                        }
                    ]
                });
                this.loadUniqueVisitorChartData();
            },
        });
    </script>
</be-page-content>