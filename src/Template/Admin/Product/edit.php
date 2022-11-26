<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <script src="<?php echo $wwwUrl; ?>/lib/sortable/sortable.min.js"></script>
    <script src="<?php echo $wwwUrl; ?>/lib/vuedraggable/vuedraggable.umd.min.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo $wwwUrl; ?>/admin/product/css/edit.css" />
</be-head>


<be-north>
    <div class="be-north" id="be-north">
        <div class="be-row">
            <div class="be-col">
                <div style="padding: 1.25rem 0 0 2rem;">
                    <el-link icon="el-icon-back" href="<?php echo $this->backUrl; ?>">返回商品列表</el-link>
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
        <el-form ref="formRef" :model="formData" class="be-mb-400">
            <?php
            $formData['id'] = ($this->product ? $this->product->id : '');
            ?>

            <div class="be-row">
                <div class="be-col-24 be-md-col-18">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">基本信息</div>

                        <div class="be-mt-200"><span class="be-c-red">*</span> 商品名称：</div>
                        <el-form-item class="be-mt-50" prop="name" :rules="[{required: true, message: '请输入商品名称', trigger: 'change' }]">
                            <el-input
                                    type="text"
                                    placeholder="请输入商品名称"
                                    v-model = "formData.name"
                                    size="medium"
                                    maxlength="200"
                                    show-word-limit
                                    @change="nameChange">
                            </el-input>
                        </el-form-item>
                        <?php $formData['name'] = ($this->product ? $this->product->name : ''); ?>

                        <div class="be-mt-100">商品摘要：</div>
                        <el-form-item class="be-mt-50" prop="summary">
                            <el-input
                                    type="textarea"
                                    :autosize="{minRows:2,maxRows:6}"
                                    placeholder="请输入商品摘要"
                                    v-model="formData.summary"
                                    size="medium"
                                    maxlength="600"
                                    show-word-limit
                                    @change="seoUpdate">
                            </el-input>
                        </el-form-item>
                        <?php $formData['summary'] = ($this->product ? $this->product->summary : ''); ?>

                        <div class="be-mt-100">商品描述：</div>
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

                        $formData['description'] = ($this->product ? $this->product->description : '');

                        $uiItems->add($driver);
                        ?>

                         <div class="be-row be-mt-100">
                             <div class="be-col-auto">自动下载远程图片：</div>
                             <div class="be-col-auto">
                                 <el-form-item prop="download_remote_image">
                                     <el-switch v-model.number="formData.download_remote_image" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                 </el-form-item>
                             </div>
                         </div>
                         <?php $formData['download_remote_image'] = ($this->product && $this->product->download_remote_image !== 0 ? 1 : 0); ?>

                     </div>

                </div>


                <div class="be-col-24 be-md-col-6 be-pl-150">
                     <div class="be-p-150 be-bc-fff">
                        <div class="be-fs-110">基本属性</div>
                        <?php
                        if ($this->product && $this->product->is_enable === -1) {
                            $formData['is_enable'] = -1;
                        } else {
                            ?>
                            <div class="be-row be-mt-200">
                                <div class="be-col">上架商品：</div>
                                <div class="be-col-auto">
                                    <el-form-item prop="is_enable">
                                        <el-switch v-model.number="formData.is_enable" :active-value="1" :inactive-value="0" size="medium"></el-switch>
                                    </el-form-item>
                                </div>
                            </div>
                            <?php
                            $formData['is_enable'] = ($this->product ? $this->product->is_enable : 0);
                        }
                        ?>

                        <div class="be-mt-150">
                            SPU：
                            <el-tooltip effect="dark" content="标准化产品单元，如：属性值、特性相同的商品可以称为一个 SPU" placement="top">
                                <i class="el-icon-fa fa-question-circle-o"></i>
                            </el-tooltip>
                        </div>
                        <el-form-item class="be-mt-50" prop="spu">
                            <el-input
                                    type="text"
                                    placeholder="请输入SPU"
                                    v-model="formData.spu"
                                    maxlength="60"
                                    size="medium"
                                    show-word-limit>
                            </el-input>
                        </el-form-item>
                        <?php $formData['spu'] = ($this->product ? $this->product->spu : ''); ?>

                        <div class="be-mt-150">
                            站外销量：
                            <el-tooltip effect="dark" content="商品销量 = 本店铺实际销量 + 站外销量" placement="top">
                                <i class="el-icon-fa fa-question-circle-o"></i>
                            </el-tooltip>
                        </div>
                        <el-form-item class="be-mt-50" prop="sales_volume_base">
                            <el-input-number
                                    :precision="0"
                                    :step="1"
                                    :max="999999"
                                    maxlength="6"
                                    placeholder="请输入店铺外销量"
                                    v-model.number="formData.sales_volume_base"
                                    size="medium">
                            </el-input-number>
                        </el-form-item>
                        <?php $formData['sales_volume_base'] = ($this->product ? $this->product->sales_volume_base : 0); ?>


                        <div class="be-mt-150">
                            分类：
                        </div>
                        <el-form-item class="be-mt-50" prop="category_ids">
                            <el-select
                                    v-model="formData.category_ids"
                                    multiple
                                    placeholder="请选择分类"
                                    size="medium">
                                <?php
                                foreach ($this->categoryKeyValues as $key => $val) {
                                    echo '<el-option value="'. $key .'" key="'. $key .'" label="' .$val . '"></el-option>';
                                }
                                ?>
                            </el-select>
                        </el-form-item>
                        <?php
                        $formData['category_ids'] = ($this->product ? $this->product->categoryIds : []);
                        ?>

                        <div class="be-mt-150">
                            标签：
                        </div>
                        <div v-if="formData.tags">
                              <el-tag
                                    v-for="tag in formData.tags"
                                    :key="tag"
                                    closable
                                    @close="removeTag(tag)"
                                    class="be-mr-50 be-mt-50"
                                    size="medium">
                                {{tag}}
                            </el-tag>
                        </div>
                        <el-form-item class="be-mt-50" v-if="formData.tags.length <= 60">
                            <el-input
                                    type="text"
                                    placeholder="添加标签（回车确认输入）"
                                    v-model="formItems.tags.currentTag"
                                    maxlength="60"
                                    size="medium"
                                    show-word-limit
                                    @change="addTag">
                            </el-input>
                        </el-form-item>
                        <?php
                        $formData['tags'] = ($this->product ? $this->product->tags : []);
                        $uiItems->setVueData('formItems', [
                            'tags' => ['currentTag' => '']
                        ]);
                        ?>

                        <div class="be-mt-150">
                            品牌：
                        </div>
                        <el-form-item class="be-mt-50" prop="brand">
                            <el-input
                                    type="text"
                                    placeholder="请输入品牌"
                                    v-model="formData.brand"
                                    maxlength="60"
                                    size="medium"
                                    show-word-limit>
                            </el-input>
                        </el-form-item>
                        <?php
                        $formData['brand'] = ($this->product ? $this->product->brand : '');
                        ?>

                    </div>

                    <div class="be-p-150 be-bc-fff be-mt-150">

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

                        <div class="be-mt-100 be-t-break be-c-999 be-fs-80"><?php echo $rootUrl; ?>/<?php echo $this->configProduct->urlPrefix; ?>/{{formData.url}}<?php echo $this->configProduct->urlSuffix; ?></div>
                        <div class="be-mt-100">{{formData.seo_title}}</div>
                        <div class="be-mt-100 be-t-ellipsis-2">{{formData.seo_description}}</div>

                    </div>
                </div>
            </div>

            <div class="be-p-150 be-bc-fff be-mt-150">
                <div class="be-fs-110">
                    主图 <span class="be-c-999 be-fs-90">请使用尺寸一样的商品图</span>
                </div>

                <div class="be-mt-150">
                    <draggable v-model="formData.images" force-fallback="true" animation="100" filter=".image-uploader" handle=".image-move">
                        <transition-group>
                            <div v-for="image in formData.images" :key="image.ordering" class="image">
                                <img :src="image.url" :alt="image.alt">
                                <div class="image-move"></div>
                                <div class="image-actions">
                                    <span class="image-action" @click="imagePreview(image)"><i class="el-icon-zoom-in"></i></span>
                                    <span class="image-action" @click="imageRemove(image)"><i class="el-icon-delete"></i></span>
                                </div>
                            </div>

                            <div class="image-selector" @click="imageSelect" key="99999">
                                <i class="el-icon-plus"></i>
                            </div>
                        </transition-group>
                    </draggable>
                </div>

                <el-dialog :visible.sync="imageSelectorVisible" class="dialog-image-selector" title="选择主图" :width="600" :close-on-click-modal="false">
                    <iframe :src="imageSelectorUrl" style="width:100%;height:400px;border:0;}"></iframe>
                    <div slot="footer" class="dialog-footer">
                        <el-button @click="imageSelectedCancel">取 消</el-button>
                        <el-button type="primary" @click="imageSelectedConfirm">确 定</el-button>
                    </div>
                </el-dialog>


                <el-dialog :visible.sync="imagePreviewVisible" center="true">
                    <div class="be-ta-center">
                        <img style="max-width: 100%;max-height: 400px;" :src="imagePreviewUrl" alt="">
                    </div>
                </el-dialog>

                <el-dialog :visible.sync="imageAltVisible" center="true">
                </el-dialog>

            </div>
            <?php
            $formData['images'] = ($this->product ? $this->product->images : []);
            ?>


            <div class="be-p-150 be-bc-fff be-mt-150">
                <div class="be-fs-110">
                    关联其它商品 <span class="be-c-999 be-fs-90">您可以将多个类似的商品关联起来</span>
                </div>

                <div class="be-mt-150">
                    <el-checkbox v-model.number="formData.related" :true-label="1" :false-label="0">开启商品关联</el-checkbox>
                </div>
                <?php
                $formData['related'] = $this->product && $this->product->relate_id !== '' ? 1 : 0;

                $relate = null;
                if ($this->product && $this->product->relate_id !== '' && $this->product->relate) {
                    $relate = $this->product->relate;
                } else {
                    $relate = new \stdClass();
                    $relate->id = '';
                }

                if (!isset($relate->name)) {
                    $relate->name = '';
                }

                if (!isset($relate->icon_type)) {
                    $relate->icon_type = 'text';
                }

                if (!isset($relate->items) || !is_array($relate->items) || count($relate->items) === 0) {
                    $relate->items = [[
                        'id' => '',
                        'product_id' => $this->product ? $this->product->id : '',
                        'product_name' => $this->product ? $this->product->name : '当前商品',
                        'value' => '',
                        'icon_image' => '',
                        'icon_color' => '',
                        'self' => 1,
                    ]];
                } else {
                    if ($this->product) {
                        foreach ($relate->items as &$relateItem) {
                            $relateItem->self = $relateItem->product_id === $this->product->id ? 1 : 0;
                        }
                        unset($relateItem);
                    }
                }

                $formData['relate'] = $relate;
                ?>

                <template v-if="formData.related === 1">

                    <div class="be-mt-100 be-row">
                        <div class="be-col-auto be-lh-250">关联属性的名称：<span class="be-c-red">*</span></div>
                        <div class="be-col be-pl-100">
                            <el-form-item prop="relate.name" :rules="[{required: true, message: '请输入商品关联属性的名称', trigger: 'change' }]">
                                <el-input
                                        type="text"
                                        placeholder="请输入关联属性的名称（如：Color）"
                                        v-model = "formData.relate.name"
                                        size="medium"
                                        maxlength="200" style="min-width: 300px;">
                                </el-input>
                            </el-form-item>
                        </div>
                        <div class="be-col-auto be-pl-200 be-lh-250">
                            图标类型：
                        </div>
                        <div class="be-col be-pl-100 be-lh-250">
                            <el-radio v-model="formData.relate.icon_type" label="text">文本</el-radio>
                            <el-radio v-model="formData.relate.icon_type" label="image">图片</el-radio>
                            <el-radio v-model="formData.relate.icon_type" label="color">色块</el-radio>
                        </div>
                    </div>

                    <div class="be-mt-100">

                        <div class="be-row relate-items-header">
                            <div class="be-col-auto">
                                <div class="relate-item-col-drag-icon"></div>
                            </div>
                            <div class="be-col-auto">
                                <div class="relate-item-col-op be-fw-bold">
                                    操作
                                </div>
                            </div>
                            <div class="be-col">
                                <div class="relate-item-col-product be-fw-bold">
                                    商品
                                </div>
                            </div>
                            <div class="be-col">
                                <div class="relate-item-col-value be-fw-bold">
                                    关联属性的值
                                </div>
                            </div>
                            <div class="be-col-auto" v-if="formData.relate.icon_type === 'image'">
                                <div class="relate-item-col-icon-image be-fw-bold">
                                    图标：图片
                                </div>
                            </div>
                            <div class="be-col-auto" v-if="formData.relate.icon_type === 'color'">
                                <div class="relate-item-col-icon-color be-fw-bold">
                                    图标：颜色
                                </div>
                            </div>
                        </div>

                        <div class="relate-items">
                            <draggable
                                    v-model="formData.relate.items"
                                    ghost-class="relate-item-ghost"
                                    chosen-class="relate-item-chosen"
                                    drag-class="relate-item-drag"
                                    handle=".relate-item-drag-icon"
                                    force-fallback="true"
                                    animation="100">
                                <transition-group>
                                    <div class="be-row relate-item" v-for="relateItem, relateItemIndex in formData.relate.items" :key="relateItem.id">
                                        <div class="be-col-auto">
                                            <div class="relate-item-col-drag-icon">
                                                <div class="relate-item-drag-icon">
                                                    <i class="el-icon-rank"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="be-col-auto">
                                            <div class="relate-item-col-op">
                                                <el-button type="text" icon="el-icon-delete" @click="relateDelete(relateItem)" :disabled="relateItem.self===1"></el-button>
                                            </div>
                                        </div>

                                        <div class="be-col">
                                            <div class="relate-item-col-product be-lh-250">
                                                {{relateItem.product_name}}<el-tag v-if="relateItem.self === 1" size="mini" class="be-ml-100">当前商品</el-tag>
                                            </div>
                                        </div>
                                        <div class="be-col">
                                            <div class="relate-item-col-value">
                                                <el-form-item :prop="'relate.items['+relateItemIndex+'].value'" :rules="[{required: true, message: '请输入关联属性的值', trigger: 'change' }]">
                                                    <el-input
                                                            type="text"
                                                            placeholder="请输入关联属性的值"
                                                            v-model = "relateItem.value"
                                                            size="medium"
                                                            maxlength="200" style="min-width: 300px;">
                                                    </el-input>
                                                </el-form-item>
                                            </div>
                                        </div>
                                        <div class="be-col-auto" v-if="formData.relate.icon_type === 'image'">
                                            <div class="relate-item-col-icon-image">

                                                <div class="relate-icon-image">
                                                    <div v-if="relateItem.icon_image" class="relate-icon-image-img">
                                                        <el-image :src="relateItem.icon_image" fit="contain" @click="relateIconImageSelect(relateItem)"></el-image>
                                                        <div class="relate-icon-image-img-action">
                                                            <i class="el-icon-delete" @click="relateIconImageDelete(relateItem)"></i>
                                                        </div>
                                                    </div>
                                                    <i v-else class="el-icon-plus relate-icon-image-icon" @click="relateIconImageSelect(relateItem)"></i>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="be-col-auto" v-if="formData.relate.icon_type === 'color'">
                                            <div class="relate-item-col-icon-color">
                                                <el-color-picker v-model="relateItem.icon_color"></el-color-picker>
                                            </div>
                                        </div>
                                    </div>
                                </transition-group>
                            </draggable>
                        </div>

                        <div class="be-mt-50">
                            <el-button size="medium" icon="el-icon-plus" @click="relateAdd()">添加商品</el-button>
                        </div>
                    </div>

                </template>

            </div>



            <div class="be-p-150 be-bc-fff be-mt-150">
                <div class="be-fs-110">
                    款式设置 <span class="be-c-999 be-fs-90">您可以设置该商品多种款式的细节</span>
                </div>

                <div class="be-mt-150">
                    <el-radio v-model.number="formData.style" :label="1" @change="styleToggle(1)">单一款式</el-radio>
                    <el-radio v-model.number="formData.style" :label="2" @change="styleToggle(2)">多款式</el-radio>
                </div>
                <?php
                $formData['style'] = ($this->product ? $this->product->style : 1);
                ?>

                <div class="be-mt-150">
                    <el-checkbox v-model.number="formData.stock_tracking" :true-label="1" :false-label="0">开启库存追踪</el-checkbox>
                    <?php
                    $formData['stock_tracking'] = ($this->product ? $this->product->stock_tracking : 0);
                    ?>

                    <el-select v-model.number="formData.stock_out_action" v-if="formData.stock_tracking === 1" class="be-ml-200" size="medium">
                        <el-option label="库存为0时不允许购买" :value="0"></el-option>
                        <el-option label="库存为0时允许购买" :value="1"></el-option>
                        <el-option label="库存为0时自动下架" :value="-1"></el-option>
                    </el-select>
                    <?php
                    $formData['stock_out_action'] = ($this->product ? $this->product->stock_out_action : 1);
                    ?>
                </div>
            </div>

            <div class="be-p-150 be-bc-fff be-mt-150">
                <div class="be-fs-110">
                    {{formData.style === 1 ? "单一款式" : "多款式"}}
                </div>

                <div v-if="formData.style === 2">


                    <div class="be-b-ddd be-mt-50 be-mb-200" v-for="style, styleIndex in formData.styles">
                        <div class="be-row">
                            <div class="be-col-auto" style="background-color: var(--major-color-9);">
                                <div class="be-px-100 be-py-150">
                                    <div class="be-ta-center be-fs-110">款式组 {{styleIndex + 1}}</div>
                                    <div class="be-mt-200">
                                        <el-button class="be-ml-100" size="mini" icon="el-icon-delete" type="danger" @click="styleDelete(style)" :disabled="formData.styles.length < 2">删除</el-button>
                                    </div>
                                </div>
                            </div>
                            <div class="be-col">
                                <div class="be-p-100">
                                    <div class="be-row">
                                        <div class="be-col-auto be-lh-250">款式名称：<span class="be-c-red">*</span></div>
                                        <div class="be-col be-pl-100">
                                            <el-form-item :prop="'styles['+styleIndex+'].name'" :rules="[{required: true, message: '请输入款式名称', trigger: 'change' }]">
                                                <el-input
                                                        type="text"
                                                        placeholder="请输入款式名称（如：Color）"
                                                        v-model = "style.name"
                                                        size="medium"
                                                        maxlength="60"
                                                        @change="styleNameChange">
                                                </el-input>
                                            </el-form-item>
                                        </div>
                                        <div class="be-col-auto be-pl-200 be-lh-250">
                                            款式图标类型：
                                        </div>
                                        <div class="be-col be-pl-100 be-lh-250">
                                            <el-radio v-model="style.icon_type" label="text">文本</el-radio>
                                            <el-radio v-model="style.icon_type" label="image">图片</el-radio>
                                            <el-radio v-model="style.icon_type" label="color">色块</el-radio>
                                        </div>
                                    </div>

                                    <div class="be-mt-100">

                                        <div class="be-row style-items-header">
                                            <div class="be-col-auto">
                                                <div class="style-item-col-drag-icon"></div>
                                            </div>
                                            <div class="be-col-auto">
                                                <div class="style-item-col-op be-fw-bold">
                                                    操作
                                                </div>
                                            </div>
                                            <div class="be-col">
                                                <div class="style-item-col-value be-fw-bold">
                                                    款式子项的值
                                                </div>
                                            </div>
                                            <div class="be-col-auto" v-if="style.icon_type === 'image'">
                                                <div class="style-item-col-icon-image be-fw-bold">
                                                    图标：图片
                                                </div>
                                            </div>
                                            <div class="be-col-auto" v-if="style.icon_type === 'color'">
                                                <div class="style-item-col-icon-color be-fw-bold">
                                                    图标：颜色
                                                </div>
                                            </div>
                                        </div>

                                        <div class="style-items">
                                            <draggable
                                                    v-model="style.items"
                                                    ghost-class="style-item-ghost"
                                                    chosen-class="style-item-chosen"
                                                    drag-class="style-item-drag"
                                                    handle=".style-item-drag-icon"
                                                    force-fallback="true"
                                                    animation="100">
                                                <transition-group>
                                                    <div class="be-row style-item" v-for="styleItem,styleItemIndex in style.items" :key="styleItem.id">
                                                        <div class="be-col-auto">
                                                            <div class="style-item-col-drag-icon">
                                                                <div class="style-item-drag-icon">
                                                                    <i class="el-icon-rank"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="be-col-auto">
                                                            <div class="style-item-col-op">
                                                                <el-button type="text" icon="el-icon-delete" @click="styleItemDelete(style, styleItem)"></el-button>
                                                            </div>
                                                        </div>

                                                        <div class="be-col">
                                                            <div class="style-item-col-value">
                                                                <el-form-item :prop="'styles['+styleIndex+'].items['+styleItemIndex+'].value'" :rules="[{required: true, message: '请输入款式子项的值', trigger: 'change' }]">
                                                                    <el-input
                                                                            type="text"
                                                                            placeholder="请输入款式子项的值"
                                                                            v-model = "styleItem.value"
                                                                            size="medium"
                                                                            maxlength="60"
                                                                            @change="((val)=>{styleItemChange(val, styleIndex)})">
                                                                    </el-input>
                                                                </el-form-item>
                                                            </div>
                                                        </div>
                                                        <div class="be-col-auto" v-if="style.icon_type === 'image'">
                                                            <div class="style-item-col-icon-image">

                                                                <div class="style-icon-image">
                                                                    <div v-if="styleItem.icon_image" class="style-icon-image-img">
                                                                        <el-image :src="styleItem.icon_image" fit="contain" @click="relateIconImageSelect(styleItem)"></el-image>
                                                                        <div class="style-icon-image-img-action">
                                                                            <i class="el-icon-delete" @click="relateIconImageDelete(styleItem)"></i>
                                                                        </div>
                                                                    </div>
                                                                    <i v-else class="el-icon-plus style-icon-image-icon" @click="relateIconImageSelect(styleItem)"></i>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="be-col-auto" v-if="style.icon_type === 'color'">
                                                            <div class="style-item-col-icon-color">
                                                                <el-color-picker v-model="styleItem.icon_color"></el-color-picker>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </transition-group>
                                            </draggable>
                                        </div>

                                        <div class="be-mt-50">
                                            <el-button size="mini" icon="el-icon-plus" @click="styleAddItem(style)">添加款式子项</el-button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="be-mt-100">
                        <el-button size="medium" icon="el-icon-plus" @click="styleAdd()">添加商品款式</el-button>
                    </div>
                </div>
                <?php
                if ($this->product && $this->product->style === 2) {
                    $formData['styles'] = $this->product->styles;
                } else {
                    $formData['styles'] = [
                        (object)[
                            'id' => '',
                            'name' => '',
                            'icon_type' => 'text',
                            'items' => [
                                (object) [
                                    'id' => '',
                                    'value' => '',
                                    'icon_image' => '',
                                    'icon_color' => '',
                                ],
                            ],
                        ],
                    ];
                }
                ?>

                <el-table
                        class="be-mt-150"
                        ref = "itemTableRef"
                        :data="formData.items">

                    <template slot="empty">
                        <el-empty description="暂无数据"></el-empty>
                    </template>

                    <el-table-column
                            type="selection"
                            align="center"
                            fixed
                            width="60">
                    </el-table-column>

                    <el-table-column
                            label="操作"
                            align="center"
                            fixed
                            width="80">
                        <template slot-scope="scope">
                            <el-button
                                    type="text"
                                    icon="el-icon-delete"
                                    @click="itemDelete(scope.row)"></el-button>
                        </template>
                    </el-table-column>

                    <el-table-column
                            v-if="formData.style === 2"
                            label="图片"
                            align="center">
                        <template slot-scope="scope">
                            <div class="item-images-container" @click="itemImagesManage(scope.row)">
                                <div v-if="scope.row.images.length > 0" class="item-images" :style="'width:' + (scope.row.images.length > 1 ? (scope.row.images.length * 10 + 50) : 60) + 'px'">
                                    <el-image v-for="(itemImage, itemIndex) in scope.row.images" :key="itemImage.ordering" :src="itemImage.url" fit="contain" :style="'left:' + itemIndex * 10 + 'px;z-index:' + (100 - itemIndex)"></el-image>
                                </div>
                                <i v-else class="el-icon-setting item-images-icon"></i>
                            </div>
                        </template>
                    </el-table-column>


                    <el-table-column
                            v-if="formData.style === 2"
                            v-for="(item, index) in itemTableColumnStyles"
                            :label="item.label"
                            :prop="item.name"
                            :key="index"
                            :filters="item.filters"
                            :filter-method="itemFilter"
                            align="center"
                            min-width="90">
                    </el-table-column>


                    <el-table-column
                            prop="price"
                            align="center"
                            width="150">
                        <template slot="header" slot-scope="scope">
                            售价（<?php echo $this->configStore->currencySymbol; ?>）
                        </template>
                        <template slot-scope="scope">
                            <el-input-number
                                    style="max-width:100px;"
                                    :precision="2"
                                    :step="0.01"
                                    :max="999999999"
                                    :controls="false"
                                    v-model="formData.items[scope.$index].price"
                                    size="medium">
                            </el-input-number>
                        </template>
                    </el-table-column>

                    <el-table-column
                            prop="original_price"
                            align="center"
                            width="150">
                        <template slot="header" slot-scope="scope">
                            原价（<?php echo $this->configStore->currencySymbol; ?>）
                            <el-tooltip effect="dark" content="原价为0或小于等于售价时，网店将隐藏原价展示" placement="top">
                                <i class="el-icon-fa fa-question-circle-o"></i>
                            </el-tooltip>
                        </template>
                        <template slot-scope="scope">
                            <el-input-number
                                    style="max-width:100px;"
                                    :precision="2"
                                    :step="0.01"
                                    :max="999999999"
                                    :controls="false"
                                    v-model="formData.items[scope.$index].original_price"
                                    size="medium">
                            </el-input-number>
                        </template>
                    </el-table-column>


                    <el-table-column
                            prop="original_price"
                            label="重量"
                            align="center"
                            width="180">
                        <template slot-scope="scope">
                            <div class="be-row">
                                <div class="be-col">
                                    <el-input-number
                                            style="max-width:100px;"
                                            :precision="2"
                                            :step="0.01"
                                            :max="999999999"
                                            :controls="false"
                                            v-model="formData.items[scope.$index].weight"
                                            size="medium">
                                    </el-input-number>
                                </div>
                                <div class="be-col-auto">
                                    <el-select v-model="formData.items[scope.$index].weight_unit" size="medium" style="width:60px;">
                                        <el-option label="kg" value="kg"></el-option>
                                        <el-option label="g" value="g"></el-option>
                                        <el-option label="lb" value="lb"></el-option>
                                        <el-option label="oz" value="oz"></el-option>
                                    </el-select>
                                </div>
                            </div>
                        </template>
                    </el-table-column>


                    <el-table-column
                            v-if="formData.stock_tracking === 1"
                            prop="stock"
                            label="库存"
                            align="center"
                            width="120">
                        <template slot-scope="scope">
                            <el-input-number
                                    style="width:100px;"
                                    :precision="0"
                                    :step="1"
                                    :max="999999999"
                                    :controls="false"
                                    v-model="formData.items[scope.$index].stock"
                                    size="medium">
                            </el-input-number>
                        </template>
                    </el-table-column>


                    <el-table-column
                            prop="sku"
                            label="SKU"
                            align="center"
                            width="240">
                        <template slot-scope="scope">
                            <el-input
                                    type="text"
                                    v-model="formData.items[scope.$index].sku"
                                    size="medium"
                                    maxlength="60">
                            </el-input>
                        </template>
                    </el-table-column>

                    <el-table-column
                            prop="barcode"
                            label="条形码"
                            align="center"
                            width="240">
                        <template slot-scope="scope">
                            <el-input
                                    type="text"
                                    v-model="formData.items[scope.$index].barcode"
                                    size="medium"
                                    maxlength="60">
                            </el-input>
                        </template>
                    </el-table-column>

                </el-table>

                <?php
                $defaultStyle1Items = [
                    [
                        'id' => '',
                        'sku' => '',
                        'barcode' => '',
                        'style' => '',
                        'style_json' => [],
                        'price' => '0',
                        'original_price' => '0',
                        'weight' => '0',
                        'weight_unit' => 'g',
                        'stock' => 0,
                        'images' => [],
                    ]
                ];

                $style1Items = [];
                $style2Items = [];
                if ($this->product) {
                    if ($this->product->style === 1) {
                        $formData['items'] = $this->product->items;
                        $style1Items = $this->product->items;
                    } elseif ($this->product->style === 2) {
                        $items = $this->product->items;
                        foreach ($items as &$Item) {
                            if (is_array($Item->style_json)) {
                                foreach ($Item->style_json as $style) {
                                    $styleField = 'style_field_' . $style['name'];
                                    $Item->$styleField = $style['value'];
                                }
                            } else {
                                $Item->style_json = [];
                            }
                        }
                        unset($Item);

                        $formData['items'] = $items;
                        $style2Items = $items;

                        $style1Items = $defaultStyle1Items;
                    }
                } else {
                    $formData['items'] = $defaultStyle1Items;
                    $style1Items = $defaultStyle1Items;
                }
                ?>
            </div>

        </el-form>


        <el-dialog :visible.sync="itemImageSelectorVisible" class="dialog-image-selector" title="选择款式图像" :width="600" :close-on-click-modal="false">
            <iframe :src="itemImageSelectorUrl" style="width:100%;height:400px;border:0;}"></iframe>
            <div slot="footer" class="dialog-footer">
                <el-button @click="itemImageSelectedCancel">取 消</el-button>
                <el-button type="primary" @click="itemImageSelectedConfirm">确 定</el-button>
            </div>
        </el-dialog>


        <el-dialog :visible.sync="itemImagePreviewVisible" center="true">
            <div class="be-ta-center">
                <img style="max-width: 100%;max-height: 400px;" :src="itemImagePreviewUrl" alt="">
            </div>
        </el-dialog>

        <el-dialog :visible.sync="itemImageAltVisible" center="true">
        </el-dialog>


        <el-drawer
                :visible.sync="drawerItemImages"
                title="款式图片管理"
                size="80%"
                :wrapper-closable="false"
                :destroy-on-close="true">

            <div class="be-px-150">

                <draggable v-model="currentItemImages" force-fallback="true" animation="100" filter=".image-uploader" handle=".image-move">
                    <transition-group>
                        <div v-for="itemImage in currentItemImages" :key="itemImage.ordering" class="image">
                            <img :src="itemImage.url" :alt="itemImage.alt">
                            <div class="image-move"></div>
                            <div class="image-actions">
                                <span class="image-action" @click="itemImagePreview(itemImage)"><i class="el-icon-zoom-in"></i></span>
                                <span class="image-action" @click="itemImageRemove(itemImage)"><i class="el-icon-delete"></i></span>
                            </div>
                        </div>

                        <div class="image-selector" @click="itemImageSelect" key="99999">
                            <i class="el-icon-plus"></i>
                        </div>
                    </transition-group>
                </draggable>

                <div class="be-mt-150 be-ta-right">
                    <el-button size="medium" type="primary" @click="drawerItemImages=false">确定</el-button>
                </div>
            </div>
        </el-drawer>


        <el-drawer
                :visible.sync="drawerSeo"
                title="搜索引擎优化"
                size="50%"
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
                $formData['seo_title'] = ($this->product ? $this->product->seo_title : '');
                $formData['seo_title_custom'] = ($this->product ? $this->product->seo_title_custom : 0);
                ?>

                <div class="be-row be-mt-150">
                    <div class="be-col-auto">
                        SEO描述
                        <el-tooltip effect="dark" content="这是该商品的整体SEO描述，使商品在搜索引擎中获得更高的排名。" placement="top">
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
                $formData['seo_description'] = ($this->product ? $this->product->seo_description : '');
                $formData['seo_description_custom'] = ($this->product ? $this->product->seo_description_custom : 0);
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
                    <template slot="prepend"><?php echo $rootUrl; ?>/<?php echo $this->configProduct->urlPrefix; ?>/</template>
                    <?php if ($this->configProduct->urlSuffix) { ?>
                        <template slot="append"><?php echo $this->configProduct->urlSuffix; ?></template>
                    <?php } ?>
                </el-input>
                <?php
                $formData['url'] = ($this->product ? $this->product->url : '');
                $formData['url_custom'] = ($this->product ? $this->product->url_custom : 0);
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
                $formData['seo_keywords'] = ($this->product ? $this->product->seo_keywords : '');
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

    $itemTableColumnStyles = [];
    if ($this->product) {
        if ($this->product->style === 2) {
            foreach ($this->product->styles as $style) {
                $filters = [];
                foreach ($style->items as $styleItem) {
                    $filters[] = [
                        'text' => $styleItem->value,
                        'value' => $styleItem->value,
                    ];
                }

                $itemTableColumnStyles[] = [
                    'name' => 'style_field_' . $style->name,
                    'label' => $style->name,
                    'filters' => $filters,
                ];
            }
        }
    }
    ?>

    <script>
        Vue.component('vuedraggable', window.vuedraggable);

        let vueCenter = new Vue({
            el: '#app',
            components: {
                vuedraggable: window.vuedraggable,//当前页面注册组件
            },
            data: {
                formData: <?php echo json_encode($formData); ?>,
                loading: false,

                drawerSeo: false,

                imagePreviewUrl: "",
                imagePreviewVisible: false,
                imageAltVisible: false,
                imageSelectorVisible: false,
                imageSelectorUrl: "about:blank",
                imageSelectedFiles: [],

                currentrelateItem: null,

                drawerItemImages: false,

                currentItem: false,
                currentItemImages: [],

                itemImagePreviewUrl: "",
                itemImagePreviewVisible: false,
                itemImageAltVisible: false,
                itemImageSelectorVisible: false,
                itemImageSelectorUrl: "about:blank",
                itemImageSelectedFiles: [],

                itemTableColumnStyles: <?php echo json_encode($itemTableColumnStyles); ?>,

                style1Items: <?php echo json_encode($style1Items); ?>,
                style2Items: <?php echo json_encode($style2Items); ?>,

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
                            _this.$http.post("<?php echo $this->formActionUrl; ?>", {
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
                                            window.location.href = responseData.redirectUrl;
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
                    window.location.href = "<?php echo $this->backUrl; ?>";
                },
                addTag: function () {
                    if (this.formItems.tags.currentTag) {
                        if (this.formData.tags.indexOf(this.formItems.tags.currentTag) === -1) {
                            this.formData.tags.push(this.formItems.tags.currentTag);
                        }

                        this.formItems.tags.currentTag = "";
                    }
                },
                removeTag: function (tag) {
                    this.formData.tags.splice(this.formData.tags.indexOf(tag), 1);
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

                    for(let item of this.formData.relate.items) {
                        if (item.self === 1) {
                            item.product_name = this.formData.name;
                            break;
                        }
                    }

                    this.seoUpdate();
                },
                seoUpdate: function () {

                    if (this.formData.seo_title_custom === 0) {
                        this.formData.seo_title = this.formData.name;
                    }

                    if (this.formData.seo_description_custom === 0) {
                        let seoDescription;
                        if (this.formData.summary) {
                            seoDescription = this.formData.summary;
                        } else {
                            seoDescription = this.formData.description;
                            seoDescription = seoDescription.replace(/<[^>]*>/g,"");
                            seoDescription = seoDescription.replace("\r", " ");
                            seoDescription = seoDescription.replace("\n", " ");
                        }
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
                    this.imageSelectedFiles = files;
                },
                imageSelectedConfirm: function () {
                    if (this.imageSelectedFiles.length > 0) {
                        for (let file of this.imageSelectedFiles) {
                            let ordering = this.formData.images.length + 1;
                            this.formData.images.push({
                                id : "",
                                url: file.url,
                                name: file.name,
                                alt: file.title,
                                ordering: ordering
                            });
                        }
                    }
                    this.imageSelectorVisible = false;
                    this.imageSelectedFiles = [];
                    this.imageSelectorUrl = "about:blank";
                },
                imageSelectedCancel: function () {
                    this.imageSelectorVisible = false;
                    this.imageSelectedFiles = [];
                    this.imageSelectorUrl = "about:blank";
                },
                imagePreview: function (image) {
                    this.imagePreviewVisible = true;
                    this.imagePreviewUrl = image.url;
                },
                imageRemove: function (image) {
                    this.formData.images.splice(this.formData.images.indexOf(image), 1);
                },


                relateToggle: function (relate) {
                },
                relateIconImageSelect: function(row) {
                    this.currentrelateItem = row;

                    <?php
                    $imageCallback = base64_encode('parent.relateIconImageSelected(files);');
                    $imageSelectorUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]);
                    ?>
                    be.openDrawer("请选择图片", "<?php echo $imageSelectorUrl; ?>", {width: "80%"});
                },
                relateIconImageSelected: function(files) {
                    let file = files[0];
                    this.currentrelateItem.icon_image = file.url
                    be.closeDrawer();
                },
                relateIconImageDelete: function(row) {
                    row.icon_image = ""
                },
                relateDelete: function(row) {
                    this.formData.relate.items.splice(this.formData.relate.items.indexOf(row), 1);
                },
                relateAdd: function() {
                    let excludeProductIds = [];
                    for(let item of this.formData.relate.items) {
                        excludeProductIds.push(item.product_id)
                    }
                    let url = "<?php echo beAdminUrl('Shop.Product.relate'); ?>";
                    url +=  url.indexOf("?") === -1 ? "?" : "&";
                    url += "exclude_product_ids=" + excludeProductIds.join(",");

                    be.openDrawer("请选择您要关联的商品", url, {width: "80%"});
                },
                relateAdded:function (products) {
                    for(let product of products) {
                        this.formData.relate.items.push({
                            id: "",
                            product_id: product.product_id,
                            product_name: product.product_name,
                            value: product.relate_value,
                            icon_image: product.relate_icon_image,
                            icon_color: product.relate_icon_color,
                            self: 0
                        });
                    }

                    be.closeDrawer();
                },

                styleToggle: function (style) {
                    if (style === 1) {
                        this.style2Items = this.formData.items;
                        this.formData.items = this.style1Items;
                    } else if (style === 2) {
                        this.style1Items = this.formData.items;
                        this.formData.items = this.style2Items;
                    }
                },
                styleAdd: function () {
                    this.formData.styles.push({
                        id : "",
                        name: "",
                        icon_type: "text",
                        items: [
                            {
                                id: "",
                                value: "",
                                icon_image: "",
                                icon_color: ""
                            }
                        ]
                    });
                },
                styleAddItem: function (style) {
                    style.items.push({
                        id: "",
                        value: "",
                        icon_image: "",
                        icon_color: ""
                    });
                },
                styleItemDelete(style, styleItem) {
                    style.items.splice(style.items.indexOf(styleItem), 1);
                    this.updateItems();
                },
                styleDelete(style) {
                    this.formData.styles.splice(this.formData.styles.indexOf(style), 1);
                    this.updateItems();
                },
                styleNameChange: function(value) {
                    if (value) {
                        this.updateItems();
                    }
                },
                styleItemChange: function(value, styleIndex) {
                    if (value) {
                        let styleLen = this.formData.styles.length;
                        let style, styleItemLen;
                        let n = 1;

                        for (let i=0; i<styleLen; i++) {
                            style = this.formData.styles[i];
                            if (!style.name) continue;

                            styleItemLen = style.items.length;
                            if (!styleItemLen) continue;

                            n = n * styleItemLen;
                        }

                        if (n > 300) {
                            this.$message.warning("抱歉，所有属性/款式项最大数量为300个！");
                            this.formData.styles[styleIndex].items.pop();
                            return;
                        }

                        this.updateItems();
                    }
                },


                updateItems: function () {
                    if (this.formData.style === 1) {
                        return;
                    }

                    let styleLen = this.formData.styles.length;
                    let style, styleItem, item;
                    let itemLen, styleItemLen;
                    let i,j,k;

                    let items = [];
                    let newItems;

                    let filters;

                    let itemTableColumnStyles = [];

                    for (i=0; i<styleLen; i++) {
                        style = this.formData.styles[i];
                        if (!style.name) continue;

                        styleItemLen = style.items.length;
                        if (!styleItemLen) continue;

                        filters = [];
                        for(let x in style.items) {
                            filters.push({
                                text: style.items[x].value,
                                value: style.items[x].value
                            });
                        }

                        itemTableColumnStyles.push({
                            name: "style_field_" +style.name,
                            label: style.name,
                            filters: filters
                        });
                    }

                    if (JSON.stringify(itemTableColumnStyles) === JSON.stringify(this.itemTableColumnStyles)) {
                        return;
                    }

                    this.itemTableColumnStyles = itemTableColumnStyles;

                    for (i=0; i<styleLen; i++) {
                        style = this.formData.styles[i];
                        if (!style.name) continue;

                        styleItemLen = style.items.length;
                        if (!styleItemLen) continue;

                        itemLen = items.length;
                        if (!itemLen) {
                            for (k=0; k<styleItemLen; k++) {
                                styleItem = style.items[k];
                                items.push([{
                                    name: style.name,
                                    value: styleItem.value
                                }]);
                            }
                        } else {
                            newItems = [];
                            for (j=0; j<itemLen; j++) {
                                item = items[j];
                                for (k=0; k<styleItemLen; k++) {
                                    styleItem = style.items[k];
                                    let tmpItem = clone(item);
                                    tmpItem.push({
                                        name: style.name,
                                        value: styleItem.value
                                    });
                                    newItems.push(tmpItem);
                                }
                            }
                            items = newItems;
                        }
                    }

                    //console.log("items", items);

                    let formDataItems = [];
                    let formDataItemLen;
                    let formDataItem;
                    let formDataItemFound;

                    let formDataLen, len;
                    let formDataItemItem, subItem;

                    let formDataItemKey, formDataItemKeys, itemKey, itemKeys;
                    let itemStyle, itemStyles;

                    let ii;

                    itemLen = items.length;
                    for (i=0; i<itemLen; i++) {
                        itemKeys = [];
                        itemStyles = [];
                        item = items[i];
                        len = item.length;
                        for (j=0; j<len; j++) {
                            subItem = item[j];
                            itemKeys.push(subItem.name + ':' + subItem.value);
                            itemStyles.push(subItem.value);
                        }
                        itemKey = itemKeys.sort().join(";");
                        itemStyle = itemStyles.join(" ");

                        formDataItemFound = false;
                        formDataItemLen = this.formData.items.length;
                        for (k=0; k<formDataItemLen; k++) {
                            formDataItemKeys = [];
                            formDataItem = this.formData.items[k];
                            formDataLen = formDataItem.style_json.length;
                            for (ii=0; ii<formDataLen; ii++) {
                                formDataItemItem = formDataItem.style_json[ii];
                                formDataItemKeys.push(formDataItemItem.name + ':' + formDataItemItem.value);
                            }
                            formDataItemKey = formDataItemKeys.sort().join(";");

                            if (itemKey === formDataItemKey) {
                                formDataItems.push(formDataItem);
                                formDataItemFound = true;
                                break;
                            }
                        }

                        if (!formDataItemFound) {
                            formDataItem = {
                                id: "",
                                sku: "",
                                barcode: "",
                                style: itemStyle,
                                style_json: item,
                                price: "0",
                                original_price: "0",
                                weight: "0",
                                weight_unit: "g",
                                stock: "0",
                                images: [],
                            };

                            len = item.length;
                            for (j=0; j<len; j++) {
                                subItem = item[j];
                                formDataItem["style_field_" + subItem.name] = subItem.value;
                            }

                            formDataItems.push(formDataItem);
                        }
                    }

                    //console.log("formDataItems", formDataItems);

                    this.formData.items = formDataItems;

                    this.itemTableDoLayout();
                },
                itemFilter: function(value, row, column) {
                    let property = column['property'];
                    return row[property] === value;
                },
                itemDelete: function(row) {
                    if (this.formData.items.length === 1) {
                        this.$message.warning("至少保留一个款式！");
                        return;
                    }
                    this.formData.items.splice(this.formData.items.indexOf(row), 1);
                },

                itemImagesManage: function(itemRow) {
                    this.drawerItemImages = true;
                    this.currentItem = itemRow;
                    this.currentItemImages = itemRow.images;
                },
                itemImageSelect: function () {
                    this.itemImageSelectorVisible = true;
                    <?php
                    $imageCallback = base64_encode('parent.itemImageSelected(files);');
                    $imageSelectorUrl = beAdminUrl('System.Storage.pop', ['filterImage' => 1, 'callback' => $imageCallback]);
                    ?>
                    let imageSelectorUrl = "<?php echo $imageSelectorUrl; ?>";
                    imageSelectorUrl += imageSelectorUrl.indexOf("?") === -1 ? "?" : "&"
                    imageSelectorUrl += "_=" + Math.random();
                    this.itemImageSelectorUrl = imageSelectorUrl;
                },

                itemImageSelected: function (files) {
                    this.itemImageSelectedFiles = files;
                },
                itemImageSelectedConfirm: function () {
                    if (this.itemImageSelectedFiles.length > 0) {
                        for (let file of this.itemImageSelectedFiles) {
                            let ordering = this.currentItem.images.length + 1;
                            this.currentItem.images.push({
                                id : "",
                                url: file.url,
                                name: file.name,
                                alt: file.title,
                                is_main: ordering === 1 ? 1 : 0,
                                ordering: ordering
                            });
                        }

                        console.log(this.currentItem);
                    }
                    this.itemImageSelectorVisible = false;
                    this.itemImageSelectedFiles = [];
                    this.itemImageSelectorUrl = "about:blank";
                },
                itemImageSelectedCancel: function () {
                    this.itemImageSelectorVisible = false;
                    this.itemImageSelectedFiles = [];
                    this.itemImageSelectorUrl = "about:blank";
                },
                itemImagePreview: function (itemImage) {
                    this.itemImagePreviewVisible = true;
                    this.itemImagePreviewUrl = itemImage.url;
                },
                itemImageRemove: function (itemImage) {
                    this.currentItemImages.splice(this.currentItemImages.indexOf(itemImage), 1);
                },

                itemTableDoLayout: function() {
                    let _this = this;
                    this.$nextTick(function () {
                        _this.$refs.itemTableRef.doLayout()
                    })
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

        function itemImageSelected(files) {
            vueCenter.itemImageSelected(files);
        }

        function relateIconImageSelected(files) {
            vueCenter.relateIconImageSelected(files);
        }

        function setRelate(products) {
            vueCenter.relateAdded(products);
        }

        function clone(obj) {
            if (null === obj || "object" != typeof obj) return obj;

            let copy;
            if (obj instanceof Date) {
                copy = new Date();
                copy.setTime(obj.getTime());
                return copy;
            }

            if (obj instanceof Array) {
                copy = [];
                let len = obj.length;
                for (let i = 0; i < len; ++i) {
                    copy[i] = clone(obj[i]);
                }
                return copy;
            }

            if (obj instanceof Object) {
                copy = {};
                for (let attr in obj) {
                    if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
                }
                return copy;
            }

            throw new Error("Unable to clone obj!");
        }
    </script>

</be-page-content>