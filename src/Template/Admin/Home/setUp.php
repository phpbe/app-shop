


<be-page-content>
    <div id="app" v-cloak>

        <div class="be-p-150 be-bc-fff">
            <div class="be-fs-150 be-pb-50 be-bb-eee">
                三步快速上手您的店铺
            </div>

            <div class="be-mt-200">
                <el-tabs type="card" tab-position="left">
                    <el-tab-pane class="be-pl-200">
                        <?php
                        if (($this->configStore->setUp & 1) === 0) {
                            ?>
                            <span slot="label" class="be-fs-125 be-px-200"><i class="el-icon-goods"></i> 添加商品</span>
                            <div class="be-fs-200">
                                添加或导入商品
                            </div>

                            <div class="be-mt-100 be-c-999">
                                添加并上架您的第一件商品，用户才可以在您的网站上进行购物
                            </div>

                            <div class="be-mt-400">
                                <el-button type="primary" size="medium" @click="window.location.href='<?php echo beAdminUrl('Shop.Product.products'); ?>'">去添加</el-button>
                            </div>
                            <?php
                        } else {
                            ?>
                            <span slot="label" class="be-fs-125 be-px-200"><i class="el-icon-goods"></i> 添加商品 <i class="el-icon-success be-c-green"></i></span>
                            <div class="be-fs-200">
                                您已添加了新产品
                            </div>

                            <div class="be-mt-100 be-c-999">
                                您还可以继续添加更多产品
                            </div>

                            <div class="be-mt-400">
                                <el-button type="primary" size="medium" @click="window.location.href='<?php echo beAdminUrl('Shop.Product.products'); ?>'">继续添加</el-button>
                            </div>
                            <?php
                        }
                        ?>
                    </el-tab-pane>
                    <el-tab-pane class="be-pl-200">
                        <?php
                        if (($this->configStore->setUp & 2) === 0) {
                            ?>
                            <span slot="label" class="be-fs-125 be-px-200"><i class="el-icon-bank-card"></i> 配置物流</span>
                            <div class="be-fs-200">
                                设置物流运费
                            </div>

                            <div class="be-mt-100 be-c-999">
                                支持按重量等多个维度设置不同国家/地区的物流模版，也可同时支持多个方案。
                            </div>

                            <div class="be-mt-400">
                                <el-button type="primary" size="medium" @click="window.location.href='<?php echo beAdminUrl('Shop.Shipping.index'); ?>'">设置物流运费</el-button>
                            </div>
                            <?php
                        } else {
                            ?>
                            <span slot="label" class="be-fs-125 be-px-200"><i class="el-icon-bank-card"></i> 配置物流 <i class="el-icon-success be-c-green"></i></span>
                            <div class="be-fs-200">
                                您已设置物流运费
                            </div>

                            <div class="be-mt-100 be-c-999">
                                支持按重量等多个维度设置不同国家/地区的物流模版，也可同时支持多个方案。
                            </div>

                            <div class="be-mt-400">
                                <el-button type="primary" size="medium" @click="window.location.href='<?php echo beAdminUrl('Shop.Shipping.index'); ?>'">设置物流运费</el-button>
                            </div>
                            <?php
                        }
                        ?>
                    </el-tab-pane>
                    <el-tab-pane class="be-pl-200">
                        <?php
                        if (($this->configStore->setUp & 4) === 0) {
                            ?>
                            <span slot="label" class="be-fs-125 be-px-200"><i class="el-icon-truck"></i> 设定收款</span>
                            <div class="be-fs-200">
                                设置收款方式
                            </div>

                            <div class="be-mt-100 be-c-999">
                                可根据您售卖的地区设置合适的付款方式
                            </div>

                            <div class="be-mt-400">
                                <el-button type="primary" size="medium" @click="window.location.href='<?php echo beAdminUrl('Shop.Payment.payments'); ?>'">设置收款方式</el-button>
                            </div>
                            <?php
                        } else {
                            ?>
                            <span slot="label" class="be-fs-125 be-px-200"><i class="el-icon-truck"></i> 设定收款 <i class="el-icon-success be-c-green"></i></span>
                            <div class="be-fs-200">
                                您已设置设置收款方式
                            </div>

                            <div class="be-mt-100 be-c-999">
                                可根据您售卖的地区设置合适的付款方式
                            </div>

                            <div class="be-mt-400">
                                <el-button type="primary" size="medium" @click="window.location.href='<?php echo beAdminUrl('Shop.Payment.payments'); ?>'">设置收款方式</el-button>
                            </div>
                            <?php
                        }
                        ?>
                    </el-tab-pane>
                </el-tabs>
            </div>
        </div>

        <div class="be-mt-150 be-p-150 be-bc-fff">

            <div class="be-fs-150 be-pb-50 be-bb-eee">
                装修网店
            </div>
            <div class="be-row be-mt-200">
                <div class="be-col">
                    <div class="be-fs-125">
                        当前主题：<?php echo $this->themeProperty->getLabel(); ?>（<?php echo $this->themeProperty->getName(); ?>）
                    </div>
                    <div class="be-mt-100 be-c-999">
                        预设主题能帮助你快速搭建网店。同时你也可以在主题商场更换其他心仪的模板。
                    </div>

                    <div class="be-mt-400">
                        <el-button size="medium" @click="window.location.href='<?php echo beAdminUrl('System.Theme.setting', ['themeName' => $this->themeProperty->getName()]); ?>'">前往装修店铺</el-button>
                        <el-button size="medium" @click="window.location.href='<?php echo beAdminUrl('System.Theme.themes'); ?>'">主题商城</el-button>
                    </div>
                </div>
                <div class="be-col-auto">
                    <img style="max-width:300px;" src="<?php echo $this->themeProperty->getPreviewImageUrl(); ?>" alt="<?php echo $this->themeProperty->getLabel(); ?>">
                </div>
            </div>
        </div>

    </div>

    <script>
        let vueCenter = new Vue({el: '#app'});
    </script>
</be-page-content>