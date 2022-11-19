<be-head>
    <style type="text/css">

        .el-table__row .el-divider__text,
        .el-table .el-link {
            margin-left: 4px;
            margin-right: 4px;
        }

        .el-table th.el-table__cell {
            color: #666;
            background-color: #EBEEF5;
        }

        .el-table__cell .el-avatar,
        .el-table__cell .el-image {
            display: block;
        }
    </style>
</be-head>


<be-center>
    <div id="app" v-cloak>

        <el-form size="medium" :inline="true">

            <el-tabs v-model="formData.related" type="card"  @tab-click="search">
                <el-tab-pane label="未设置关联的商品" name="0"></el-tab-pane>
                <el-tab-pane label="已设置关联的商品" name="1"></el-tab-pane>
            </el-tabs>

            <el-form-item>
                <el-select v-model="formData.is_enable" style="width: 120px;">
                    <el-option value="" label="全部状态"></el-option>
                    <el-option value="1" label="上架"></el-option>
                    <el-option value="0" label="下架"></el-option>
                </el-select>
            </el-form-item>

            <el-form-item>
                <el-input v-model="formData.name" clearable placeholder="商品名称"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" icon="el-icon-search" @click="search" :disabled="loading">搜索</el-button>
            </el-form-item>
        </el-form>

        <div class="be-c-red be-pb-100" v-if="formData.related === '1'">注意：如果您选择了已关联的商品，则该商品将从旧的关联中移除！</div>

        <el-table
                :data="gridData"
                ref="tableRef"
                v-loading="loading"
                size="medium"
                :height="tableHeight"
                @row-click="rowClick"
                @selection-change="selectionChange">
            <template slot="empty"><el-empty description="暂无数据"></el-empty></template>

            <el-table-column type="selection" width="60"></el-table-column>

            <el-table-column
                    prop="image"
                    label=""
                    align="center"
                    header-align="center"
                    width="50">
                <template slot-scope="scope">
                    <el-image :src="scope.row.image" style="width:40px; height: 40px;"></el-image>
                </template>
            </el-table-column>

            <el-table-column
                    prop="name"
                    label="商品信息"
                    align="left"
                    header-align="left">
                <template slot-scope="scope">
                    {{scope.row.name}}
                </template>
            </el-table-column>

            <el-table-column
                    prop="relate"
                    label="关联信息"
                    align="left"
                    header-align="left"
                    v-if="formData.related === '1'">
                <template slot-scope="scope">
                    已有关联（{{scope.row.relate.items.length}}个商品）
                </template>
            </el-table-column>

            <el-table-column
                    prop="is_enable"
                    label="状态"
                    align="center"
                    header-align="center"
                    width="80">
                <template slot-scope="scope">
                    <el-tag :type="scope.row.is_enable === '1' ? 'success' : 'info'">
                        {{scope.row.is_enable === "1" ? "上架" : "下架"}}
                    </el-tag>
                </template>
            </el-table-column>

        </el-table>


        <div class="be-row be-mt-50">
            <div class="be-col">
                <el-pagination
                        v-if="total > 0"
                        @size-change="changePageSize"
                        @current-change="gotoPage"
                        :current-page="page"
                        :page-sizes="[10, 15, 20, 25, 30, 50, 100]"
                        :page-size="pageSize"
                        layout="total, sizes, prev, pager, next, jumper"
                        :total="total">
                </el-pagination>
            </div>
            <div class="be-col-auto">
                <el-button type="primary" size="medium" icon="el-icon-check" @click="submit" :disabled="selectedProducts.length === 0">确定</el-button>
            </div>
        </div>

    </div>

    <script>
        var pageSizeKey = "<?php echo $this->url; ?>:pageSize";
        var pageSize = localStorage.getItem(pageSizeKey);
        if (pageSize === null || isNaN(pageSize)) {
            pageSize = <?php echo $this->pageSize; ?>;
        } else {
            pageSize = Number(pageSize);
        }

        let vueCenter = new Vue({
            el: '#app',
            data: {
                loading: false,
                formData: {
                    related: "0",
                    is_enable: "1",
                    name: "",
                    exclude_product_ids: "<?php echo $this->excludeProductIds; ?>"
                },

                gridData: [],
                pageSize: pageSize,
                page: 1,
                pages: 1,
                total: 0,
                tableHeight: 500,

                selectedProducts: [],

                t: false
            },
            methods: {
                selectionChange(val) {
                    this.selectedProducts = val;
                },
                rowClick(row) {
                    this.$refs.tableRef.toggleRowSelection(row);
                },
                submit: function () {
                    let products = [];
                    let product, relate_name, relate_icon_type, relate_value, relate_icon_image, relate_icon_color;
                    for (let p of this.selectedProducts) {
                        relate_name = "";
                        relate_icon_type = "";
                        relate_value = "";
                        relate_icon_image = "";
                        relate_icon_color = "";
                        if (p.relate_id !== ""){
                            relate_name = p.relate.name;
                            relate_icon_type = p.relate.icon_type;
                            for (let relateItem of p.relate.items) {
                                if (relateItem.product_id === p.id) {
                                    relate_value = relateItem.value;
                                    relate_icon_image = relateItem.icon_image;
                                    relate_icon_color = relateItem.icon_color;
                                    break;
                                }
                            }
                        }

                        product = {
                            product_id: p.id,
                            product_name: p.name,
                            product_image: p.image,
                            relate_id: p.relate_id,
                            relate_name: relate_name,
                            relate_icon_type: relate_icon_type,
                            relate_value: relate_value,
                            relate_icon_image: relate_icon_image,
                            relate_icon_color: relate_icon_color,
                        }
                        products.push(product);
                    }
                    parent.setRelate(products);
                },
                search: function () {
                    this.page = 1;
                    this.loadGridData();
                },
                loadGridData: function () {
                    this.loading = true;
                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl('Shop.Product.relate'); ?>", {
                        formData: _this.formData,
                        page: _this.page,
                        pageSize: _this.pageSize
                    }).then(function (response) {
                        _this.loading = false;
                        //console.log(response);
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.total = parseInt(responseData.data.total);
                                _this.gridData = responseData.data.gridData;
                                _this.pages = Math.floor(_this.total / _this.pageSize);
                            } else {
                                _this.total = 0;
                                _this.gridData = [];
                                _this.page = 1;
                                _this.pages = 1;

                                if (responseData.message) {
                                    _this.$message({
                                        showClose: true,
                                        message: responseData.message,
                                        type: 'error'
                                    });
                                }
                            }
                            _this.resize();
                        }
                    }).catch(function (error) {
                        _this.loading = false;
                        _this.$message.error(error);
                    });
                },
                reloadGridData: function () {
                    var _this = this;
                    _this.$http.post("<?php echo beAdminUrl('Shop.Product.relate'); ?>", {
                        formData: _this.formData,
                        page: _this.page,
                        pageSize: _this.pageSize
                    }).then(function (response) {
                        if (response.status === 200) {
                            var responseData = response.data;
                            if (responseData.success) {
                                _this.total = parseInt(responseData.data.total);
                                _this.gridData = responseData.data.gridData;
                                _this.pages = Math.floor(_this.total / _this.pageSize);
                            }
                            _this.resize();
                        }
                    });
                },
                changePageSize: function (pageSize) {
                    this.pageSize = pageSize;
                    this.page = 1;
                    localStorage.setItem(pageSizeKey, pageSize);
                    this.loadGridData();
                },
                gotoPage: function (page) {
                    this.page = page;
                    this.loadGridData();
                },

                resize: function () {
                    let offset = 60;
                    let rect = this.$refs.tableRef.$el.getBoundingClientRect();
                    //console.log(rect);
                    this.tableHeight = Math.max(document.documentElement.clientHeight - rect.top - offset, 100);
                },

                t: function () {
                }
            },
            created: function () {
                this.search();
            },
            mounted: function () {
                this.$nextTick(function () {
                    this.resize();
                    let _this = this;
                    window.onresize = function () {
                        _this.resize();
                    };
                });
            },
            updated: function () {
                let _this = this;
                this.$nextTick(function () {
                    _this.$refs.tableRef.doLayout();
                });
            }
        });
    </script>

</be-center>