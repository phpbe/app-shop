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

        .image, .image-selector {
            display: inline-block;
            width: 148px;
            height: 148px;
            margin: 0 8px 8px 0;
            border: 1px dashed #c0ccda;
            border-radius: 6px;
            overflow: hidden;
            line-height: 148px;
            position: relative;
            text-align: center;
        }

        .image-selector {
            cursor: pointer;
        }
        .image-selector:hover {
            border-color: #409eff;
        }

        .image-selector i {
            font-size: 28px;
            color: #8c939d;
        }

        .image img {
            max-width: 100%;
            vertical-align: middle;
        }

        .image .image-actions {
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

        .image:hover .image-actions {
            opacity: 1;
        }

        .image .image-action {
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
                    <el-link icon="el-icon-back" href="<?php echo beAdminUrl('Shop.Category.categories'); ?>">返回分类列表</el-link>
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
        <div class="be-p-150">
            <el-form ref="formRef" :model="formData" class="be-mb-400">
                <?php
                $formData['id'] = ($this->category ? $this->category->id : '');
                ?>

                <div class="be-row">
                    <div class="be-col-24 be-md-col-18">
                         <div class="be-p-150 be-bc-fff">
                            <div><span class="be-c-red">*</span> 分类名称：</div>
                            <el-form-item class="be-mt-50" prop="name" :rules="[{required: true, message: '请输入分类名称', trigger: 'change' }]">
                                <el-input
                                        type="text"
                                        placeholder="请输入分类名称"
                                        v-model = "formData.name"
                                        size="medium"
                                        maxlength="120"
                                        show-word-limit
                                        @change="nameChange">
                                </el-input>
                            </el-form-item>
                            <?php $formData['name'] = ($this->category ? $this->category->name : ''); ?>

                            <div class="be-mt-100">分类描述：</div>
                            <?php

                            $fileCallback = base64_encode('parent.window.befile.selectedFiles = files;');
                            $imageCallback = base64_encode('parent.window.beimage.selectedFiles = files;');

                            $driver = new \Be\AdminPlugin\Form\Item\FormItemTinymce([
                                'name' => 'description',
                                'ui' => [
                                    'form-item' => [
                                        'class' => 'be-mt-50'
                                    ],
                                    '@change' => 'seoUpdate',
                                ],
                                'layout' => 'simple',
                            ]);
                            echo $driver->getHtml();

                            $formData['description'] = ($this->category ? $this->category->description : '');

                            $uiItems->add($driver);
                            ?>

                        </div>
                    </div>


                    <div class="be-col-24 be-md-col-6 be-pl-150">

                         <div class="be-p-150 be-bc-fff">

                            <div class="be-row">
                                <div class="be-col">是否启用：</div>
                                <div class="be-col-auto">
                                    <el-form-item prop="is_enable">
                                        <el-switch v-model.number="formData.is_enable" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </el-form-item>
                                </div>
                            </div>
                            <?php $formData['is_enable'] = ($this->category ? $this->category->is_enable : 0); ?>


                            <div class="be-row be-mt-200">
                                <div class="be-col be-lh-250">排序：</div>
                                <div class="be-col-auto">
                                    <el-form-item prop="ordering">
                                        <el-input-number
                                                v-model = "formData.ordering"
                                                size="medium">
                                        </el-input-number>
                                    </el-form-item>
                                </div>
                            </div>
                            <?php $formData['ordering'] = ($this->category ? $this->category->ordering : ''); ?>


                            <div class="be-mt-150">封面图片：</div>
                            <div class="be-row be-mt-50">
                                <div class="be-col-auto">
                                    <div v-if="formData.image !== ''" :key="formData.image" class="image">
                                        <img :src="formData.image">
                                        <div class="image-actions">
                                            <span class="image-action" @click="imagePreview()"><i class="el-icon-zoom-in"></i></span>
                                            <span class="image-action" @click="imageRemove()"><i class="el-icon-delete"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="be-col-auto">
                                    <div class="image-selector" @click="imageSelect" key="99999">
                                        <i class="el-icon-plus"></i>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $formData['image'] = ($this->category ? $this->category->image : '');
                            ?>


                            <el-dialog :visible.sync="imageSelectorVisible" class="dialog-image-selector" title="选择主图" :width="600" :close-on-click-modal="false">
                                <iframe :src="imageSelectorUrl" style="width:100%;height:400px;border:0;}"></iframe>
                            </el-dialog>

                            <el-dialog :visible.sync="imagePreviewVisible" center="true">
                                <div class="be-ta-center">
                                    <img style="max-width: 100%;max-height: 400px;" :src="formData.image" alt="">
                                </div>
                            </el-dialog>

                            <el-dialog :visible.sync="imageAltVisible" center="true">
                            </el-dialog>
                        </div>

                        <div class="be-p-150 be-bc-fff be-mt-200">
                            <div class="be-row">
                                <div class="be-col">
                                    <div class="be-fs-110">
                                        SEO（搜索引擎优化）
                                    </div>
                                </div>
                                <div class="be-col-auto">
                                    <el-link type="primary" @click="drawerSeo=true">编辑</el-link>
                                </div>
                            </div>
                            
                            <div class="be-mt-100 be-t-break be-c-999 be-fs-80"><?php echo $rootUrl; ?>/<?php echo $this->configCategory->urlPrefix; ?>/{{formData.url}}<?php echo $this->configCategory->urlSuffix; ?></div>
                            <div class="be-mt-100">{{formData.seo_title}}</div>
                            <div class="be-mt-100 be-t-ellipsis-2">{{formData.seo_description}}</div>
                        </div>

                    </div>
                </div>


            </el-form>
        </div>

        <el-drawer
                :visible.sync="drawerSeo"
                title="搜索引擎优化"
                size="40%"
                :wrapper-closable="false"
                :destroy-on-close="true">

            <div class="be-px-150">

                <div class="be-row">
                    <div class="be-col-auto">
                        SEO标题
                        <el-tooltip effect="dark" content="标题是SEO最重要的部分，该标题会显示在搜索引擎的搜索结果中。" placement="top">
                            <i class="el-icon-fa fa-question-circle-o"></i>
                        </el-tooltip>：
                    </div>
                    <div class="be-col">
                        <div class="be-pl-100">
                            <el-switch v-model.number="formData.seo_title_custom" :active-value="1" :inactive-value="0" inactive-text="自动生成" active-text="自定义" size="medium" @change="seoUpdate"></el-switch>
                        </div>
                    </div>
                </div>
                <el-input
                        class="be-mt-50"
                        type="text"
                        placeholder="请输入SEO标题"
                        v-model = "formData.seo_title"
                        size="medium"
                        maxlength="200"
                        show-word-limit
                        :disabled="formData.seo_title_custom === 0">
                </el-input>
                <?php
                $formData['seo_title'] = ($this->category ? $this->category->seo_title : '');
                $formData['seo_title_custom'] = ($this->category ? $this->category->seo_title_custom : 0);
                ?>


                <div class="be-row be-mt-150">
                    <div class="be-col-auto">
                        SEO描述
                        <el-tooltip effect="dark" content="这是该分类的整体SEO描述，使分类在搜索引擎中获得更高的排名。" placement="top">
                            <i class="el-icon-fa fa-question-circle-o"></i>
                        </el-tooltip>：
                    </div>
                    <div class="be-col">
                        <div class="be-pl-100">
                            <el-switch v-model.number="formData.seo_description_custom" :active-value="1" :inactive-value="0" inactive-text="自动生成" active-text="自定义" size="medium" @change="seoUpdate"></el-switch>
                        </div>
                    </div>
                </div>
                <el-input
                        class="be-mt-50"
                        type="textarea"
                        :rows="6"
                        placeholder="请输入SEO描述"
                        v-model = "formData.seo_description"
                        size="medium"
                        maxlength="500"
                        show-word-limit
                        :disabled="formData.seo_description_custom === 0">
                </el-input>
                <?php
                $formData['seo_description'] = ($this->category ? $this->category->seo_description : '');
                $formData['seo_description_custom'] = ($this->category ? $this->category->seo_description_custom : 0);
                ?>

                <div class="be-row be-mt-150">
                    <div class="be-col-auto">
                        SEO友好链接：
                    </div>
                    <div class="be-col">
                        <div class="be-pl-100">
                            <el-switch v-model.number="formData.url_custom" :active-value="1" :inactive-value="0" inactive-text="自动生成" active-text="自定义" size="medium" @change="seoUpdate"></el-switch>
                        </div>
                    </div>
                </div>
                <el-input
                        class="be-mt-50"
                        type="text"
                        placeholder="请输入SEO友好链接"
                        v-model = "formData.url"
                        size="medium"
                        maxlength="200"
                        show-word-limit
                        :disabled="formData.url_custom === 0">
                    <template slot="prepend"><?php echo $rootUrl; ?>/<?php echo $this->configCategory->urlPrefix; ?>/</template>
                    <?php if ($this->configCategory->urlSuffix) { ?>
                        <template slot="append"><?php echo $this->configCategory->urlSuffix; ?>/</template>
                    <?php } ?>
                </el-input>
                <?php
                $formData['url'] = ($this->category ? $this->category->url : '');
                $formData['url_custom'] = ($this->category ? $this->category->url_custom : 0);
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
                $formData['seo_keywords'] = ($this->category ? $this->category->seo_keywords : '');
                ?>

                <div class="be-mt-150 be-ta-right">
                    <el-button size="medium" type="primary" @click="drawerSeo=false">确定</el-button>
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

                drawerSeo: false,

                imageFiles: [],
                imagePreviewVisible: false,
                imageAltVisible: false,

                imageSelectorVisible: false,
                imageSelectorUrl: "about:blank",

                t: false
                <?php
                echo $uiItems->getVueData();
                ?>
            },
            methods: {
                save: function () {
                    let _this = this;
                    this.$refs["formRef"].validate(function (valid) {
                        if (valid) {
                            _this.loading = true;
                            vueNorth.loading = true;
                            _this.$http.post("<?php echo beAdminUrl('Shop.Category.' . ($this->category ? 'edit' :'create')); ?>", {
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
                                            window.location.href = "<?php echo beAdminUrl('Shop.Category.categories'); ?>";
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
                    window.location.href = "<?php echo beAdminUrl('Shop.Category.categories'); ?>";
                },

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
                seoUpdate: function () {
                    if (this.formData.seo_title_custom === 0) {
                        this.formData.seo_title = this.formData.name;
                    }

                    if (this.formData.seo_description_custom === 0) {
                        let seoDescription = this.formData.description;
                        seoDescription = seoDescription.replace(/<[^>]*>/g,"");
                        seoDescription = seoDescription.replace("\r", " ");
                        seoDescription = seoDescription.replace("\n", " ");
                        this.formData.seo_description = seoDescription;

                        if (seoDescription.length > 500) {
                            seoDescription = seoDescription.substr(0, 500);
                        }
                        this.formData.seo_description = seoDescription;
                    }

                    if (this.formData.url_custom === 0) {
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

                imageSelect: function () {
                    this.imageSelectorVisible = true;
                    <?php
                    $imageCallback = base64_encode('parent.imageSelected(files);');
                    $imageSelectorUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]);
                    ?>
                    let imageSelectorUrl = "<?php echo $imageSelectorUrl; ?>";
                    imageSelectorUrl += imageSelectorUrl.indexOf("?") === -1 ? "?" : "&"
                    imageSelectorUrl += "_=" + Math.random();
                    this.imageSelectorUrl = imageSelectorUrl;
                },
                imageSelected: function (files) {
                    if (files.length > 0) {
                        let file = files[0];
                        this.formData.image = file.url;

                        this.imageSelectorVisible = false;
                        this.imageSelectorUrl = "about:blank";
                    }
                },
                imagePreview: function () {
                    this.imagePreviewVisible = true;
                },
                imageRemove: function () {
                    this.imageFiles = [];
                    this.formData.image = "";
                }

                <?php
                echo $uiItems->getVueMethods();
                ?>
            }

            <?php
            $uiItems->setVueHook('mounted', 'window.onbeforeunload = function(e) {e = e || window.event; if (e) { e.returnValue = ""; } return ""; };');
            echo $uiItems->getVueHooks();
            ?>
        });

        function imageSelected(files) {
            vueCenter.imageSelected(files);
        }

    </script>

</be-page-content>