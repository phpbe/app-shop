<be-head>
    <style>
    </style>
</be-head>



<be-center>
    <div id="app" v-cloak>

        <div class="be-row be-my-80" style="align-items:center;">
            <div class="be-col-auto">
                <div class="be-fs-125 be-py-50"><?php echo $this->title; ?></div>
            </div>

            <div class="be-col"></div>

            <div class="be-col-auto be-pl-100">
                <el-button size="medium" type="primary" @click="createShipping();">添加区域方案</el-button>
            </div>
        </div>


        <?php
        echo $this->tag0('be-page-content');
        ?>
        <div class="be-p-150 be-bc-fff" v-if="shippingList.length === 0">
            <el-empty description="暂无数据"></el-empty>
        </div>

        <div
                class="be-p-150 be-bc-fff be-mb-150"
                v-for="shipping, shippingIndex in shippingList"
        >

            <div class="be-row be-my-80" style="align-items:center;">
                <div class="be-col-auto">
                    <div class="be-fs-125">{{shipping.name}}</div>
                </div>
                <div class="be-col-auto">
                    <div class="be-pl-100 be-c-999">（{{shipping.region_description}}）</div>
                </div>

                <div class="be-col"></div>

                <div class="be-col-auto be-pl-100">
                    <el-link size="medium" type="primary" icon="el-icon-edit" @click="editShipping(shipping);">编辑方案</el-link>
                    <el-link size="medium" type="danger" icon="el-icon-delete" @click="deleteShipping(shipping, shippingIndex);" class="be-ml-100">删除方案</el-link>
                </div>
            </div>


            <el-table
                    class="be-mt-150"
                    ref = "shippingPlanTableRef"
                    :data="shipping.plans">

                <el-table-column label="方案名称" align="left">
                    <template slot-scope="scope">
                        {{scope.row.name}}
                    </template>
                </el-table-column>

                <el-table-column label="运费" align="left">
                    <template slot-scope="scope">
                        <template v-if="scope.row.shipping_fee_type === 'fixed'">
                            {{scope.row.shipping_fee_fixed === '0.00' ? '免运费' : scope.row.shipping_fee_fixed}}
                        </template>
                        <template v-else-if="scope.row.shipping_fee_type === 'weight'">
                            <div>首重：{{scope.row.shipping_fee_first_weight}} {{scope.row.shipping_fee_first_weight_unit}} - <?php echo $this->configStore->currencySymbol; ?> {{scope.row.shipping_fee_first_weight_price}}</div>
                            <div>续重：每 {{scope.row.shipping_fee_additional_weight}} {{scope.row.shipping_fee_additional_weight_unit}} - <?php echo $this->configStore->currencySymbol; ?> {{scope.row.shipping_fee_additional_weight_price}}</div>
                        </template>
                    </template>
                </el-table-column>

                <el-table-column label="下单限制" align="left">
                    <template slot-scope="scope">
                        <template v-if="scope.row.limit === '1'">
                            <template v-if="scope.row.limit_type === 'amount'">
                                订单金额：<?php echo $this->configStore->currencySymbol; ?> {{scope.row.limit_amount_from}} ~ {{scope.row.limit_amount_to === "-1.00" ? "无限" : scope.row.limit_amount_to}}
                            </template>
                            <template v-else-if="scope.row.limit_type === 'quantity'">
                                商品件数：{{scope.row.limit_quantity_from}} ~ {{scope.row.limit_quantity_to === "-1" ? "无限" : scope.row.limit_quantity_to}} 件
                            </template>
                            <template v-else-if="scope.row.limit_type === 'weight'">
                                商品重量：{{scope.row.limit_weight_from}} ~ {{scope.row.limit_weight_to === "-1.00" ? "无限" : scope.row.limit_weight_to}} {{scope.row.limit_weight_unit}}
                            </template>
                        </template>
                        <template v-else>未开启</template>
                    </template>
                </el-table-column>

                <el-table-column label="货到付款" align="center" width="120">
                    <template slot-scope="scope">
                        <el-link :type="scope.row.cod === '1' ? 'success' : 'info'" :icon="scope.row.cod === '1' ? 'el-icon-success' : 'el-icon-error'" :underline="false" style="cursor:auto;font-size:24px;"></el-link>
                    </template>
                </el-table-column>

            </el-table>

        </div>
        <?php
        echo $this->tag1('be-page-content');
        ?>

    </div>

    <script>
        <?php
        foreach ($this->shippingList as $shipping) {
            $shipping->editUrl = beAdminUrl('Shop.Shipping.edit', ['id' => $shipping->id]);
            $shipping->deleteUrl = beAdminUrl('Shop.Shipping.delete', ['id' => $shipping->id]);
        }
        ?>

        let vueCenter = new Vue({
            el: '#app',
            data: {
                loading: false,
                shippingList: <?php echo json_encode($this->shippingList); ?>,
                drawer: {visible: false, width: "40%", title: "", url: "about:blank"},
                t: false
            },
            methods: {
                createShipping: function () {
                    window.location.href = "<?php echo beAdminUrl('Shop.Shipping.create'); ?>";
                },
                editShipping: function (shipping) {
                    window.location.href = shipping.editUrl;
                },
                deleteShipping: function (shipping, shippingindex) {
                    var _this = this;

                    this.$confirm("确认要删除运费方案么？", "操作确认", {
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        type: "warning"
                    }).then(function(){

                        _this.loading = true;
                        _this.$http.get(shipping.deleteUrl).then(function (response) {
                            //console.log(response);
                            _this.loading = false;

                            if (response.status === 200) {
                                var responseData = response.data;
                                if (responseData.success) {
                                    _this.$message.success(responseData.message);
                                    _this.shippingList.splice(shippingindex, 1);
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
                            _this.$message.error(error);
                        });

                    }).catch(function(){});

                },
                t: function () {
                }
            }
        });
    </script>

</be-center>