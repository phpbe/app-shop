<be-head>
    <style>
        .product-header {
            color: #666;
            background-color: #EBEEF5;
            height: 3rem;
            line-height: 3rem;
            margin-bottom: .5rem;
        }

        .product-op {
            color: #666;
            border-bottom: #EBEEF5 1px solid;
            padding-top: .5rem;
            padding-bottom: .5rem;
            margin-bottom: 2px;
        }

        .product {
            border-bottom: #EBEEF5 1px solid;
            padding-top: .5rem;
            padding-bottom: .5rem;
            margin-bottom: 2px;
        }
    </style>
</be-head>

<be-page-content>
    <?php
    $rootUrl = \Be\Be::getRequest()->getRootUrl();
    $formData = ['products' => $this->products];
    ?>

    <div id="app" v-cloak>
        <div style="position: absolute; left: 0; right: 0; top: 0; bottom: 6rem; overflow-y: auto;">

            <div class="product-header">
                <div class="be-row">
                    <div class="be-col-12 be-fw-bold be-pl-100">
                        商品
                    </div>
                    <div class="be-col-12 be-fw-bold be-ta-center">
                        设置分类
                    </div>
                </div>
            </div>

            <div class="product-op">
                <div class="be-row">
                    <div class="be-col">
                    </div>
                    <div class="be-col-auto be-lh-250">
                        选择分类：
                    </div>
                    <div class="be-col-auto be-pl-100">
                        <el-select
                                v-model="categoryId"
                                placeholder="请选择分类"
                                filterable
                                size="medium">
                            <?php
                            foreach ($this->categoryKeyValues as $key => $val) {
                                echo '<el-option value="'. $key .'" key="'. $key .'" label="' .$val . '"></el-option>';
                            }
                            ?>
                        </el-select>
                    </div>
                    <div class="be-col-auto be-pl-100">
                        <el-button type="success" size="medium" @click="addCategory">确认加入</el-button>
                    </div>
                    <div class="be-col-auto be-pl-100">
                        <el-button type="danger" size="medium" @click="removeCategory">确认移出</el-button>
                    </div>
                    <div class="be-col">
                    </div>
                </div>
            </div>


            <div class="product" v-for="product, productIndex in formData.products" :key="product.id">
                <div class="be-row">
                    <div class="be-col-12 be-pl-100 be-lh-250">
                        {{product.name}}
                    </div>
                    <div class="be-col-12 be-pr-100">
                        <el-select
                                v-model="product.categoryIds"
                                multiple
                                placeholder="请选择分类"
                                size="medium" style="min-width: 100%">
                            <?php
                            foreach ($this->categoryKeyValues as $key => $val) {
                                echo '<el-option value="'. $key .'" key="'. $key .'" label="' .$val . '"></el-option>';
                            }
                            ?>
                        </el-select>
                    </div>
                </div>
            </div>
        </div>

        <div class="be-row be-bt" style="position: absolute; left: 0; right: 0; bottom: 0; height: 5rem; line-height: 4rem;">
            <div class="be-col"></div>
            <div class="be-col-auto">
                <el-button type="primary" icon="el-icon-check" @click="importConfirm" :disable="loading">确认保存</el-button>
            </div>
        </div>

    </div>

    <script>
        let vueCenter = new Vue({
            el: '#app',
            data: {
                categoryId: '',
                formData: <?php echo json_encode($formData); ?>,
                loading: false,
                t: false
            },
            methods: {
                importConfirm: function () {
                    let _this = this;
                    _this.loading = true;
                    _this.$http.post("<?php echo beAdminUrl('Shop.Product.batchSetCategoriesSave'); ?>", {
                        formData: _this.formData
                    }).then(function (response) {
                        _this.loading = false;
                        //console.log(response);
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.$message.success(responseData.message);
                                setTimeout(function () {
                                    parent.closeAndReload();
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
                        _this.$message.error(error);
                    });
                },
                addCategory() {
                    for (let product of this.formData.products) {
                        if (product.categoryIds.indexOf(this.categoryId) === -1) {
                            product.categoryIds.push(this.categoryId);
                        }
                    }
                },
                removeCategory() {
                    for (let product of this.formData.products) {
                        if (product.categoryIds.indexOf(this.categoryId) !== -1) {
                            product.categoryIds.splice(product.categoryIds.indexOf(this.categoryId), 1);
                        }
                    }
                }
            }
        });
    </script>

</be-page-content>