<be-head>
    <style>
        .el-form-item {
            margin-bottom: inherit;
        }

        .el-form-item.is-error{
            margin-bottom:22px;
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



        .poster,
        .poster-selector {
            width: 100%;
            height: 148px;
            margin: 0 8px 8px 0;
            border: 1px dashed #c0ccda;
            border-radius: 6px;
            overflow: hidden;
            line-height: 148px;
            position: relative;
            text-align: center;
        }

        .poster-selector {
            cursor: pointer;
        }

        .poster-selector:hover {
            border-color: #409eff;
        }

        .poster-selector i {
            font-size: 28px;
            color: #8c939d;
        }

        .poster img {
            max-width: 100%;
            vertical-align: middle;
        }

        .poster .poster-actions {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 32px;
            line-height: 32px;
            background-color: rgba(0, 0, 0, 0.5);
            text-align: center;
            cursor: default;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .poster:hover .poster-actions {
            opacity: 1;
        }

        .poster .poster-action {
            color: #ddd;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

    </style>
</be-head>


<be-north>
    <div class="be-north" id="be-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo beAdminUrl('Shop.PromotionActivity.activities'); ?>">返回满减活动列表</el-link>
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
    $rootUrl = \Be\Be::getRequest()->getRootUrl();
    ?>

    <div id="app" v-cloak>
        <el-form ref="formRef" :model="formData" class="be-mb-400" size="medium">
            <?php
            $formData['id'] = ($this->promotionActivity ? $this->promotionActivity->id : '');
            ?>

            <div class="be-row">
                <div class="be-col-24 be-xxl-col-16">
                     <div class="be-p-150 be-bc-fff">

                        <div class="be-row">
                            <div class="be-col-auto be-lh-250 be-pr-100">
                                名称：
                            </div>
                            <div class="be-col">
                                <el-form-item prop="name" :rules="[{required: true, message: '请输入名称', trigger: 'change' }]">
                                    <el-input
                                            type="text"
                                            placeholder="例如：Black Friday Discounts"
                                            v-model = "formData.name"
                                            maxlength="60"
                                            show-word-limit
                                            @change="nameChange">
                                    </el-input>
                                </el-form-item>
                                <?php $formData['name'] = ($this->promotionActivity ? $this->promotionActivity->name : ''); ?>
                            </div>
                        </div>

                        <div class="be-mt-150 be-row">
                            <div class="be-col">
                                <el-checkbox v-model.number="formData.poster" :true-label="1" :false-label="0">展示活动页海报</el-checkbox>
                            </div>
                            <div class="be-col-auto">
                                <el-link type="primary" @click="drawerPoster=true">海报展示效果说明</el-link>
                            </div>
                        </div>
                        <?php $formData['poster'] = ($this->promotionActivity ? $this->promotionActivity->poster : 0); ?>

                        <div class="be-row be-mt-150" v-if="formData.poster === 1">
                            <div class="be-col-14">
                                <div>
                                    <span class="be-fs-weight">电脑端</span>
                                    <span class="be-c-999">建议尺寸：宽1920 X 高400</span>
                                </div>
                                <div class="be-row be-mt-50">
                                    <div class="be-col" v-if="formData.poster_desktop !== ''">
                                        <div :key="formData.poster_desktop" class="poster">
                                            <img :src="formData.poster_desktop">
                                            <div class="poster-actions">
                                                <span class="poster-action" @click="posterDesktopPreview()"><i class="el-icon-zoom-in"></i></span>
                                                <span class="poster-action" @click="posterDesktopRemove()"><i class="el-icon-delete"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="be-col" v-if="formData.poster_desktop === ''">
                                        <div class="poster-selector" @click="posterDesktopSelect" key="99999">
                                            <i class="el-icon-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="be-col-1"></div>
                            <div class="be-col-9">
                                <div>
                                    <span class="be-fs-weight">移动端</span>
                                    <span class="be-c-999">建议尺寸：宽375 X 高160</span>
                                </div>
                                <div class="be-row be-mt-50">
                                    <div class="be-col" v-if="formData.poster_mobile !== ''">
                                        <div :key="formData.poster_mobile" class="poster">
                                            <img :src="formData.poster_mobile">
                                            <div class="poster-actions">
                                                <span class="poster-action" @click="posterMobilePreview()"><i class="el-icon-zoom-in"></i></span>
                                                <span class="poster-action" @click="posterMobileRemove()"><i class="el-icon-delete"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="be-col" v-if="formData.poster_mobile === ''">
                                        <div class="poster-selector" @click="posterMobileSelect" key="99999">
                                            <i class="el-icon-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $formData['poster_desktop'] = ($this->promotionActivity ? $this->promotionActivity->poster_desktop : '');
                        $formData['poster_mobile'] = ($this->promotionActivity ? $this->promotionActivity->poster_mobile : '');
                        ?>
                    </div>

                    <el-dialog :visible.sync="posterDesktopPreviewVisible" center="true">
                        <div class="be-ta-center">
                            <img style="max-width: 100%;max-height: 400px;" :src="formData.poster_desktop" alt="">
                        </div>
                    </el-dialog>

                    <el-dialog :visible.sync="posterMobilePreviewVisible" center="true">
                        <div class="be-ta-center">
                            <img style="max-width: 100%;max-height: 160px;" :src="formData.poster_mobile" alt="">
                        </div>
                    </el-dialog>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            优惠类型
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-auto be-lh-250 be-pr-300">
                                <el-radio v-model="formData.discount_type" label="percent">百分比折扣</el-radio>
                                <el-radio v-model="formData.discount_type" label="amount">固定金额</el-radio>
                                <?php $formData['discount_type'] = ($this->promotionActivity ? $this->promotionActivity->discount_type : 'percent'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            优惠条件
                        </div>

                        <div class="be-row be-mt-100">
                            <div class="be-col-auto be-lh-250 be-pr-300">
                                <el-radio v-model="formData.condition" label="min_amount">需消费指定金额</el-radio>
                                <el-radio v-model="formData.condition" label="min_quantity">需购买指定数量</el-radio>
                                <?php $formData['condition'] = ($this->promotionActivity ? $this->promotionActivity->condition : 'min_amount'); ?>
                            </div>
                        </div>

                        <div class="be-row be-mt-100">
                            <el-table
                                    class="be-mt-100"
                                    ref = "scopeProductTableRef"
                                    :data="formData.discounts">
                                <el-table-column
                                        prop="amount"
                                        v-if="formData.condition === 'min_amount'"
                                        label="购买金额"
                                        align="left"
                                        width="250">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'discounts['+scope.$index+'].min_amount'" :rules="[{required: formData.condition === 'min_amount', message: '购买金额不能为空', trigger: 'change' }]">
                                            <el-input
                                                    v-model.string = "formData.discounts[scope.$index].min_amount"
                                                    type="number"
                                                    placeholder="0.00"
                                                    size="medium"
                                                    min="0.01">
                                                <template slot="prepend"><?php echo $this->configStore->currencySymbol; ?></template>
                                            </el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>

                                <el-table-column
                                        prop="quantity"
                                        v-if="formData.condition === 'min_quantity'"
                                        label="购买数量"
                                        align="left"
                                        width="250">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'discounts['+scope.$index+'].min_quantity'" :rules="[{required: formData.condition === 'min_quantity', message: '购买数量不能为空', trigger: 'change' }]">
                                            <el-input
                                                    v-model.number = "formData.discounts[scope.$index].min_quantity"
                                                    type="number"
                                                    placeholder="0"
                                                    size="medium"
                                                    min="1">
                                                <template slot="append">件</template>
                                            </el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>

                                <el-table-column
                                        prop="discount_percent"
                                        v-if="formData.discount_type === 'percent'"
                                        label="减免折扣"
                                        align="left"
                                        width="250">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'discounts['+scope.$index+'].discount_percent'" :rules="[{required: formData.discount_type === 'percent', message: '减免折扣不能为空', trigger: 'change' }]">
                                            <el-input
                                                v-model.number = "formData.discounts[scope.$index].discount_percent"
                                                type="number"
                                                placeholder="0"
                                                size="medium"
                                                min="1"
                                                max="99"
                                                maxLength="2">
                                                <template slot="append">%</template>
                                            </el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>

                                <el-table-column
                                        prop="discount_amount"
                                        v-if="formData.discount_type === 'amount'"
                                        label="减免金额"
                                        align="left"
                                        width="250">
                                    <template slot-scope="scope">
                                        <el-form-item :prop="'discounts['+scope.$index+'].discount_amount'" :rules="[{required: formData.discount_type === 'amount', message: '减免金额不能为空', trigger: 'change' }]">
                                            <el-input
                                                    v-model.string = "formData.discounts[scope.$index].discount_amount"
                                                    type="number"
                                                    placeholder="0.00"
                                                    size="medium"
                                                    min="0.01">
                                                <template slot="prepend"><?php echo $this->configStore->currencySymbol; ?></template>
                                            </el-input>
                                        </el-form-item>
                                    </template>
                                </el-table-column>

                                <el-table-column
                                        align="left">
                                    <template slot-scope="scope">
                                        <el-button
                                                type="text"
                                                v-if="formData.discounts.length > 1"
                                                icon="el-icon-delete"
                                                size="medium"
                                                @click="deleteDiscount(scope.row)"></el-button>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>

                        <div class="be-mt-100">
                            <el-button size="medium" @click="addDiscount">增加梯度优惠</el-button>
                        </div>

                        <div class="be-mt-100 be-c-999">
                            建议设置逐级递增的优惠梯度以吸引客户购买更多商品
                        </div>
                    </div>
                    <?php
                    if ($this->promotionActivity) {
                        $formData['discounts'] = $this->promotionActivity->discounts;
                    } else {
                        $formData['discounts'] = [[
                            'id' => '',
                            'min_amount' => '',
                            'min_quantity' => '',
                            'discount_percent' => '',
                            'discount_amount' => '',
                        ]];
                    }
                    ?>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-fs-110 be-fw-bold">
                            适用商品
                        </div>

                        <div class="be-mt-100 be-lh-250">
                            <el-radio v-model="formData.scope_product" label="all">所有商品</el-radio>
                            <el-radio v-model="formData.scope_product" label="assign">指定商品</el-radio>
                            <el-radio v-model="formData.scope_product" label="category">指定分类</el-radio>
                            <?php $formData['scope_product'] = ($this->promotionActivity ? $this->promotionActivity->scope_product : 'all'); ?>
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
                                        :data="formData.scope_products.slice((promotionActivityPage-1)*promotionActivityPageSize,promotionActivityPage*promotionActivityPageSize)">
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
                                            :page-size="promotionActivityPageSize"
                                            @current-change="promotionActivityPageChange"
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
                            <?php $formData['scope_products'] = ($this->promotionActivity ? $this->promotionActivity->scope_products : []); ?>
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
                                            <el-image :src="scope.row.image" fit="contain"></el-image>
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
                            <?php $formData['scope_categories'] = ($this->promotionActivity ? $this->promotionActivity->scope_categories : []); ?>
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

                        $formData['start_time'] = ($this->promotionActivity ? $this->promotionActivity->start_time : $storeNow);
                        $formData['never_expire'] = ($this->promotionActivity ? $this->promotionActivity->never_expire : 1);
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
                        if ($this->promotionActivity) {
                            if ($this->promotionActivity->never_expire) {
                                $formData['end_time'] = $storeNextMonth;
                            } else {
                                $formData['end_time'] = $this->promotionActivity->end_time;
                            }
                        } else {
                            $formData['end_time'] = $storeNextMonth;
                        }
                        ?>

                        <div class="be-mt-100 be-c-999">
                            店铺时区：<?php echo \Be\Util\Time\Timezone::getTimezoneName($this->configStore->timezone); ?> <el-link class="be-ml-100" type="primary" href="#">设置时区</el-link>
                        </div>

                    </div>

                    <?php
                    if ($this->promotionActivity && $this->changes) {
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
                <div class="be-col-0 be-xxl-col-8 be-pl-150">

                     <div class="be-p-150 be-bc-fff">

                        <div class="be-fs-110 be-fw-bold">
                            概览
                        </div>

                        <div class="be-mt-100">
                            <div v-if="formData.code" class="be-fs-150 be-lh-300">{{formData.name}}</div>
                            <ul style="padding-left: 1.5rem;">

                                <template v-for="discount in formData.discounts">
                                    <template v-if="formData.condition === 'min_amount' && discount.min_amount!== ''">
                                        <template v-if="formData.discount_type === 'percent' && discount.discount_percent!== ''">
                                            <li>满 <?php echo $this->configStore->currencySymbol; ?>{{discount.min_amount}} 减 {{discount.discount_percent}}%</li>
                                        </template>
                                        <template v-else-if="discount.discount_amount!== ''">
                                            <li>满 <?php echo $this->configStore->currencySymbol; ?>{{discount.min_amount}} 减 <?php echo $this->configStore->currencySymbol; ?>{{discount.discount_amount}}</li>
                                        </template>
                                    </template>
                                    <template v-if="formData.condition === 'min_quantity' && discount.min_quantity!== ''">
                                        <template v-if="formData.discount_type === 'percent' && discount.discount_percent!== ''">
                                            <li>满 {{discount.min_quantity}} 件减 {{discount.discount_percent}}%</li>
                                        </template>
                                        <template v-else-if="discount.discount_amount!== ''">
                                            <li>满 {{discount.min_quantity}} 件减 <?php echo $this->configStore->currencySymbol; ?>{{discount.discount_amount}} </li>
                                        </template>
                                    </template>
                                </template>

                                <li v-if="formData.scope_product === 'all'">所有商品适用</li>
                                <li v-if="formData.scope_product === 'assign' && formData.scope_products.length > 0">{{formData.scope_products.length}}个商品适用</li>
                                <li v-if="formData.scope_product === 'category' && formData.scope_categories.length > 0">{{formData.scope_categories.length}}个分类适用</li>

                                <li v-if="formData.start_time">
                                    {{formData.start_time}} 开始
                                </li>

                            </ul>
                        </div>

                    </div>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-row">
                            <div class="be-col">
                                <div class="be-fs-110 be-fw-bold">
                                    SEO（搜索引擎优化）
                                </div>
                            </div>
                            <div class="be-col-auto">
                                <el-link type="primary" @click="drawerSeo=true">编辑</el-link>
                            </div>
                        </div>
                        <div class="be-mt-100 be-t-break be-c-999 be-fs-80"><?php echo $rootUrl; ?>/activity/{{formData.url}}</div>
                        <div class="be-mt-100">{{formData.seo_title}}</div>
                        <div class="be-mt-100 be-t-ellipsis-2">{{formData.seo_description}}</div>
                    </div>

                    <div class="be-p-150 be-bc-fff be-mt-150">
                        <div class="be-row">
                            <div class="be-col">
                                <div class="be-fs-110 be-fw-bold">
                                    优惠展示
                                </div>
                            </div>
                            <div class="be-col-auto">
                                <el-link type="primary" @click="drawerDiscountText=true">编辑</el-link>
                            </div>
                        </div>

                        <div class="be-mt-100" v-html="discountText"></div>
                    </div>

                    <?php
                    if ($this->promotionActivity) {
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

                </div>
            </div>


        </el-form>

        <el-drawer
                :visible.sync="drawerPoster"
                title="海报展示效果说明"
                size="40%"
                :wrapper-closable="true"
                :destroy-on-close="true">
            <div class="be-px-150">
                <el-tabs v-model="drawerPosterActive" type="border-card">
                    <el-tab-pane label="移动端" name="mobile">
                        <img src="<?php echo \Be\Be::getProperty('App.Shop')->getWwwUrl(); ?>/image/demo/promotion-activity-poster-mobile.png" alt="" class="be-mw-100">
                    </el-tab-pane>
                    <el-tab-pane label="电礅端" name="desktop">
                        <img src="<?php echo \Be\Be::getProperty('App.Shop')->getWwwUrl(); ?>/image/demo/promotion-activity-poster-desktop.png" alt="" class="be-mw-100">
                    </el-tab-pane>
                </el-tabs>
            </div>
        </el-drawer>

        <el-drawer
                :visible.sync="drawerSeo"
                title="搜索引擎优化"
                size="40%"
                :wrapper-closable="false"
                :destroy-on-close="true">

            <div class="be-px-150">

                <div>
                    <el-checkbox v-model.number="formData.seo" :true-label="1" :false-label="0">独立编辑</el-checkbox>
                    <el-tooltip effect="dark" content="单独编辑SEO后,SEO信息不随名称改动" placement="top">
                        <i class="el-icon-fa fa-question-circle-o"></i>
                    </el-tooltip>
                </div>
                <?php
                $formData['seo'] = ($this->promotionActivity ? $this->promotionActivity->seo : 0);
                ?>

                <div class="be-mt-150">
                    SEO标题
                    <el-tooltip effect="dark" content="标题是SEO最重要的部分，该标题会显示在搜索引擎的搜索结果中。" placement="top">
                        <i class="el-icon-fa fa-question-circle-o"></i>
                    </el-tooltip>
                </div>
                <el-input
                        class="be-mt-50"
                        type="text"
                        placeholder="请输入SEO标题"
                        v-model = "formData.seo_title"
                        size="medium"
                        maxlength="200"
                        show-word-limit
                        :disabled="formData.seo === 0">
                </el-input>
                <?php
                $formData['seo_title'] = ($this->promotionActivity ? $this->promotionActivity->seo_title : '');
                ?>

                <div class="be-mt-150">
                    SEO描述
                    <el-tooltip effect="dark" content="建议详细描述满减活动以吸引客户访问，不要堆砌关键词。" placement="top">
                        <i class="el-icon-fa fa-question-circle-o"></i>
                    </el-tooltip>
                </div>
                <el-input
                        class="be-mt-50"
                        type="textarea"
                        :rows="6"
                        placeholder="请输入SEO描述"
                        v-model = "formData.seo_description"
                        size="medium"
                        maxlength="500"
                        show-word-limit>
                </el-input>
                <?php
                $formData['seo_description'] = ($this->promotionActivity ? $this->promotionActivity->seo_description : '');
                ?>


                <div class="be-mt-150">
                    SEO友好链接
                </div>
                <el-input
                        class="be-mt-50"
                        type="text"
                        placeholder="请输入SEO友好链接"
                        v-model = "formData.url"
                        size="medium"
                        maxlength="200"
                        show-word-limit
                        :disabled="formData.seo === 0">
                    <template slot="prepend"><?php echo $rootUrl; ?>/activity/</template>
                </el-input>

                <?php
                $formData['url'] = ($this->promotionActivity ? $this->promotionActivity->url : '');
                ?>

                <div class="be-mt-150">
                    SEO关键词
                    <el-tooltip effect="dark" content="关键词可以提高搜索结果排名，建议1-2个关键词即可，堆砌关键词可能会降低排名！" placement="top">
                        <i class="el-icon-fa fa-question-circle-o"></i>
                    </el-tooltip>
                </div>
                <el-input
                        class="be-mt-50"
                        type="text"
                        placeholder="请输入SEO关键词，多个关键词以逗号分隔。"
                        v-model = "formData.seo_keywords"
                        size="medium"
                        maxlength="60">
                </el-input>
                <?php
                $formData['seo_keywords'] = ($this->promotionActivity ? $this->promotionActivity->seo_keywords : '');
                ?>

                <div class="be-mt-150 be-ta-right">
                    <el-button size="medium" type="primary" @click="drawerSeo=false">确定</el-button>
                </div>

            </div>

        </el-drawer>

        <el-drawer
                :visible.sync="drawerDiscountText"
                title="优惠展示"
                size="40%"
                :wrapper-closable="false"
                :destroy-on-close="true">

            <div class="be-px-150">

                <div class="be-fs-110 be-fw-bold">
                    优惠文案设置
                </div>
                <el-input
                        class="be-mt-50"
                        type="text"
                        placeholder="请输入优惠文案设置"
                        v-model = "formData.discount_text"
                        size="medium"
                        maxlength="200"
                        show-word-limit>
                </el-input>
                <?php
                $formData['discount_text'] = ($this->promotionActivity ? $this->promotionActivity->discount_text : 'Buy {优惠条件} get {优惠值} OFF');
                ?>
                <div class="be-mt-50">
                    <span class="be-c-999">动态变量文字：</span> <el-tag>{优惠条件}</el-tag> <el-tag>{优惠值}</el-tag>
                </div>

                <div class="be-mt-100 be-ta-right">
                    <el-button size="medium" type="primary" @click="drawerDiscountText=false">确定</el-button>
                </div>

                <div class="be-mt-100 be-fs-110 be-fw-bold">
                    预览示例（不同的主题实际效果有一定差异）
                </div>
                <div class="be-mt-150">

                    <div class="be-row">
                        <div class="be-col-auto">
                            <img src="<?php echo \Be\Be::getProperty('App.Shop')->getWwwUrl(); ?>/image/product/demo.png" alt="" style="max-width: 140px;">
                        </div>
                        <div class="be-col">
                            <div class="be-pl-200">
                                <div class="be-bc-eee be-w-50 be-py-50">&nbsp;</div>
                                <div class="be-mt-100 be-bc-eee">&nbsp;</div>

                                <div class="be-mt-100 be-c-eee be-fs-300">$$$$</div>

                                <div class="be-mt-100 be-px-50 be-py-10" v-html="discountText" style="color: #c00; background-color:#FFE5E5"></div>

                                <div class="be-mt-200 be-bc-eee be-ta-center be-c-999 be-py-50">Add to Cart</div>

                                <div class="be-mt-200 be-bc-eee">&nbsp;</div>
                                <div class="be-mt-50 be-bc-eee">&nbsp;</div>
                                <div class="be-mt-50 be-bc-eee">&nbsp;</div>
                                <div class="be-mt-50 be-bc-eee">&nbsp;</div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>

        </el-drawer>

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

                drawerPoster: false,
                drawerPosterActive: "mobile",
                posterDesktopPreviewVisible: false,
                posterMobilePreviewVisible: false,

                drawerSeo: false,
                drawerDiscountText: false,

                promotionActivityPageSize: 6,
                promotionActivityPage: 1,

                categoryPageSize: 6,
                categoryPage: 1,

                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {

                nameChange: function () {
                    while (this.formData.name.substr(0, 1) === " ") {
                        this.formData.name = this.formData.name.substr(1);
                    }

                    let len = this.formData.name.length;
                    while (this.formData.name.substr(len - 1, len) === " ") {
                        this.formData.name = this.formData.name.substr(0, len - 1);
                        len = this.formData.name.length;
                    }

                    this.seoUpdate();
                },


                posterDesktopSelect: function () {
                    <?php
                    $callback = base64_encode('parent.posterDesktopSelected(files);');
                    $selectorUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $callback]);
                    ?>
                    let selectorUrl = "<?php echo $selectorUrl; ?>";
                    be.openDialog("选择海服电脑端图像", selectorUrl);
                },
                posterDesktopSelected: function (files) {
                    if (files.length > 0) {
                        let file = files[0];
                        this.formData.poster_desktop = file.url;
                    }
                    be.closeDialog();
                },
                posterDesktopPreview: function () {
                    this.posterDesktopPreviewVisible = true;
                },
                posterDesktopRemove: function () {
                    this.formData.poster_desktop = "";
                },

                posterMobileSelect: function () {
                    <?php
                    $callback = base64_encode('parent.posterMobileSelected(files);');
                    $selectorUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $callback]);
                    ?>
                    let selectorUrl = "<?php echo $selectorUrl; ?>";
                    be.openDialog("选择海服移动端图像", selectorUrl);
                },
                posterMobileSelected: function (files) {
                    if (files.length > 0) {
                        let file = files[0];
                        this.formData.poster_mobile = file.url;
                    }
                    be.closeDialog();
                },
                posterMobilePreview: function () {
                    this.posterMobilePreviewVisible = true;
                },
                posterMobileRemove: function () {
                    this.formData.poster_mobile = "";
                },

                seoUpdate: function () {
                    if (this.formData.seo === 0) {
                        this.formData.seo_title = this.formData.name;

                        let url = this.formData.name.toLowerCase();

                        url = url.replace(/[^a-z0-9]/g, '-');

                        while (url.indexOf(' ') >= 0) {
                            url = url.replace(' ', '-');
                        }

                        while (url.indexOf('--') >= 0) {
                            url = url.replace('--', '-');
                        }

                        this.formData.url = url;
                    }
                },

                addDiscount: function () {
                    this.formData.discounts.push({
                        id: '',
                        min_amount: '',
                        min_quantity: '',
                        discount_percent: '',
                        discount_amount: '',
                    });
                },
                deleteDiscount: function (row) {
                    this.formData.discounts.splice(this.formData.discounts.indexOf(row), 1);
                },

                selectProduct: function () {
                    let formData = null;
                    if (this.formData.scope_products.length > 0) {
                        let promotionActivityIds = [];
                        for (let promotionActivity of this.formData.scope_products) {
                            promotionActivityIds.push(promotionActivity.id);
                        }

                        formData = {
                            exclude_ids: promotionActivityIds.join(",")
                        }
                    }

                    be.openDrawer("选择商品", "<?php echo beAdminUrl('Shop.Product.picker', ['multiple'=>1, 'callback' => 'selectProducts']); ?>", {width: "60%"}, formData);
                },
                selectProducts: function (rows) {
                    for (let row of rows) {
                        let exist = false;
                        for (let promotionActivity of this.formData.scope_products) {
                            if (row.id === promotionActivity.id) {
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
                promotionActivityPageChange: function (page) {
                    this.promotionActivityPage = page;
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

                    be.openDrawer("选择分类", "<?php echo beAdminUrl('Shop.Category.picker', ['multiple'=>1, 'callback' => 'selectCategories']); ?>", {width: "60%"}, formData);
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

                            _this.$http.post("<?php echo beAdminUrl('Shop.PromotionActivity.' . ($this->promotionActivity ? 'edit' :'create')); ?>", {
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
                                            window.location.href = "<?php echo beAdminUrl('Shop.PromotionActivity.activities'); ?>";
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
                    window.location.href = "<?php echo beAdminUrl('Shop.PromotionActivity.activities'); ?>";
                }

                <?php
                echo $uiItems->getVueMethods();
                ?>
            },
            computed: {
                discountText: function () {
                    let text = "";
                    if (this.formData.discount_text !== "") {
                        for (let discount of this.formData.discounts) {
                            let param1 = "";
                            let param2 = "";
                            if (this.formData.condition === 'min_amount' && discount.min_amount!== '') {
                                if (this.formData.discount_type === 'percent' && discount.discount_percent!== '') {
                                    param1 = "<?php echo $this->configStore->currencySymbol; ?>" + discount.min_amount;
                                    param2 = discount.discount_percent + "%";
                                } else if (discount.discount_amount!== '') {
                                    param1 = "<?php echo $this->configStore->currencySymbol; ?>" + discount.min_amount;
                                    param2 = "<?php echo $this->configStore->currencySymbol; ?>" + discount.discount_amount;
                                }
                            }

                            if (this.formData.condition === 'min_quantity' && discount.min_quantity!== '') {
                                if (this.formData.discount_type === 'percent' && discount.discount_percent!== '') {
                                    param1 = discount.min_quantity;
                                    param2 = discount.discount_percent + "%";
                                } else if (discount.discount_amount!== '') {
                                    param1 = discount.min_quantity;
                                    param2 = "<?php echo $this->configStore->currencySymbol; ?>" + discount.discount_amount;
                                }
                            }

                            if (param1 !== "" && param2 !== "") {
                                let textItem = this.formData.discount_text;
                                while (textItem.indexOf('{优惠条件}') >= 0) {
                                    textItem = textItem.replace('{优惠条件}', param1);
                                }

                                while (textItem.indexOf('{优惠值}') >= 0) {
                                    textItem = textItem.replace('{优惠值}', param2);
                                }

                                text += "<div class=\"be-py-50\">" + textItem + "</div>";
                            }
                        }
                    }

                    if (text === "") {
                        return "<div class=\"be-py-50\">" + this.formData.discount_text + "</div>";
                    }

                    return text;
                }
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
                });
            ');
            echo $uiItems->getVueHooks();
            ?>
        });

        function posterDesktopSelected(files) {
            vueCenter.posterDesktopSelected(files);
        }

        function posterMobileSelected(files) {
            vueCenter.posterMobileSelected(files);
        }

        function selectProducts(rows) {
            vueCenter.selectProducts(rows);
            be.closeDrawer();
        }

        function selectCategories(rows) {
            vueCenter.selectCategories(rows);
            be.closeDrawer();
        }
    </script>
</be-page-content>