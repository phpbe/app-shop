<be-head>

    <style>
        .el-form-item {
            margin-bottom: inherit;
        }

        .el-form-item__content {
            line-height: inherit;
        }

        .el-tooltip {
            cursor: pointer;
        }

        .el-tooltip:hover {
            color: #409EFF;
        }

        .dialog-image-selector .el-dialog__body {
            padding: 0;
        }

    </style>
</be-head>

<be-north>
    <div class="be-north" id="be-north">
        <div style="padding: 1.25rem 0 0 2rem;">
            <el-link icon="el-icon-back" href="<?php echo beAdminUrl('Shop.PromotionCoupon.coupons'); ?>">返回优惠券列表</el-link>
        </div>
    </div>
    <script>
        let vueNorth = new Vue({el: '#be-north'});
    </script>
</be-north>


<be-page-content>
    <div id="app" v-cloak>
        <div class="be-row">
            <div class="be-col-16 be-pr-100">
                 <div class="be-p-150 be-bc-fff">
                    <div class="be-fs-120 be-fw-bold">
                        已付款
                    </div>

                    <div class="be-row be-mt-150">
                        <div class="be-col-6">
                            <div class="be-c-999">订单数</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->statistics['paidOrderCount']; ?></div>
                        </div>
                        <div class="be-col-6">
                            <div class="be-c-999">订单金额</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->configStore->currencySymbol . $this->statistics['paidOrderTotalAmount']; ?></div>
                        </div>
                        <div class="be-col-6">
                            <div class="be-c-999">优惠</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->configStore->currencySymbol . $this->statistics['paidOrderDiscountAmount']; ?></div>
                        </div>
                        <div class="be-col-6">
                            <div class="be-c-999">客单价</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->configStore->currencySymbol . $this->statistics['paidOrderAvgAmount']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="be-col-8 be-pl-100">
                 <div class="be-p-150 be-bc-fff">
                    <div class="be-fs-120 be-fw-bold">
                        未付款
                    </div>

                    <div class="be-row be-mt-150">
                        <div class="be-col-6">
                            <div class="be-c-999">订单数</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->statistics['unpaidOrderCount']; ?></div>
                        </div>
                        <div class="be-col-6">
                            <div class="be-c-999">订单金额</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->configStore->currencySymbol . $this->statistics['unpaidOrderTotalAmount']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="be-p-150 be-bc-fff be-mt-200">
            <div class="be-fs-120 be-fw-bold">
                订单明细
            </div>

            <el-table
                    class="be-mt-100"
                    ref = "scopeOrderTableRef"
                    :data="paidOrders">

                <template slot="empty">
                    <el-empty description="暂无数据"></el-empty>
                </template>

                <el-table-column
                        prop="order_sn"
                        label="订单号"
                        align="left">
                </el-table-column>

                <el-table-column
                        prop="email"
                        label="买家邮箱"
                        align="left">
                </el-table-column>

                <el-table-column
                        prop="amount"
                        label="订单金额"
                        align="center"
                        width="180">
                </el-table-column>

                <el-table-column
                        prop="pay_time"
                        label="付款时间"
                        align="center"
                        width="180">
                </el-table-column>

                <el-table-column
                        prop="create_time"
                        label="下单时间"
                        align="center"
                        width="180">
                </el-table-column>

            </el-table>
        </div>

    </div>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                loading: false,
                paidOrders: <?php echo json_encode($this->statistics['paidOrders']); ?>,
                t: false
            },
            methods: {
            }
        });
    </script>
</be-page-content>