<be-head>
    <style>
        .el-tree-node__content {
            height: 3.5rem;
            line-height: 3.5rem;
        }

        .region-country-flag {
            height: 3rem;
            line-height: 3.5rem;
        }

        .region-country-flag img {
            width: 3rem;
            vertical-align: middle;
        }

        .region-countries {

        }

        .region-countries img {
            width: 3rem;
            vertical-align: middle;
        }

        .currency-tag {
            height: 2.5rem;
            line-height: 2.5rem;
        }


        .region-plan-form .el-form-item {
            margin-bottom: 0;
        }

        .region-plan-form .el-form-item.is-error {
            margin-bottom: 1rem;
        }

        .drawer-padding {
            padding:0 20px;height:100%;
        }

        .drawer-relative {
            height:100%; position: relative;
        }

        .drawer-body {
            position: absolute; top: 0; left: 0; right: 0; bottom: 65px; padding: 0 10px; overflow-y: auto;
        }

        .drawer-footer {
            position: absolute; left: 0; right: 0; bottom: 0; height: 60px; border-top: #eee 1px solid;
        }

    </style>
</be-head>

<be-north>
    <div class="be-north" id="be-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo beAdminUrl('Shop.Shipping.index'); ?>">返回物流运费列表</el-link>
                </div>
            </div>
            <div class="be-col-auto">
                <div style="padding: .75rem 2rem 0 0;">
                    <el-button size="medium" :disabled="loading" @click="vueCenter.cancel();">取消</el-button>
                    <el-button size="medium" type="primary" :disabled="loading" @click="vueCenter.save();">保存</el-button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let vueNorth = new Vue({
            el: '#be-north',
            data: {
                loading: false,
            }
        });
    </script>
</be-north>


<be-page-content>
    <div id="app" v-cloak>

        <el-form ref="formRef" :model="formData" class="be-mb-400">
            <?php $formData['id'] = ($this->shipping ? $this->shipping->id : ''); ?>
            <div class="be-p-150 be-bc-fff">

                <div class="be-row" style="align-items:center;">
                    <div class="be-col-auto">
                        <div class="be-fs-110">区域方案名称<span class="be-c-red be-pl-50">*</span></div>
                    </div>

                    <div class="be-col-auto be-c-999 be-pl-100">
                        （该名称不会展示给客户查看/地区）
                    </div>

                    <div class="be-col"></div>
                </div>

                <el-form-item class="be-mt-100" style="margin-bottom: 0;" prop="name" :rules="[{required: true, message: '请输入区域方案名称', trigger: 'change' }]">
                    <el-input
                            type="text"
                            placeholder="请输入区域方案名称"
                            v-model = "formData.name"
                            size="medium"
                            maxlength="60"
                            show-word-limit>
                    </el-input>
                </el-form-item>
                <?php $formData['name'] = ($this->shipping ? $this->shipping->name : ''); ?>
            </div>


            <div class="be-p-150 be-bc-fff be-mt-200">

                <div class="be-row" style="align-items:center;">
                    <div class="be-col-auto">
                        <div class="be-fs-110">配送区域<span class="be-c-red be-pl-50">*</span></div>
                    </div>

                    <div class="be-col-auto be-c-999 be-pl-100">
                        （添加当前方案适用的国家/地区）
                    </div>

                    <div class="be-col"></div>

                    <div class="be-col-auto be-pl-100">
                        <el-button size="medium" type="primary" @click="editRegion();">添加国家/地区</el-button>
                    </div>
                </div>

                <el-table
                        class="be-mt-100 region-countries"
                        ref = "shippingRegionTableRef"
                        :data="formData.regions">
                    <el-table-column label="国家/地区" align="left">
                        <template slot-scope="scope">
                            <img :src="'<?php echo \Be\Be::getProperty('App.Shop')->getWwwUrl(); ?>/images/country-flag/' + scope.row.country.flag" :alt="scope.row.country.name_cn">
                            <span class="be-pl-100">{{scope.row.country.name_cn}}</span>
                            <span class="be-c-999">（{{scope.row.country.name}}）</span>
                        </template>
                    </el-table-column>

                    <el-table-column label="州/省" align="center" width="300">
                        <template slot-scope="scope">
                            {{scope.row.state_description}}
                        </template>
                    </el-table-column>

                    <el-table-column label="操作" align="center" width="120">
                        <template slot-scope="scope">
                            <el-link class="be-mr-100" type="primary" icon="el-icon-edit" :disabled="scope.row.country.state_count === '0'" :underline="false" style="font-size: 20px;" @click="editRegionState(scope.row)"></el-link>
                            <el-link type="danger" icon="el-icon-delete" :underline="false" style="font-size: 20px;" @click="deleteRegionCountry(scope.row)"></el-link>
                        </template>
                    </el-table-column>
                </el-table>
                <?php $formData['regions'] = ($this->shipping ? $this->shipping->regions : []); ?>

            </div>


            <div class="be-p-150 be-bc-fff be-mt-200">

                <div class="be-row" style="align-items:center;">
                    <div class="be-col-auto">
                        <div class="be-fs-110">运费方案<span class="be-c-red be-pl-50">*</span></div>
                    </div>

                    <div class="be-col"></div>

                    <div class="be-col-auto be-pl-100">
                        <el-button size="medium" type="primary" @click="addPlan();">添加运费方案</el-button>
                    </div>
                </div>

                <el-table
                        class="be-mt-100"
                        ref = "shippingPlanTableRef"
                        :data="formData.plans">

                    <el-table-column label="方案名称" align="left">
                        <template slot-scope="scope">
                            {{scope.row.name}}
                        </template>
                    </el-table-column>

                    <el-table-column label="运费" align="left">
                        <template slot-scope="scope">
                            <template v-if="scope.row.shipping_fee_type === 'fixed'">
                                {{scope.row.shipping_fee_fixed === "0.00" ? "免运费" : ("<?php echo $this->configStore->currencySymbol; ?>" + scope.row.shipping_fee_fixed)}}
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

                    <el-table-column label="操作" align="center" width="120">
                        <template slot-scope="scope">
                            <el-link class="be-mr-100" type="primary" icon="el-icon-edit" :underline="false" style="font-size: 20px;" @click="editPlan(scope.row, scope.$index)"></el-link>
                            <el-link type="danger" icon="el-icon-delete" :underline="false" style="font-size: 20px;" @click="deletePlan(scope.row)"></el-link>
                        </template>
                    </el-table-column>
                </el-table>
                <?php $formData['plans'] = ($this->shipping ? $this->shipping->plans : []); ?>

            </div>
        </el-form>

        <el-drawer
                :visible.sync="drawerRegion.visible"
                :size="drawerRegion.width"
                :title="drawerRegion.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div class="drawer-padding">
                <div class="drawer-relative">
                    <div class="drawer-body">
                        <el-tree
                                ref="regionTreeRef"
                                :data="regionTree"
                                show-checkbox
                                check-on-click-node
                                node-key="code"
                                :default-checked-keys="regionTreeCheckedKeys"
                                :props="regionTreeProps"
                        >
                            <div slot-scope="{node, data}" class="be-row">
                                <div v-if="data.flag" class="be-col-auto be-px-50 region-country-flag">
                                    <img :src="'<?php echo \Be\Be::getProperty('App.Shop')->getWwwUrl(); ?>/images/country-flag/' + data.flag" :alt="data.name_cn">
                                </div>
                                <div class="be-col-auto be-pl-50">{{data.name_cn}}</div>
                                <div class="be-col-auto be-c-999">（{{data.name}}）</div>
                            </div>
                        </el-tree>
                    </div>

                    <div class="drawer-footer">
                        <div class="be-ta-right be-pt-50">
                            <el-button @click="drawerRegion.visible=false" size="medium"> 取 消 </el-button>
                            <el-button @click="saveRegion" type="primary" size="medium"> 保 存 </el-button>
                        </div>
                    </div>
                </div>
            </div>
        </el-drawer>

        <el-drawer
                :visible.sync="drawerRegionState.visible"
                :size="drawerRegionState.width"
                :title="drawerRegionState.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div class="drawer-padding">
                <div class="drawer-relative">
                    <div class="drawer-body" v-loading="regionStateLoading">
                        <el-checkbox-group v-model="regionStatesChecked">
                            <div class="be-py-75 be-pl-100" v-for="state in regionStates">
                                <el-checkbox :label="state.id" :key="state.id">{{state.name_cn}}（{{state.name}}）</el-checkbox>
                            </div>
                        </el-checkbox-group>
                    </div>

                    <div class="drawer-footer">
                        <div class="be-ta-right be-pt-50">
                            <el-button @click="drawerRegionState.visible=false" :disabled="regionStateLoading" size="medium"> 取 消 </el-button>
                            <el-button @click="saveRegionState" type="primary" :disabled="regionStateLoading" size="medium"> 保 存 </el-button>
                        </div>
                    </div>
                </div>
            </div>
        </el-drawer>

        <el-drawer
                :visible.sync="drawerPlan.visible"
                :size="drawerPlan.width"
                :title="drawerPlan.title"
                :wrapper-closable="false"
                :destroy-on-close="true">
            <div class="drawer-padding">
                <div class="drawer-relative">
                    <div class="drawer-body">

                        <el-form ref="formPlanRef" :model="formPlanData" class="region-plan-form">

                            <div>方案名称<span class="be-c-red">*</span><span class="be-c-999">（顾客选择物流方案时展示）</span>：</div>
                            <el-form-item class="be-mt-50" prop="name" :rules="[{required: true, message: '请输入区域方案名称', trigger: 'change' }]">
                                <el-input
                                        type="text"
                                        placeholder="请输入区域方案名称"
                                        v-model = "formPlanData.name"
                                        size="medium"
                                        maxlength="60"
                                        show-word-limit>
                                </el-input>
                            </el-form-item>

                            <div class="be-mt-150">方案说明<span class="be-c-999">（顾客选择物流方案时展示）</span>：</div>
                            <el-form-item class="be-mt-50" prop="description">
                                <el-input
                                        type="textarea"
                                        :rows="3"
                                        placeholder="请输入区域方案说明"
                                        v-model = "formPlanData.description"
                                        size="medium"
                                        maxlength="200"
                                        show-word-limit>
                                </el-input>
                            </el-form-item>


                            <div class="be-mt-200">
                                运费设置：
                                <el-radio-group v-model="formPlanData.shipping_fee_type">
                                    <el-radio label="fixed">固定运费</el-radio>
                                    <el-radio label="weight">首重+续重</el-radio>
                                </el-radio-group>
                            </div>

                            <div class="be-mt-100" v-if="formPlanData.shipping_fee_type === 'fixed'">
                                <el-form-item prop="shipping_fee_fixed" :rules="[{required: formPlanData.shipping_fee_type === 'fixed', message: '请输入运费', trigger: 'change' }]">
                                    <el-tag type="info" class="currency-tag"><?php echo $this->configStore->currencySymbol; ?></el-tag>
                                    <el-input-number
                                            controls-position="right"
                                            :precision="2"
                                            :step="0.01"
                                            step-strictly
                                            :min="0"
                                            placeholder="请输入运费"
                                            v-model.string = "formPlanData.shipping_fee_fixed"
                                            size="medium"
                                            style="width: 120px;">
                                    </el-input-number>
                                </el-form-item>
                            </div>

                            <div class="be-mt-100" v-else-if="formPlanData.shipping_fee_type === 'weight'">

                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250" style="width: 5rem">首重：</div>

                                    <div class="be-col-auto">
                                        <el-form-item prop="shipping_fee_first_weight" :rules="[{required: formPlanData.shipping_fee_type === 'weight', message: '请输入首重重量', trigger: 'change' }]">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="2"
                                                    :step="0.01"
                                                    step-strictly
                                                    :min="0"
                                                    placeholder="首重"
                                                    v-model = "formPlanData.shipping_fee_first_weight"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </el-form-item>
                                    </div>
                                    <div class="be-col-auto be-pl-10">
                                        <el-select v-model="formPlanData.shipping_fee_first_weight_unit" size="medium" style="width: 70px;">
                                            <el-option label="kg" value="kg"></el-option>
                                            <el-option label="g" value="g"></el-option>
                                            <el-option label="lb" value="lb"></el-option>
                                            <el-option label="oz" value="oz"></el-option>
                                        </el-select>
                                    </div>

                                    <div class="be-col-auto be-pl-100">
                                        <el-tag type="info" class="currency-tag"><?php echo $this->configStore->currencySymbol; ?></el-tag>
                                    </div>
                                    <div class="be-col-auto be-pl-10">
                                        <el-form-item prop="shipping_fee_first_weight_price" :rules="[{required: formPlanData.shipping_fee_type === 'weight', message: '请输入首重运费', trigger: 'change' }]">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="2"
                                                    :step="0.01"
                                                    step-strictly
                                                    :min="0"
                                                    placeholder="首重运费"
                                                    v-model = "formPlanData.shipping_fee_first_weight_price"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </el-form-item>
                                    </div>

                                </div>
                                <div class="be-row be-mt-100">
                                    <div class="be-col-auto be-lh-250" style="width: 5rem">续重：每</div>

                                    <div class="be-col-auto">
                                        <el-form-item prop="shipping_fee_additional_weight" :rules="[{required: formPlanData.shipping_fee_type === 'weight', message: '请输入续重重量', trigger: 'change' }]">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="2"
                                                    :step="0.01"
                                                    step-strictly
                                                    :min="0"
                                                    placeholder="续重"
                                                    v-model = "formPlanData.shipping_fee_additional_weight"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </el-form-item>
                                    </div>
                                    <div class="be-col-auto be-pl-10">
                                        <el-select v-model="formPlanData.shipping_fee_additional_weight_unit" size="medium" style="width: 70px;">
                                            <el-option label="kg" value="kg"></el-option>
                                            <el-option label="g" value="g"></el-option>
                                            <el-option label="lb" value="lb"></el-option>
                                            <el-option label="oz" value="oz"></el-option>
                                        </el-select>
                                    </div>

                                    <div class="be-col-auto be-pl-100">
                                        <el-tag type="info" class="currency-tag"><?php echo $this->configStore->currencySymbol; ?></el-tag>
                                    </div>
                                    <div class="be-col-auto be-pl-10">
                                        <el-form-item prop="shipping_fee_additional_weight_price" :rules="[{required: formPlanData.shipping_fee_type === 'weight', message: '请输入续重运费', trigger: 'change' }]">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="2"
                                                    :step="0.01"
                                                    step-strictly
                                                    :min="0"
                                                    placeholder="续重运费"
                                                    v-model = "formPlanData.shipping_fee_additional_weight_price"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </el-form-item>
                                    </div>
                                </div>
                            </div>

                            <div class="be-mt-200">
                                <el-checkbox v-model="formPlanData.cod" true-label="1" false-label="0">支持货到付款</el-checkbox>
                            </div>

                            <div class="be-mt-200">
                                <el-checkbox v-model="formPlanData.limit" true-label="1" false-label="0">开启下单限制</el-checkbox>

                                <el-select v-model="formPlanData.limit_type" class="be-ml-200" v-if="formPlanData.limit === '1'" size="medium" style="width: 120px;">
                                    <el-option label="订单金额" value="amount"></el-option>
                                    <el-option label="商品件数" value="quantity"></el-option>
                                    <el-option label="商品重量" value="weight"></el-option>
                                </el-select>
                            </div>
                            <div class="be-mt-100" v-if="formPlanData.limit === '1'">
                                <template v-if="formPlanData.limit_type === 'amount'">
                                    <div class="be-row">
                                        <div class="be-col-auto be-lh-250">订单金额范围：</div>
                                        <div class="be-col-auto be-lh-250">
                                            <el-tag type="info" class="currency-tag"><?php echo $this->configStore->currencySymbol; ?></el-tag>
                                        </div>
                                        <div class="be-col-auto be-pl-50">
                                            <el-form-item prop="limit_amount_from" :rules="[{required: formPlanData.limit === '1' && formPlanData.limit_type === 'amount', message: '请输入订单金额范围', trigger: 'change' }]">
                                                <el-input-number
                                                        controls-position="right"
                                                        placeholder="0"
                                                        :precision="2"
                                                        :step="0.01"
                                                        step-strictly
                                                        :min="0"
                                                        v-model = "formPlanData.limit_amount_from"
                                                        size="medium"
                                                        style="width: 120px;">
                                                </el-input-number>
                                            </el-form-item>
                                        </div>
                                        <div class="be-col-auto be-pl-50 be-lh-250">~</div>
                                        <div class="be-col-auto be-pl-50">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="2"
                                                    :step="0.01"
                                                    step-strictly
                                                    :min="0"
                                                    placeholder="无限"
                                                    v-model = "formPlanData.limit_amount_to"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </div>
                                    </div>
                                    <div class="be-mt-50 be-c-999">当订单金额满足以上条件时，方可选用此运费方案</div>
                                </template>
                                <template v-else-if="formPlanData.limit_type === 'quantity'">
                                    <div class="be-row">
                                        <div class="be-col-auto be-lh-250">商品件数范围：</div>
                                        <div class="be-col-auto be-pl-50">
                                            <el-form-item prop="limit_quantity_from" :rules="[{required: formPlanData.limit === '1' && formPlanData.limit_type === 'quantity', message: '请输入商品件数范围', trigger: 'change' }]">
                                                <el-input-number
                                                    controls-position="right"
                                                    :precision="0"
                                                    :step="1"
                                                    step-strictly
                                                    :min="1"
                                                    v-model = "formPlanData.limit_quantity_from"
                                                    size="medium"
                                                    style="width: 120px;">
                                                </el-input-number>
                                            </el-form-item>
                                        </div>
                                        <div class="be-col-auto be-pl-50 be-lh-250">~</div>
                                        <div class="be-col-auto be-pl-50">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="0"
                                                    :step="1"
                                                    step-strictly
                                                    :min="1"
                                                    placeholder="无限"
                                                    v-model = "formPlanData.limit_quantity_to"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </div>

                                        <div class="be-col-auto be-pl-50 be-lh-250">
                                            件
                                        </div>
                                    </div>
                                    <div class="be-mt-50 be-c-999">当商品件数满足以上条件时，方可选用此运费方案</div>
                                </template>
                                <template v-else-if="formPlanData.limit_type === 'weight'">
                                    <div class="be-row">
                                        <div class="be-col-auto be-lh-250">商品重量范围：</div>
                                        <div class="be-col-auto">
                                            <el-form-item prop="limit_weight_from" :rules="[{required: formPlanData.limit === '1' && formPlanData.limit_type === 'weight', message: '请输入商品重量范围', trigger: 'change' }]">
                                                <el-input-number
                                                        controls-position="right"
                                                        :precision="2"
                                                        :step="0.01"
                                                        step-strictly
                                                        :min="0"
                                                        placeholder="0"
                                                        v-model = "formPlanData.limit_weight_from"
                                                        size="medium"
                                                        style="width: 120px;">
                                                </el-input-number>
                                            </el-form-item>
                                        </div>
                                        <div class="be-col-auto be-pl-50 be-lh-250">~</div>
                                        <div class="be-col-auto be-pl-50">
                                            <el-input-number
                                                    controls-position="right"
                                                    :precision="2"
                                                    :step="0.01"
                                                    step-strictly
                                                    :min="0"
                                                    placeholder="无限"
                                                    v-model = "formPlanData.limit_weight_to"
                                                    size="medium"
                                                    style="width: 120px;">
                                            </el-input-number>
                                        </div>

                                        <div class="be-col-auto be-pl-50">
                                            <el-select v-model="formPlanData.limit_weight_unit" size="medium" style="width: 70px;">
                                                <el-option label="kg" value="kg"></el-option>
                                                <el-option label="g" value="g"></el-option>
                                                <el-option label="lb" value="lb"></el-option>
                                                <el-option label="oz" value="oz"></el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                    <div class="be-mt-50 be-c-999">当商品重量满足以上条件时，方可选用此运费方案</div>
                                </template>
                            </div>

                        </el-form>
                    </div>

                    <div class="drawer-footer">
                        <div class="be-ta-right be-pt-50">
                            <el-button @click="drawerPlan.visible=false" size="medium"> 取 消 </el-button>
                            <el-button @click="savePlan" type="primary" size="medium"> 保 存 </el-button>
                        </div>
                    </div>
                </div>
            </div>
        </el-drawer>

    </div>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                loading: false,
                formData: <?php echo json_encode($formData); ?>,

                drawerRegion: {visible: false, width: "40%", title: "区域设置"},
                drawerRegionState: {visible: false, width: "40%", title: "州/省选择"},
                drawerPlan: {visible: false, width: "40%", title: ""},

                regionTree: <?php echo json_encode($this->regionTree); ?>,
                regionTreeProps: {
                    children: 'countries',
                    label: 'name_cn'
                },
                regionTreeCheckedKeys: [],

                regionStateLoading: false,
                regionCurrent: false,
                regionStates: [],
                regionStatesChecked: [],

                planCurrentIndex: -1,
                formPlanData: {
                    id: "",
                    name: "",
                    description: "",
                    limit: "0",
                    limit_type: "amount",
                    limit_amount_from: "0.00",
                    limit_amount_to: undefined,
                    limit_quantity_from: "0",
                    limit_quantity_to: undefined,
                    limit_weight_from: "0.00",
                    limit_weight_to: undefined,
                    limit_weight_unit: "g",
                    cod: "0",
                    shipping_fee_type: "fixed",
                    shipping_fee_fixed: "0.00",
                    shipping_fee_first_weight_price: undefined,
                    shipping_fee_first_weight: undefined,
                    shipping_fee_first_weight_unit: "g",
                    shipping_fee_additional_weight_price: undefined,
                    shipping_fee_additional_weight: undefined,
                    shipping_fee_additional_weight_unit: "g"
                },

                t: false
            },
            methods: {
                save: function () {
                    let _this = this;
                    this.$refs.formRef.validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            vueNorth.loading = true;
                            _this.$http.post("<?php echo beAdminUrl('Shop.Shipping.' . ($this->shipping ? 'edit' :'create')); ?>", {
                                formData: _this.formData
                            }).then(function (response) {
                                _this.loading = false;
                                vueNorth.loading = false;
                                //console.log(response);
                                if (response.status === 200) {
                                    var responseData = response.data;
                                    if (responseData.success) {
                                        _this.$message.success(responseData.message);
                                        setTimeout(function () {
                                            window.onbeforeunload = null;
                                            window.location.href = "<?php echo beAdminUrl('Shop.Shipping.index'); ?>";
                                        }, 1000);
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
                                vueNorth.loading = false;
                                _this.$message.error(error);
                            });
                        } else {
                            return false;
                        }
                    });
                },

                cancel: function () {
                    window.onbeforeunload = null;
                    window.location.href = "<?php echo beAdminUrl('Shop.Shipping.index'); ?>";
                },

                editRegion: function () {
                    this.drawerRegion.visible = true;

                    let regionTreeCheckedKeys = [];
                    for (let region of this.formData.regions) {
                        regionTreeCheckedKeys.push(region.country_code);
                    }
                    //this.$refs.regionTreeRef.setCheckedKeys(regionTreeCheckedKeys);

                    this.regionTreeCheckedKeys = regionTreeCheckedKeys;
                },
                saveRegion: function () {
                    this.drawerRegion.visible = false;

                    let exist;
                    let newRegions = [];
                    let countries = this.$refs.regionTreeRef.getCheckedNodes();
                    //console.log(nodes);
                    for (let country of countries) {
                        // 跳过洲节点
                        if (!("flag" in country)) {
                            continue;
                        }

                        // 已经存在的国家， 直接使用
                        exist = false;
                        for (let region of this.formData.regions) {
                            if (region.country_code === country.code) {
                                newRegions.push(region);
                                exist = true;
                                break;
                            }
                        }

                        if (!exist) {

                            // 新增的国家
                            newRegions.push({
                                id : "",
                                country_id: country.id,
                                country_code: country.code,
                                country:country,
                                assign_state: "0",
                                state_description: (country.state_count === "0" ? "无" : "全部"),
                                states: []
                            });
                        }
                    }

                    this.formData.regions = newRegions;
                },
                deleteRegionCountry:function (row) {
                    this.formData.regions.splice(this.formData.regions.indexOf(row), 1);
                },
                editRegionState:function (row) {
                    //console.log(row);

                    this.regionStateLoading = true;
                    this.drawerRegionState.visible = true;

                    this.regionCurrent = row;

                    // 先初始化州列表
                    this.regionStates = [];
                    this.regionStatesChecked = [];

                    var _this = this;
                    this.$http.post("<?php echo beAdminUrl('Shop.Region.getStates'); ?>", {
                        country_code: row.country_code
                    }).then(function (response) {
                        //console.log(response);
                        _this.regionStateLoading = false;

                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.regionStates = responseData.states;

                                // 处理默认选中项
                                let regionStatesChecked = [];
                                if (row.assign_state === "0") { // 该国家的全部州
                                    for (let state of _this.regionStates) {
                                        regionStatesChecked.push(state.id);
                                    }
                                } else {
                                    for (let state of row.states) {
                                        regionStatesChecked.push(state.state_id);
                                    }
                                }
                                _this.regionStatesChecked = regionStatesChecked;

                            } else {
                                if (responseData.message) {
                                    _this.$message.error(responseData.message);
                                } else {
                                    _this.$message.error("服务器返回数据异常！");
                                }
                            }
                        }
                    }).catch(function (error) {
                        _this.regionStateLoading = false;
                        _this.$message.error(error);
                    });

                },
                saveRegionState: function() {
                    let checkLength = this.regionStatesChecked.length;
                    if (checkLength === 0) {

                        // 没有选任保州，删除该国家
                        let _this = this;
                        this.$confirm("您未选择任何州/省，将从配送区域移除国家 " + this.regionCurrent.country.name_cn + "， 是否继续?", "提示", {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(function () {
                            _this.formData.regions.splice(this.formData.regions.indexOf(_this.regionCurrent), 1);
                        });

                    } else {

                        this.drawerRegionState.visible = false;

                        if (checkLength === Number(this.regionCurrent.country.state_count)) {
                            // 全部选中了
                            this.regionCurrent.assign_state = "0";
                            this.regionCurrent.state_description = "全部";
                            this.regionCurrent.states = [];

                        } else {
                            // 选中了部分
                            this.regionCurrent.assign_state = "1";
                            this.regionCurrent.state_description = checkLength  + "/" + this.regionCurrent.country.state_count + " 个州/省";

                            let states = [];
                            let exist;
                            for (let stateId of this.regionStatesChecked) {
                                exist = false;
                                for (let state of this.regionCurrent.states) {
                                    if (state.state_id === stateId) {
                                        states.push(state);
                                        exist = true;
                                    }
                                }
                                if (!exist) {
                                    for (let state of this.regionStates) {
                                        if (state.id === stateId) {
                                            states.push({
                                                id: "",
                                                state_id: state.id,
                                                state_name: state.name
                                            });
                                            break;
                                        }
                                    }
                                }
                            }
                            this.regionCurrent.states = states;
                        }
                    }
                },

                addPlan: function () {
                    this.drawerPlan.visible = true;
                    this.drawerPlan.title = "添加运费方案";

                    this.planCurrentIndex = -1;

                    this.formPlanData.id = "";
                    this.formPlanData.name = "";
                    this.formPlanData.description = "";
                    this.formPlanData.limit = "0";
                    this.formPlanData.limit_type = "amount";
                    this.formPlanData.limit_amount_from = "0.00";
                    this.formPlanData.limit_amount_to = undefined;
                    this.formPlanData.limit_quantity_from = "0";
                    this.formPlanData.limit_quantity_to = undefined;
                    this.formPlanData.limit_weight_from = "0.00";
                    this.formPlanData.limit_weight_to = undefined;
                    this.formPlanData.limit_weight_unit = "g";
                    this.formPlanData.cod = "0";
                    this.formPlanData.shipping_fee_type = "fixed";
                    this.formPlanData.shipping_fee_fixed = "0.00";
                    this.formPlanData.shipping_fee_first_weight_price = undefined;
                    this.formPlanData.shipping_fee_first_weight = undefined;
                    this.formPlanData.shipping_fee_first_weight_unit = "g";
                    this.formPlanData.shipping_fee_additional_weight_price = undefined;
                    this.formPlanData.shipping_fee_additional_weight = undefined;
                    this.formPlanData.shipping_fee_additional_weight_unit = "g";
                },
                editPlan: function (row, index) {
                    this.drawerPlan.visible = true;
                    this.drawerPlan.title = "编辑运费方案";

                    this.planCurrentIndex = index;

                    this.formPlanData.id = row.id;
                    this.formPlanData.name = row.name;
                    this.formPlanData.description = row.description;

                    this.formPlanData.limit = row.limit;

                    this.formPlanData.limit_type = "amount";
                    this.formPlanData.limit_amount_from = "0.00";
                    this.formPlanData.limit_amount_to = undefined;
                    this.formPlanData.limit_quantity_from = "0";
                    this.formPlanData.limit_quantity_to = undefined;
                    this.formPlanData.limit_weight_from = "0.00";
                    this.formPlanData.limit_weight_to = undefined;
                    this.formPlanData.limit_weight_unit = "g";
                    if (row.limit === "1") {
                        this.formPlanData.limit_type = row.limit_type;
                        switch (row.limit_type) {
                            case "amount":
                                this.formPlanData.limit_amount_from = row.limit_amount_from;
                                this.formPlanData.limit_amount_to = (row.limit_amount_to === "-1.00" ? undefined : row.limit_amount_to);
                                break;
                            case "quantity":
                                this.formPlanData.limit_quantity_from = row.limit_quantity_from;
                                this.formPlanData.limit_quantity_to = (row.limit_quantity_to === "-1" ? undefined : row.limit_quantity_to);
                                break;
                            case "weight":
                                this.formPlanData.limit_weight_from = row.limit_weight_from;
                                this.formPlanData.limit_weight_to = (row.limit_weight_to === "-1.00" ? undefined : row.limit_weight_to);
                                this.formPlanData.limit_weight_unit = row.limit_weight_unit;
                                break;
                        }
                    }

                    this.formPlanData.cod = row.cod;

                    this.formPlanData.shipping_fee_type = row.shipping_fee_type;

                    this.formPlanData.shipping_fee_fixed = "0.00";
                    this.formPlanData.shipping_fee_first_weight_price = undefined;
                    this.formPlanData.shipping_fee_first_weight = undefined;
                    this.formPlanData.shipping_fee_first_weight_unit = "g";
                    this.formPlanData.shipping_fee_additional_weight_price = undefined;
                    this.formPlanData.shipping_fee_additional_weight = undefined;
                    this.formPlanData.shipping_fee_additional_weight_unit = "g";
                    if (row.shipping_fee_type === "fixed") {
                        this.formPlanData.shipping_fee_fixed = row.shipping_fee_fixed;
                    } else {
                        this.formPlanData.shipping_fee_first_weight_price = row.shipping_fee_first_weight_price;
                        this.formPlanData.shipping_fee_first_weight = row.shipping_fee_first_weight;
                        this.formPlanData.shipping_fee_first_weight_unit = row.shipping_fee_first_weight_unit;
                        this.formPlanData.shipping_fee_additional_weight_price = row.shipping_fee_additional_weight_price;
                        this.formPlanData.shipping_fee_additional_weight = row.shipping_fee_additional_weight;
                        this.formPlanData.shipping_fee_additional_weight_unit = row.shipping_fee_additional_weight_unit;
                    }
                },
                savePlan: function () {
                    let _this = this;
                    this.$refs.formPlanRef.validate(function (valid) {
                        if (valid) {

                            //console.log(_this.formPlanData);

                            let planCurrent = _this.planCurrentIndex === -1 ? {} : _this.formData.plans[_this.planCurrentIndex];

                            planCurrent.id = _this.formPlanData.id;
                            planCurrent.name = _this.formPlanData.name;
                            planCurrent.description = _this.formPlanData.description;

                            planCurrent.limit = _this.formPlanData.limit;

                            planCurrent.limit_type = "amount";
                            planCurrent.limit_amount_from = "0.00";
                            planCurrent.limit_amount_to = "-1.00";
                            planCurrent.limit_quantity_from = "0";
                            planCurrent.limit_quantity_to = "-1";
                            planCurrent.limit_weight_from = "0.00";
                            planCurrent.limit_weight_to = "-1.00";
                            planCurrent.limit_weight_unit = "g";
                            if (_this.formPlanData.limit === "1") {
                                planCurrent.limit_type = _this.formPlanData.limit_type;
                                switch (_this.formPlanData.limit_type) {
                                    case "amount":
                                        planCurrent.limit_amount_from = _this.formPlanData.limit_amount_from.toFixed(2);
                                        planCurrent.limit_amount_to = (typeof(_this.formPlanData.limit_amount_to) === "undefined" || _this.formPlanData.limit_amount_to === "" ? "-1.00" : _this.formPlanData.limit_amount_to.toFixed(2));
                                        break;
                                    case "quantity":
                                        planCurrent.limit_quantity_from = _this.formPlanData.limit_quantity_from.toFixed(2);
                                        planCurrent.limit_quantity_to = (typeof(_this.formPlanData.limit_quantity_to) === "undefined" || _this.formPlanData.limit_quantity_to === "" ? "-1" : _this.formPlanData.limit_quantity_to.toString());
                                        break;
                                    case "weight":
                                        planCurrent.limit_weight_from = _this.formPlanData.limit_weight_from.toFixed(2);
                                        planCurrent.limit_weight_to = (typeof(_this.formPlanData.limit_weight_to) === "undefined" || _this.formPlanData.limit_weight_to === "" ? "-1.00" : _this.formPlanData.limit_weight_to.toFixed(2));
                                        planCurrent.limit_weight_unit = _this.formPlanData.limit_weight_unit;
                                        break;
                                }
                            }

                            planCurrent.cod = _this.formPlanData.cod;

                            planCurrent.shipping_fee_type = _this.formPlanData.shipping_fee_type;

                            if (_this.formPlanData.shipping_fee_type === "fixed") {
                                planCurrent.shipping_fee_fixed = _this.formPlanData.shipping_fee_fixed.toFixed(2);
                                planCurrent.shipping_fee_first_weight_price = "0.00";
                                planCurrent.shipping_fee_first_weight = "0.00";
                                planCurrent.shipping_fee_first_weight_unit = "g";
                                planCurrent.shipping_fee_additional_weight_price = "0.00";
                                planCurrent.shipping_fee_additional_weight = "0.00";
                                planCurrent.shipping_fee_additional_weight_unit = "g";
                            } else {
                                planCurrent.shipping_fee_fixed = "0.00";
                                planCurrent.shipping_fee_first_weight_price = _this.formPlanData.shipping_fee_first_weight_price.toFixed(2);
                                planCurrent.shipping_fee_first_weight = _this.formPlanData.shipping_fee_first_weight.toFixed(2);
                                planCurrent.shipping_fee_first_weight_unit = _this.formPlanData.shipping_fee_first_weight_unit;
                                planCurrent.shipping_fee_additional_weight_price = _this.formPlanData.shipping_fee_additional_weight_price.toFixed(2);
                                planCurrent.shipping_fee_additional_weight = _this.formPlanData.shipping_fee_additional_weight.toFixed(2);
                                planCurrent.shipping_fee_additional_weight_unit = _this.formPlanData.shipping_fee_additional_weight_unit;
                            }
                            //console.log(planCurrent);
                            if (_this.planCurrentIndex === -1) {
                                _this.formData.plans.push(planCurrent);
                            }

                            _this.drawerPlan.visible = false;
                        } else {
                            return false;
                        }
                    });
                },
                deletePlan: function (row) {
                    this.formData.plans.splice(this.formData.plans.indexOf(row), 1);
                },

                t: function () {
                }
            },
            mounted: function () {
                window.onbeforeunload = function(e) {
                    e = e || window.event;
                    if (e) {
                        e.returnValue = "";
                    }
                    return "";
                }
            }
        });
    </script>

</be-page-content>