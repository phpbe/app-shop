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

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button{
            -webkit-appearance: none !important;
            margin: 0;
        }

        input[type="number"]{-moz-appearance:textfield;}
    </style>
</be-head>


<be-north>
    <div class="be-north" id="be-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo beAdminUrl('ShopFai.PromotionCoupon.coupons'); ?>">返回优惠券列表</el-link>
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
    <?php
    $formData = [];
    $uiItems = new \Be\AdminPlugin\UiItem\UiItems();
    ?>

    <div id="app" v-cloak>

        <el-form ref="formRef" :model="formData" class="be-mb-400" size="medium">
            <?php
            $formData['id'] = ($this->promotionCoupon ? $this->promotionCoupon->id : '');
            ?>

            <div class="be-row">
                <div class="be-col-24 be-col-xxl-16">
                     <div class="be-p-150 be-bc-fff">

                        <div class="be-row">
                            <div class="be-col-auto be-lh-250 be-pr-100">
                                名称：
                                <el-tooltip effect="dark" content="此标题仅作内部展示" placement="top">
                                    <i class="el-icon-fa fa-question-circle-o"></i>
                                </el-tooltip>
                            </div>
                            <div class="be-col">
                                <el-form-item prop="name" :rules="[{required: true, message: '请输入名称', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="请输入名称"
                                            v-model = "formData.name"
                                            maxlength="60"
                                            show-word-limit>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['name'] = ($this->promotionCoupon ? $this->promotionCoupon->name : ''); ?>
                            </div>
                        </div>

                        <div class="be-row be-mt-150">
                            <div class="be-col-auto be-lh-250 be-pr-100">
                                优惠码：
                            </div>
                            <div class="be-col be-pr-100">
                                <el-form-item prop="code" :rules="[{required: true, message: '请输入优惠码', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="请输入优惠码"
                                            v-model = "formData.code"
                                            size="medium"
                                            maxlength="30"
                                            show-word-limit>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['code'] = ($this->promotionCoupon ? $this->promotionCoupon->code : ''); ?>
                            </div>
                            <div class="be-col-auto">
                                <el-button size="medium" type="primary" size="medium" @click="generateCode">自动生成</el-button>
                            </div>
                        </div>
                    </div>


                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            优惠类型
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-auto be-lh-250 be-pr-300">
                                <el-radio v-model="formData.discount_type" label="percent">百分比折扣</el-radio>
                                <el-radio v-model="formData.discount_type" label="amount">固定金额</el-radio>
                                <?php $formData['discount_type'] = ($this->promotionCoupon ? $this->promotionCoupon->discount_type : 'percent'); ?>
                            </div>


                            <div class="be-col-auto be-lh-250" v-if="formData.discount_type === 'percent'">
                                减免折扣：
                            </div>
                            <div class="be-col-auto" v-if="formData.discount_type === 'percent'">
                                <el-form-item prop="discount_percent" :rules="[{required: formData.discount_type === 'percent', message: '请输入优惠百分比', trigger: 'change' }]">
                                    <el-input
                                            v-model.number = "formData.discount_percent"
                                            type="number"
                                            size="medium"
                                            min="1">
                                        <template slot="append">%</template>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['discount_percent'] = ($this->promotionCoupon ? $this->promotionCoupon->discount_percent : '0'); ?>
                            </div>

                            <div class="be-col-auto be-lh-250" v-if="formData.discount_type === 'amount'">
                                减免金额：
                            </div>
                            <div class="be-col-auto" v-if="formData.discount_type === 'amount'">
                                <el-form-item prop="discount_amount" :rules="[{required: formData.discount_type === 'amount', message: '请输入优惠金额', trigger: 'change' }]">
                                    <el-input
                                            v-model.string = "formData.discount_amount"
                                            type="number"
                                            size="medium"
                                            min="0.01">
                                        <template slot="prepend"><?php echo $this->configStore->currencySymbol; ?></template>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['discount_amount'] = ($this->promotionCoupon ? $this->promotionCoupon->discount_amount : '0'); ?>
                            </div>
                        </div>
                    </div>


                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            优惠条件
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-auto be-lh-250 be-pr-300">
                                <el-radio v-model="formData.condition" label="none">无</el-radio>
                                <el-radio v-model="formData.condition" label="min_amount">需消费指定金额</el-radio>
                                <el-radio v-model="formData.condition" label="min_quantity">需购买指定数量</el-radio>
                                <?php $formData['condition'] = ($this->promotionCoupon ? $this->promotionCoupon->condition : 'none'); ?>
                            </div>

                            <div class="be-col-auto" v-if="formData.condition === 'min_amount'">
                                <el-form-item prop="condition_min_amount" :rules="[{required: formData.condition === 'min_amount', message: '请输入最低消费金额', trigger: 'change' }]">
                                    <el-input
                                            v-model.string = "formData.condition_min_amount"
                                            type="number"
                                            size="medium"
                                            min="0.01">
                                        <template slot="prepend"><?php echo $this->configStore->currencySymbol; ?></template>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['condition_min_amount'] = ($this->promotionCoupon ? $this->promotionCoupon->condition_min_amount : '0'); ?>
                            </div>

                            <div class="be-col-auto" v-if="formData.condition === 'min_quantity'">
                                <el-form-item prop="condition_min_quantity" :rules="[{required: formData.condition === 'min_quantity', message: '请输入最低购买数量', trigger: 'change' }]">
                                    <el-input
                                            v-model.number = "formData.condition_min_quantity"
                                            type="number"
                                            size="medium"
                                            min="1">
                                        <template slot="append">件</template>
                                    </el-input>
                                </el-form-item>
                                <?php $formData['condition_min_quantity'] = ($this->promotionCoupon ? $this->promotionCoupon->condition_min_quantity : '0'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            适用商品
                        </div>

                        <div class="be-mt-100 be-lh-250">
                            <el-radio v-model="formData.scope_product" label="all">所有商品</el-radio>
                            <el-radio v-model="formData.scope_product" label="assign">指定商品</el-radio>
                            <el-radio v-model="formData.scope_product" label="category">指定分类</el-radio>
                            <?php $formData['scope_product'] = ($this->promotionCoupon ? $this->promotionCoupon->scope_product : 'all'); ?>
                        </div>

                        <div class="be-mt-100" v-if="formData.scope_product === 'assign'">
                            <div v-if="formData.scope_products.length > 0">
                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        已指定 {{formData.scope_products.length}} 个商品
                                    </div>
                                    <div class="be-col"></div>
                                    <div class="be-col-auto">
                                        <el-button size="medium" @click="selectProduct">选择商品</el-button>
                                    </div>
                                </div>

                                <el-table
                                        class="be-mt-100"
                                        ref = "scopeProductTableRef"
                                        :data="formData.scope_products.slice((productPage-1)*productPageSize,productPage*productPageSize)">
                                    <el-table-column
                                            label="图片"
                                            align="center"
                                            width="80">
                                        <template slot-scope="scope">
                                            <el-image :src="scope.row.image" fit="contain"></el-image>
                                        </template>
                                    </el-table-column>

                                    <el-table-column
                                            prop="name"
                                            label="名称"
                                            align="left">
                                    </el-table-column>

                                    <el-table-column
                                            prop="price_from"
                                            label="价格"
                                            align="center"
                                            width="160">
                                        <template slot-scope="scope">
                                            <?php echo $this->configStore->currencySymbol; ?>
                                            <template v-if="scope.row.price_from === scope.row.price_to">
                                                {{scope.row.price_from}}
                                            </template>
                                            <template v-else>
                                                {{scope.row.price_from}}~{{scope.row.price_to}}
                                            </template>
                                        </template>
                                    </el-table-column>

                                    <el-table-column
                                            label="操作"
                                            align="center"
                                            width="80">
                                        <template slot-scope="scope">
                                            <el-button
                                                    type="text"
                                                    size="medium"
                                                    icon="el-icon-delete"
                                                    @click="deleteProduct(scope.row)"></el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>

                                <div class="be-mt-50 be-ta-right">
                                    <el-pagination
                                            layout="prev, pager, next"
                                            :total="formData.scope_products.length"
                                            :page-size="productPageSize"
                                            @current-change="productPageChange"
                                            hide-on-single-page></el-pagination>
                                </div>
                            </div>
                            <div class="be-py-200" v-else>
                                <div class="be-ta-center be-fs-125 be-lh-300">请指定适用的商品</div>
                                <div class="be-ta-center be-mt-50 be-c-999">最多可选择500个商品</div>
                                <div class="be-ta-center be-mt-100">
                                    <el-button size="medium" @click="selectProduct">选择商品</el-button>
                                </div>
                            </div>
                            <?php $formData['scope_products'] = ($this->promotionCoupon ? $this->promotionCoupon->scope_products : []); ?>
                        </div>

                        <div class="be-mt-100" v-if="formData.scope_product === 'category'">
                            <div v-if="formData.scope_categories.length > 0">
                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        已指定 {{formData.scope_categories.length}} 个分类
                                    </div>
                                    <div class="be-col"></div>
                                    <div class="be-col-auto">
                                        <el-button size="medium" @click="selectCategory">选择分类</el-button>
                                    </div>
                                </div>

                                <el-table
                                        class="be-mt-100"
                                        ref = "scopeCategoryTableRef"
                                        :data="formData.scope_categories.slice((categoryPage-1)*categoryPageSize,categoryPage*categoryPageSize)">
                                    <el-table-column
                                            label="图片"
                                            align="center"
                                            width="80">
                                        <template slot-scope="scope">
                                            <el-image :src="scope.row.image_small" fit="contain"></el-image>
                                        </template>
                                    </el-table-column>

                                    <el-table-column
                                            prop="name"
                                            label="名称"
                                            align="left">
                                    </el-table-column>

                                    <el-table-column
                                            label="操作"
                                            align="center"
                                            width="80">
                                        <template slot-scope="scope">
                                            <el-button
                                                    type="text"
                                                    size="medium"
                                                    icon="el-icon-delete"
                                                    @click="deleteCategory(scope.row)"></el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>

                                <div class="be-mt-50 be-ta-right">
                                    <el-pagination
                                            layout="prev, pager, next"
                                            :total="formData.scope_categories.length"
                                            :page-size="categoryPageSize"
                                            @current-change="categoryPageChange"
                                            hide-on-single-page></el-pagination>
                                </div>
                            </div>
                            <div class="be-py-200" v-else>
                                <div class="be-ta-center be-fs-125 be-lh-300">请指定适用的分类</div>
                                <div class="be-ta-center be-mt-50 be-c-999">指定分类下的商品会自动应用优惠</div>
                                <div class="be-ta-center be-mt-100">
                                    <el-button size="medium" @click="selectCategory">选择分类</el-button>
                                </div>
                            </div>
                            <?php $formData['scope_categories'] = ($this->promotionCoupon ? $this->promotionCoupon->scope_categories : []); ?>
                        </div>

                        <div class="be-mt-150">
                            <el-checkbox v-model.number="formData.show" :true-label="1" :false-label="0">在商品详情页显示该优惠券，顾客点击后可以领取</el-checkbox>
                        </div>
                        <?php $formData['show'] = ($this->promotionCoupon ? $this->promotionCoupon->show : 0); ?>

                    </div>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            适用客户
                        </div>

                        <div class="be-mt-100 be-lh-250">
                            <el-radio v-model="formData.scope_user" label="all">所有客户</el-radio>
                            <el-radio v-model="formData.scope_user" label="assign">指定客户</el-radio>
                            <!--el-radio v-model="formData.scope_user" label="group">指定分组</el-radio-->
                            <?php $formData['scope_user'] = ($this->promotionCoupon ? $this->promotionCoupon->scope_user : 'all'); ?>
                        </div>

                        <div class="be-mt-100" v-if="formData.scope_user === 'assign'">
                            <div v-if="formData.scope_users.length > 0">
                                <div class="be-row">
                                    <div class="be-col-auto be-lh-250">
                                        已指定 {{formData.scope_users.length}} 个客户
                                    </div>
                                    <div class="be-col"></div>
                                    <div class="be-col-auto">
                                        <el-button size="medium" @click="selectUser">选择客户</el-button>
                                    </div>
                                </div>

                                <el-table
                                        class="be-mt-100"
                                        ref = "scopeUserTableRef"
                                        :data="formData.scope_users.slice((userPage-1)*userPageSize,userPage*userPageSize)">
                                    <el-table-column
                                            prop="email"
                                            label="邮箱"
                                            align="left">
                                    </el-table-column>

                                    <el-table-column
                                            prop="name"
                                            label="名称"
                                            align="left">
                                        <template slot-scope="scope">
                                            {{scope.row.first_name}} {{scope.row.last_name}}
                                        </template>
                                    </el-table-column>

                                    <el-table-column
                                            label="操作"
                                            align="center"
                                            width="80">
                                        <template slot-scope="scope">
                                            <el-button
                                                    type="text"
                                                    size="medium"
                                                    icon="el-icon-delete"
                                                    @click="deleteUser(scope.row)"></el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>

                                <div class="be-mt-50 be-ta-right">
                                    <el-pagination
                                            layout="prev, pager, next"
                                            :total="formData.scope_users.length"
                                            :page-size="userPageSize"
                                            @current-change="userPageChange"
                                            hide-on-single-page></el-pagination>
                                </div>
                            </div>
                            <div class="be-py-200" v-else>
                                <div class="be-ta-center be-fs-125 be-lh-300">请指定适用的客户</div>
                                <div class="be-ta-center be-mt-100">
                                    <el-button size="medium" @click="selectUser">选择客户</el-button>
                                </div>
                            </div>
                            <?php $formData['scope_users'] = ($this->promotionCoupon ? $this->promotionCoupon->scope_users : []); ?>
                        </div>

                        <div class="be-mt-100" v-if="formData.scope_user === 'group'">

                        </div>
                    </div>


                    <div class="be-p-150 be-bc-fff be-mt-150">

                        <div class="be-fs-110 be-fw-bold">
                            使用限制
                        </div>

                        <div class="be-mt-100">

                            <?php $formData['limit_quantity'] = ($this->promotionCoupon ? $this->promotionCoupon->limit_quantity : 0); ?>
                            <div class="be-row">
                                <div class="be-col-auto be-lh-250 be-pr-100">
                                    总发放量：
                                </div>
                                <div class="be-col-auto be-lh-250 be-pr-100">
                                    <el-radio v-model="formData.limit_quantity_type" label="none">不限</el-radio>
                                    <el-radio v-model="formData.limit_quantity_type" label="custom">指定数量</el-radio>
                                    <?php $formData['limit_quantity_type'] = $formData['limit_quantity'] === 0 ? 'none' : 'custom'; ?>
                                </div>
                                <div class="be-col-auto" v-if="formData.limit_quantity_type==='custom'">
                                    <el-form-item prop="limit_quantity" :rules="[{required: formData.limit_quantity_type === 'custom', message: '请输入总发放量', trigger: 'change' }]">
                                        <el-input
                                                v-model.number = "formData.limit_quantity"
                                                type="number"
                                                size="medium"
                                                min="1">
                                            <template slot="append">张</template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </div>

                            <div class="be-row be-mt-100">
                                <?php $formData['limit_times'] = ($this->promotionCoupon ? $this->promotionCoupon->limit_times : 0); ?>
                                <div class="be-col-auto be-lh-250 be-pr-100">
                                    每人可用次数：
                                </div>
                                <div class="be-col-auto be-lh-250 be-pr-100">
                                    <el-radio v-model="formData.limit_times_type" label="none">不限</el-radio>
                                    <el-radio v-model="formData.limit_times_type" label="custom">指定次数</el-radio>
                                    <?php $formData['limit_times_type'] = $formData['limit_times'] === 0 ? 'none' : 'custom'; ?>
                                </div>
                                <div class="be-col-auto" v-if="formData.limit_times_type==='custom'">
                                    <el-form-item prop="limit_times" :rules="[{required: formData.limit_times_type === 'custom', message: '请输入每人可用次数', trigger: 'change' }]">
                                        <el-input
                                                v-model.number = "formData.limit_times"
                                                type="number"
                                                size="medium"
                                                min="1">
                                            <template slot="append">次</template>
                                        </el-input>
                                    </el-form-item>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="be-p-150 be-bc-fff be-mt-150">

                        <div class="be-fs-110 be-fw-bold">
                            活动时间
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-auto be-lh-250 be-pr-100">
                                开始时间：
                            </div>
                            <div class="be-col-auto be-pr-100">
                                <el-date-picker
                                        v-model="formData.start_time"
                                        size="medium"
                                        type="datetime"
                                        value-format="yyyy-MM-dd HH:mm:ss">
                                </el-date-picker>
                            </div>
                            <div class="be-col-col be-lh-250">
                                <el-checkbox v-model.number="formData.never_expire" :true-label="1" :false-label="0">永不过期</el-checkbox>
                            </div>
                        </div>
                        <?php
                        $now = date('Y-m-d H:i:s');
                        $storeNow = \Be\Util\Time\Timezone::convert('Y-m-d H:i:s', $this->configStore->timezone, $now);

                        $formData['start_time'] = ($this->promotionCoupon ? $this->promotionCoupon->start_time : $storeNow);
                        $formData['never_expire'] = ($this->promotionCoupon ? $this->promotionCoupon->never_expire : 1);
                        ?>

                        <div class="be-row be-mt-100" v-if="formData.never_expire === 0">
                            <div class="be-col-auto be-lh-250 be-pr-100">
                                结束时间：
                            </div>
                            <div class="be-col-auto be-pr-100">
                                <el-date-picker
                                        v-model="formData.end_time"
                                        size="medium"
                                        type="datetime"
                                        value-format="yyyy-MM-dd HH:mm:ss">
                                </el-date-picker>
                            </div>
                        </div>
                        <?php
                        $storeNextMonth = \Be\Util\Time\Datetime::getNextMonth($formData['start_time']);
                        if ($this->promotionCoupon) {
                            if ($this->promotionCoupon->never_expire) {
                                $formData['end_time'] = $storeNextMonth;
                            } else {
                                $formData['end_time'] = $this->promotionCoupon->end_time;
                            }
                        } else {
                            $formData['end_time'] = $storeNextMonth;
                        }
                        ?>

                        <div class="be-mt-100 be-c-999">
                            店铺时区：<?php echo \Be\Util\Time\Timezone::getTimezoneName($this->configStore->timezone); ?> <el-link class="be-ml-100" type="primary" href="#">设置时区</el-link>
                        </div>

                    </div>

                </div>
                <div class="be-col-0 be-col-xxl-8 be-pl-150">

                     <div class="be-p-150 be-bc-fff">

                        <div class="be-fs-110 be-fw-bold">
                            概览
                        </div>

                        <div class="be-mt-100">
                            <div v-if="formData.code" class="be-fs-150 be-lh-300">{{formData.code}}</div>
                            <ul style="padding-left: 1.5rem;">
                                <li v-if="formData.discount_type === 'percent' && formData.discount_percent">
                                    <template v-if="formData.condition === 'none'">
                                        减免折扣：{{formData.discount_percent}}%
                                    </template>
                                    <template v-else-if="formData.condition === 'min_amount'">
                                        满 <?php echo $this->configStore->currencySymbol; ?>{{formData.condition_min_amount}} 减 {{formData.discount_percent}}%
                                    </template>
                                    <template v-else-if="formData.condition === 'min_quantity'">
                                        满 {{formData.condition_min_quantity}} 件减 {{formData.discount_percent}}%
                                    </template>
                                </li>
                                <li v-if="formData.discount_type === 'amount' && formData.discount_amount">
                                    <template v-if="formData.condition === 'none'">
                                        减免金额：<?php echo $this->configStore->currencySymbol; ?>{{formData.discount_amount}}
                                    </template>
                                    <template v-else-if="formData.condition === 'min_amount'">
                                        满 <?php echo $this->configStore->currencySymbol; ?>{{formData.condition_min_amount}} 减 <?php echo $this->configStore->currencySymbol; ?>{{formData.discount_amount}}
                                    </template>
                                    <template v-else-if="formData.condition === 'min_quantity'">
                                        满 {{formData.condition_min_quantity}} 件减 <?php echo $this->configStore->currencySymbol; ?>{{formData.discount_amount}}
                                    </template>
                                </li>

                                <li v-if="formData.scope_product === 'all'">所有商品适用</li>
                                <li v-if="formData.scope_product === 'assign' && formData.scope_products.length > 0">{{formData.scope_products.length}}个商品适用</li>
                                <li v-if="formData.scope_product === 'category' && formData.scope_categories.length > 0">{{formData.scope_categories.length}}个分类适用</li>

                                <li v-if="formData.scope_user === 'all'">所有客户适用</li>
                                <li v-if="formData.scope_user === 'assign' && formData.scope_users.length > 0">{{formData.scope_users.length}}个客户适用</li>


                                <li v-if="formData.limit_quantity_type === 'custom' || formData.limit_times_type === 'custom'">
                                    <template v-if="formData.limit_quantity_type === 'custom'">
                                        总发放量 {{formData.limit_quantity}} 张&nbsp;&nbsp;&nbsp;
                                    </template>
                                    <template v-if="formData.limit_times_type === 'custom'">
                                        每人可用 {{formData.limit_times}} 次
                                    </template>
                                </li>

                                <li v-if="formData.start_time">
                                    {{formData.start_time}} 开始
                                </li>

                            </ul>
                        </div>

                    </div>

                    <?php
                    if ($this->promotionCoupon) {
                    ?>
                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            活动数据
                        </div>
                        <div class="be-mt-100">
                            <div>使用次数</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->statistics['orderCount']; ?></div>

                            <div class="be-mt-100">优惠金额</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->configStore->currencySymbol . $this->statistics['discountAmount']; ?></div>

                            <div class="be-mt-100">销售总额</div>
                            <div class="be-fs-150 be-lh-300"><?php echo $this->configStore->currencySymbol . $this->statistics['orderAmount']; ?></div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>


                    <?php
                    if ($this->promotionCoupon && $this->changes) {
                        ?>
                        <div class="be-p-150 be-bc-fff be-mt-150">
                            <div class="be-fs-110 be-fw-bold">
                                修改记录
                            </div>
                            <div class="be-mt-100" style="max-height: 400px; overflow-y: auto;">
                                <?php
                                foreach ($this->changes as $change) {
                                    ?>
                                    <div class="be-mb-100 be-c-999"><?php echo $change->create_time; ?></div>
                                    <div class="be-mb-200">
                                        <?php
                                        if (count($change->details) > 0) {
                                            echo '<ul style="padding-left: 1.5rem;"><li>';
                                            echo implode('</li><li>', $change->details);
                                            echo '</li></ul>';
                                        } else {
                                            echo '主要项目未改动';
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>
            </div>


        </el-form>
    </div>

    <?php
    echo $uiItems->getJs();
    echo $uiItems->getCss();
    ?>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false,

                productPageSize: 6,
                productPage: 1,

                categoryPageSize: 6,
                categoryPage: 1,

                userPageSize: 6,
                userPage: 1,

                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {
                generateCode: function () {
                    let _this = this;
                    this.loading = true;
                    this.$http.get("<?php echo beAdminUrl('ShopFai.PromotionCoupon.generate'); ?>").then(function (response) {
                        _this.loading = false;
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.formData.code = responseData.result;
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
                },


                selectProduct: function () {
                    let formData = null;
                    if (this.formData.scope_products.length > 0) {
                        let productIds = [];
                        for (let product of this.formData.scope_products) {
                            productIds.push(product.id);
                        }

                        formData = {
                            exclude_ids: productIds.join(",")
                        }
                    }

                    console.log(formData);

                    be.openDrawer("选择商品", "<?php echo beAdminUrl('ShopFai.Product.picker', ['multiple'=>1, 'callback' => 'selectProducts']); ?>", {width: "60%"}, formData);
                },
                selectProducts: function (rows) {
                    for (let row of rows) {
                        let exist = false;
                        for (let product of this.formData.scope_products) {
                            if (row.id === product.id) {
                                exist = true;
                                break;
                            }
                        }
                        if (!exist) {
                            this.formData.scope_products.push(row);
                        }
                    }
                },
                deleteProduct: function (row) {
                    this.formData.scope_products.splice(this.formData.scope_products.indexOf(row), 1);
                },
                productPageChange: function (page) {
                    this.productPage = page;
                },


                selectCategory: function () {
                    let formData = null;
                    if (this.formData.scope_categories.length > 0) {
                        let categoryIds = [];
                        for (let category of this.formData.scope_categories) {
                            categoryIds.push(category.id);
                        }

                        formData = {
                            exclude_ids: categoryIds.join(",")
                        }
                    }

                    be.openDrawer("选择分类", "<?php echo beAdminUrl('ShopFai.Category.picker', ['multiple'=>1, 'callback' => 'selectCategories']); ?>", {width: "60%"}, formData);
                },
                selectCategories: function (rows) {
                    for (let row of rows) {
                        let exist = false;
                        for (let category of this.formData.scope_categories) {
                            if (row.id === category.id) {
                                exist = true;
                                break;
                            }
                        }
                        if (!exist) {
                            this.formData.scope_categories.push(row);
                        }
                    }
                },
                deleteCategory: function (row) {
                    this.formData.scope_categories.splice(this.formData.scope_categories.indexOf(row), 1);
                },
                categoryPageChange: function (page) {
                    this.categoryPage = page;
                },


                selectUser: function () {
                    let formData = null;
                    if (this.formData.scope_users.length > 0) {
                        let userIds = [];
                        for (let user of this.formData.scope_users) {
                            userIds.push(user.id);
                        }

                        formData = {
                            exclude_ids: userIds.join(",")
                        }
                    }

                    be.openDrawer("选择客户", "<?php echo beAdminUrl('ShopFai.User.picker', ['multiple'=>1, 'callback' => 'selectUsers']); ?>", {width: "60%"}, formData);
                },
                selectUsers: function (rows) {
                    for (let row of rows) {
                        let exist = false;
                        for (let user of this.formData.scope_users) {
                            if (row.id === user.id) {
                                exist = true;
                                break;
                            }
                        }
                        if (!exist) {
                            this.formData.scope_users.push(row);
                        }
                    }
                },
                deleteUser: function (row) {
                    this.formData.scope_users.splice(this.formData.scope_users.indexOf(row), 1);
                },
                userPageChange: function (page) {
                    this.userPage = page;
                },


                save: function () {
                    let _this = this;
                    this.$refs["formRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            vueNorth.loading = true;

                            if (_this.formData.limit_quantity_type === "none") {
                                _this.formData.limit_quantity = 0;
                            }

                            if (_this.formData.limit_times_type === "none") {
                                _this.formData.limit_times = 0;
                            }

                            _this.$http.post("<?php echo beAdminUrl('ShopFai.PromotionCoupon.' . ($this->promotionCoupon ? 'edit' :'create')); ?>", {
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
                                            window.location.href = "<?php echo beAdminUrl('ShopFai.PromotionCoupon.coupons'); ?>";
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
                    window.location.href = "<?php echo beAdminUrl('ShopFai.PromotionCoupon.coupons'); ?>";
                }

                <?php
                echo $uiItems->getVueMethods();
                ?>
            }

            <?php
            $uiItems->setVueHook('mounted', 'window.onbeforeunload = function(e) {e = e || window.event; if (e) { e.returnValue = ""; } return ""; };');
            $uiItems->setVueHook('updated', '
                let _this = this;
                this.$nextTick(function () {
                    if (_this.formData.scope_product === \'assign\' && _this.formData.scope_products.length > 0) {
                        _this.$refs.scopeProductTableRef.doLayout();
                    }

                    if (_this.formData.scope_product === \'category\' && _this.formData.scope_categories.length > 0) {
                        _this.$refs.scopeCategoryTableRef.doLayout();
                    }

                    if (_this.formData.scope_user === \'assign\' && _this.formData.scope_users.length > 0) {
                        _this.$refs.scopeUserTableRef.doLayout();
                    }
                });
            ');
            echo $uiItems->getVueHooks();
            ?>
        });

        function selectProducts(rows) {
            vueCenter.selectProducts(rows);
            be.closeDrawer();
        }

        function selectCategories(rows) {
            vueCenter.selectCategories(rows);
            be.closeDrawer();
        }

        function selectUsers(rows) {
            vueCenter.selectUsers(rows);
            be.closeDrawer();
        }
    </script>
</be-page-content>