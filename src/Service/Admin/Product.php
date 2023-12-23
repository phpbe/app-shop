<?php

// fancybetter
$db = \Be\Be::getService('App.Etl.Admin.Ds')->newDb('f3474ba2-97ed-11ee-bb6a-00163e001e50');


// SPU 是否存在
$sql = 'SELECT * FROM shop_product WHERE spu=?';
$exitProduct = $db->getObject($sql, [$input->relate_key]);
if ($exitProduct) {
    //throw new \Be\Task\TaskException('SPU ' . $input->relate_key . ' 重复！');

    $sql = 'DELETE FROM shop_product_style_item WHERE product_style_id IN (SELECT id FROM shop_product_style WHERE product_id = ?)';
    $db->query($sql, [$exitProduct->id]);

    $sql = 'DELETE FROM shop_product_style WHERE product_id = ?';
    $db->query($sql, [$exitProduct->id]);

    $sql = 'DELETE FROM shop_product_image WHERE product_id = ?';
    $db->query($sql, [$exitProduct->id]);

    $sql = 'DELETE FROM shop_product_item WHERE product_id = ?';
    $db->query($sql, [$exitProduct->id]);

    $sql = 'DELETE FROM shop_product WHERE id = ?';
    $db->query($sql, [$exitProduct->id]);
}

$url = strtolower($input->name);
$url = preg_replace('/[^a-z0-9]/', '-', $url);
$url = str_replace(' ', '-', $url);
while (strpos($url, '--') !== false) {
    $url = str_replace('--', '-', $url);
}

$urlUnique = $url;
$urlIndex = 0;
$urlExist = null;
do {
    $sql = 'SELECT COUNT(*) FROM shop_product WHERE is_delete=0 AND url = ?';
    $urlExist = $db->getValue($sql, [$urlUnique]);

    if ($urlExist) {
        $urlIndex++;
        $urlUnique = $url . '-' . $urlIndex;
    }
} while ($urlExist);
$url = $urlUnique;


$product = new stdClass();
$product->id = $db->uuid();
$product->spu = $input->relate_key;
$product->name = $input->name;
$product->summary = $input->summary;
$product->description = $input->description;
$product->url = $url;
$product->url_custom = 0;
$product->seo_title = $input->name;
$product->seo_title_custom = 0;
$product->seo_description = $input->summary;
$product->seo_description_custom = 0;
$product->seo_keywords = '';
$product->brand = $input->brand;;
$product->relate_id = '';
$product->style = 2;
$product->stock_tracking = 0;
$product->stock_out_action = 1;
$product->publish_time = date('Y-m-d H:i:s');
$product->ordering = 0;
$product->hits = 0;
$product->sales_volume_base = 0;
$product->sales_volume = 0;
$product->price_from = $input->price;;
$product->price_to = $input->price;;
$product->original_price_from = $input->original_price;;
$product->original_price_to = $input->original_price;;
$product->rating_sum = 0;
$product->rating_count = 0;
$product->rating_avg = 0;
$product->collect_product_id = '';
$product->download_remote_image = 1;
$product->is_enable = 0;
$product->is_delete = 0;
$product->create_time = date('Y-m-d H:i:s');
$product->update_time = date('Y-m-d H:i:s');


$categories = [];
$productCategories = [];
$input->categories = trim($input->categories);
if ($input->categories !== '') {
    $categoryNames = explode("|", $input->categories);
    foreach ($categoryNames as $categoryName) {
        $sql = 'SELECT * FROM shop_category WHERE name=?';
        $existCategory = $db->getObject($sql, [$urlUnique]);
        if ($existCategory) {
            $productCategories[] = [
                'id' => $db->uuid(),
                'product_id' => $product->id,
                'category_id' => $existCategory->id,
                'ordering' => 0,
            ];
   		} else {
            $url = strtolower($categoryName);
            $url = preg_replace('/[^a-z0-9]/', '-', $url);
            $url = str_replace(' ', '-', $url);
            while (strpos($url, '--') !== false) {
                $url = str_replace('--', '-', $url);
            }

            $urlUnique = $url;
            $urlIndex = 0;
            $urlExist = null;
            do {
                $sql = 'SELECT COUNT(*) FROM shop_category WHERE is_delete=0 AND url = ?';
                $urlExist = $db->getValue($sql, [$urlUnique]);

                if ($urlExist) {
                    $urlIndex++;
                    $urlUnique = $url . '-' . $urlIndex;
                }
            } while ($urlExist);
            $url = $urlUnique;

            $category = [
                'id' => $db->uuid(),
                'name' => $categoryName,
                'description' => '',
                'url' => $url,
                'url_custom' => 0,
                'image' => '',
                'seo_title' => $categoryName,
                'seo_title_custom' => 0,
                'seo_description' => $categoryName,
                'seo_description_custom' => 0,
                'seo_keywords' => '',
                'ordering' => 0,
                'is_enable' => 1,
                'is_delete' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];

            $categories[] = $category;

            $productCategories[] = [
                'id' => $db->uuid(),
                'product_id' => $product->id,
                'category_id' => $category['id'],
                'ordering' => 0,
            ];
        }
    }
}


$colorImages = [];

$styles = [];
$style1 = [
    'id' => $db->uuid(),
    'product_id' => $product->id,
    'name' => 'Color',
    'icon_type' => 'image',
    'ordering' => 0,
];
$styles[] = $style1;

$style1Items = [];

$colors = explode('|', $input->color);
$lowerColors = explode('|', strtolower($input->color));
$images_colors = explode('|', $input->images_color);
$count = count($images_colors);
if ($count > 0 && $count % 2 === 0) {
    for ($i = 0; $i < $count; $i += 2) {
        $c = strtolower($images_colors[$i]);
        if (in_array($c, $lowerColors)) {
            if (!in_array($c, $colorImages)) {
                $colorImages[$c] = [];
            }

            $colorImages[$c][] = $images_colors[$i + 1];

            if (!in_array($c, $style1Items)) {
                $style1Items[$c] = [
                    'id' => $db->uuid(),
                    'product_style_id' => $style1['id'],
                    'value' => $colors[array_search($c, $lowerColors)],
                    'icon_image' => $images_colors[$i + 1],
                    'icon_color' => '',
                    'ordering' => $i,
                ];
            }
        }
    }
    $style1Items = array_values($style1Items);
}

$style2Items = null;
$sizes = explode('|', $input->size);
if (count($sizes) > 0) {
    $style2 = [
        'id' => $db->uuid(),
        'product_id' => $product->id,
        'name' => 'Size',
        'icon_type' => 'text',
        'ordering' => 0,
    ];
    $styles[] = $style2;

    $style2Items = [];

    $i = 0;
    foreach ($sizes as $size) {
        $style2Items[] = [
            'id' => $db->uuid(),
            'product_style_id' => $style2['id'],
            'value' => $size,
            'icon_image' => '',
            'icon_color' => '',
            'ordering' => $i,
        ];
        $i++;
    }
}

$productImages = [];
$productItems = [];
if ($style2Items === null) {
    $productItemOrdering = 0;
    foreach ($style1Items as $style1Item) {
        $productItemStyle = $style1Item['value'];
        $productItemStyleJson = json_encode([
            [
                'name' => $style1Item['name'],
                'value' => $style1Item['value'],
            ],
        ]);

        $productItem = [
            'id' => $db->uuid(),
            'product_id' => $product->id,
            'sku' => '',
            'barcode' => '',
            'style' => $productItemStyle,
            'style_json' => $productItemStyleJson,
            'price' => $input->price,
            'original_price' => $input->original_price,
            'weight' => $input->weight,
            'weight_unit' => $input->weight_unit,
            'stock' => 0,
            'ordering' => $productItemOrdering,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $productItemOrdering++;

        $productItems[] = $productItem;

        if (isset($colorImages[strtolower($style1Item['value'])]) && count($colorImages[strtolower($style1Item['value'])]) > 0) {
            $productImageOrdering = 0;
            foreach ($colorImages[strtolower($style1Item['value'])] as $img) {
                $productImages[] = [
                    'id' => $db->uuid(),
                    'product_id' => $product->id,
                    'product_item_id' => $productItem['id'],
                    'url' => $img,
                    'is_main' => $productImageOrdering === 0 ? 1 : 0,
                    'ordering' => $productImageOrdering,
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s'),
                ];
                $productImageOrdering++;
            }
        }
    }

} else {

    $productItemOrdering = 0;
    foreach ($style1Items as $style1Item) {
        foreach ($style2Items as $style2Item) {
            $productItemStyle = $style1Item['value'] . ' ' . $style2Item['value'];
            $productItemStyleJson = json_encode([
                [
                    'name' => $style1Item['name'],
                    'value' => $style1Item['value'],
                ], [
                    'name' => $style2Item['name'],
                    'value' => $style2Item['value'],
                ],
            ]);

            $productItem = [
                'id' => $db->uuid(),
                'product_id' => $product->id,
                'sku' => '',
                'barcode' => '',
                'style' => $productItemStyle,
                'style_json' => $productItemStyleJson,
                'price' => $input->price,
                'original_price' => $input->original_price,
                'weight' => $input->weight,
                'weight_unit' => $input->weight_unit,
                'stock' => 0,
                'ordering' => $productItemOrdering,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ];
            $productItemOrdering++;

            $productItems[] = $productItem;

            if (isset($colorImages[strtolower($style1Item['value'])]) && count($colorImages[strtolower($style1Item['value'])]) > 0) {
                $productImageOrdering = 0;
                foreach ($colorImages[strtolower($style1Item['value'])] as $img) {
                    $productImages[] = [
                        'id' => $db->uuid(),
                        'product_id' => $product->id,
                        'product_item_id' => $productItem['id'],
                        'url' => $img,
                        'is_main' => $productImageOrdering === 0 ? 1 : 0,
                        'ordering' => $productImageOrdering,
                        'create_time' => date('Y-m-d H:i:s'),
                        'update_time' => date('Y-m-d H:i:s'),
                    ];
                    $productImageOrdering++;
                }
            }
        }
    }
}

/*
$output = new stdClass();
$output->product = $product;
$output->productImages = $productImages;
$output->productItems = $productItems;
$output->styles = $styles;
$output->style1Items = $style1Items;
$output->style2Items = $style2Items;
return $output;
*/

$db->startTransaction();
try {

    $db->insert('shop_product', $product);

    foreach ($productImages as $productImage) {
        $db->insert('shop_product_image', $productImage);
    }

    foreach ($productItems as $productItem) {
        $db->insert('shop_product_item', $productItem);
    }

    foreach ($styles as $style) {
        $db->insert('shop_product_style', $style);
    }

    foreach ($style1Items as $styleItem) {
        $db->insert('shop_product_style_item', $styleItem);
    }

    foreach ($style2Items as $styleItem) {
        $db->insert('shop_product_style_item', $styleItem);
    }

    foreach ($categories as $category) {
        $db->insert('shop_category', $category);
    }

    foreach ($productCategories as $productCategory) {
        $db->insert('shop_product_category', $productCategory);
    }

    $db->commit();
} catch (\Throwable $t) {
    $db->rollback();

    throw $t;
}

usleep(100000);

return $input;
