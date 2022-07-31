<be-page-content>
    <div id="app" v-cloak>
        <div class="be-p-150 be-bc-fff">
            <div class="be-row be-lh-250 be-bb be-pb-50">
                <div class="be-col-auto">接口开关：</div>
                <div class="be-col-auto be-px-100">
                    <el-switch v-model.number="formData.enable" :active-value="1" :inactive-value="0" size="medium" @change="toggleEnable"></el-switch>
                </div>
            </div>

            <div class="be-row be-lh-250 be-mt-50 be-bb be-pb-50">
                <div class="be-col-auto">接口密钥：</div>
                <div class="be-col-auto be-px-100">
                    <?php echo $this->config->token; ?>
                </div>
                <div class="be-col-auto">
                    <el-link type="primary" icon="el-icon-refresh" :underline="false" href="<?php echo beAdminUrl('ShopFai.CollectProductApi.resetToken'); ?>">重新生成</el-link>
                </div>
            </div>


            <div class="be-row be-lh-250  be-mt-50 be-bb be-pb-50">
                <div class="be-col-auto">接口网址：</div>
                <div class="be-col-auto be-px-100">
                    <el-tag>
                        <?php echo beUrl('ShopFai.Api.CollectProduct', ['token' => $this->config->token]); ?>
                    </el-tag>
                </div>

                <div class="be-col-auto">
                    <el-link type="primary" icon="el-icon-document-copy" :underline="false" @click="copyUrl">复制</el-link>
                </div>
            </div>

            <div class="be-lh-250 be-mt-50">接口POST数据字段说明：</div>
            <div class="be-mt-50">

                <el-table
                        :data="tableData"
                        border
                        style="width: 100%">
                    <el-table-column
                            prop="name"
                            label="字段名"
                            width="180">
                    </el-table-column>
                    <el-table-column
                            prop="required"
                            label="是否必传"
                            align="center"
                            width="180">
                        <template slot-scope="scope">
                            <el-link v-if="scope.row.required === 1" type="success" icon="el-icon-success" style="cursor:auto;font-size:24px;"></el-link>
                            <el-link v-else type="info" icon="el-icon-error" style="cursor:auto;font-size:24px;color:#bbb;"></el-link>
                        </template>
                    </el-table-column>
                    <el-table-column
                            prop="description"
                            label="说明">
                    </el-table-column>
                </el-table>


            </div>

        </div>
    </div>
    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                formData : {
                    enable: <?php echo $this->config->enable; ?>
                },
                tableData: [
                    {
                        "name" : "unique_key",
                        "required" : 0,
                        "description" : "唯一值，有传值时可用于去重，可取采集的网址，标题、SKU等，未传值时不校验是否复复导入，200个字符以内"
                    },
                    {
                        "name" : "name",
                        "required" : 1,
                        "description" : "商品名称，200个字符以内"
                    },
                    /*
                    {
                        "name" : "summary",
                        "required" : 0,
                        "description" : "摘要，500个字符以内"
                    },
                     */
                    {
                        "name" : "description",
                        "required" : 0,
                        "description" : "描述"
                    },
                    {
                        "name" : "images",
                        "required" : 0,
                        "description" : "主图网址，多个主图用 \"|\" 分隔开"
                    },
                    {
                        "name" : "categories",
                        "required" : 0,
                        "description" : "分类，多个分类用 \"|\" 分隔开，单个分类名称120个字符以内，分类不存在时将自动创建"
                    },
                    {
                        "name" : "tags",
                        "required" : 0,
                        "description" : "标签，多个标签用 \"|\" 分隔开，单个标签60个字符以内"
                    },
                    {
                        "name" : "brand",
                        "required" : 0,
                        "description" : "品牌，60个字符以内"
                    },
                    {
                        "name" : "relate_key",
                        "required" : 0,
                        "description" : "商品关联唯一值，相同关联唯一值的商品将自动关联起来，120个字符以内"
                    },
                    {
                        "name" : "relate_name",
                        "required" : 0,
                        "description" : "商品关联名称，例：Color，120个字符以内"
                    },
                    {
                        "name" : "relate_value",
                        "required" : 0,
                        "description" : "商品关联的值，例：Red，120个字符以内"
                    },
                    {
                        "name" : "relate_icon_image",
                        "required" : 0,
                        "description" : "商品关联的图标 - 图片，网址，120个字符以内，例：https://cdn.iamdtc.com/red.jpg"
                    },
                    {
                        "name" : "relate_icon_color",
                        "required" : 0,
                        "description" : "商品关联的图标 - 色块，16进制颜色，7个字符，例：#FF0000"
                    },
                    {
                        "name" : "style[x]",
                        "required" : 0,
                        "description" : "多款式，可传多个，名称中中括号内为款式名，例：style[Color]，款式值用 \"|\" 分隔开，例：Red|Green|Blue"
                    },
                    {
                        "name" : "sku",
                        "required" : 0,
                        "description" : "SKU，60个字符以内"
                    },
                    {
                        "name" : "barcode",
                        "required" : 0,
                        "description" : "条码，60个字符以内"
                    },
                    {
                        "name" : "price",
                        "required" : 0,
                        "description" : "价格，2位小数，例：123.45，默认值：0.00"
                    },
                    {
                        "name" : "original_price",
                        "required" : 0,
                        "description" : "原价，2位小数，例：123.45，默认值：0.00"
                    },
                    {
                        "name" : "weight",
                        "required" : 0,
                        "description" : "重量，3位小数，例：1.234，默认值：0.000"
                    },
                    {
                        "name" : "weight_unit",
                        "required" : 0,
                        "description" : "重量单位，可取值：kg/g/lb/oz，默认值：kg"
                    },
                    {
                        "name" : "stock",
                        "required" : 0,
                        "description" : "库存，大于0时该商品启用跟踪库存，默认值：0"
                    },
                ]
            },
            methods: {
                toggleEnable() {
                    let _this = this;
                    _this.$http.get("<?php echo beAdminUrl('ShopFai.CollectProductApi.toggleEnable'); ?>", {
                        formData: _this.formData
                    }).then(function (response) {
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.$message.success(responseData.message);
                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                } else {
                                    _this.$message.error("服务器返回数据异常！");
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.$message.error(error);
                    });
                },
                copyUrl: function () {
                    let _this = this;
                    let input = document.createElement('input');
                    input.value = "<?php echo beUrl('ShopFai.Api.CollectProduct', ['token' => $this->config->token]); ?>";
                    document.body.appendChild(input);
                    input.select();
                    try {
                        document.execCommand('Copy');
                        _this.$message.success("接口网址已复制！");
                    } catch {
                    }
                    document.body.removeChild(input);
                }
            }
        });
    </script>
</be-page-content>