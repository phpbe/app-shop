<be-head>
    <?php
    echo '<style type="text/css">';

    echo '.shopfai-products {';
    echo 'display: flex;';
    echo 'flex-wrap: wrap;';
    echo 'overflow: hidden;';
    echo '}';

    echo '.shopfai-product {';
    echo 'flex: 0 1 auto;';
    echo 'overflow: hidden;';
    echo '}';


    // 手机端小于 320px 时, 100% 宽度
    echo '@media (max-width: 320px) {';
    echo '.shopfai-product {';
    echo 'width: 100% !important;';
    echo '}';
    echo '}';

    // 手机端
    if (isset($this->pageConfig->spacingMobile) && $this->pageConfig->spacingMobile !== '') {
        echo '@media (max-width: 768px) {';

        echo '.shopfai-products {';
        echo 'margin-left: calc(-' . $this->pageConfig->spacingMobile . ' / 2);';
        echo 'margin-right: calc(-' . $this->pageConfig->spacingMobile . ' / 2);';
        echo 'margin-bottom: -' . $this->pageConfig->spacingMobile . ';';
        echo '}';

        echo '.shopfai-product {';
        echo 'width: 50%;';
        echo 'padding-left: calc(' . $this->pageConfig->spacingMobile . ' / 2);';
        echo 'padding-right: calc(' . $this->pageConfig->spacingMobile . ' / 2);';
        echo 'margin-bottom: ' . $this->pageConfig->spacingMobile . ';';
        echo '}';

        echo '}';
    }


    // 平析端
    if (isset($this->pageConfig->spacingTablet) && $this->pageConfig->spacingTablet !== '') {
        echo '@media (min-width: 768px) {';

        echo '.shopfai-products {';
        echo 'margin-left: calc(-' . $this->pageConfig->spacingTablet . ' / 2);';
        echo 'margin-right: calc(-' . $this->pageConfig->spacingTablet . ' / 2);';
        echo 'margin-bottom: -' . $this->pageConfig->spacingTablet . ';';
        echo '}';

        echo '.shopfai-product {';
        $cols = min($this->pageConfig->cols, 3);
        echo 'width: ' . (100 / $cols) . '%;';
        echo 'padding-left: calc(' . $this->pageConfig->spacingTablet . ' / 2);';
        echo 'padding-right: calc(' . $this->pageConfig->spacingTablet . ' / 2);';
        echo 'margin-bottom: ' . $this->pageConfig->spacingTablet . ';';
        echo '}';

        echo '}';
    }


    // 电脑端
    if (isset($this->pageConfig->spacingDesktop) && $this->pageConfig->spacingDesktop !== '') {
        echo '@media (min-width: 992px) {';

        echo '.shopfai-products {';
        echo 'margin-left: calc(-' . $this->pageConfig->spacingDesktop . ' / 2);';
        echo 'margin-right: calc(-' . $this->pageConfig->spacingDesktop . ' / 2);';
        echo 'margin-bottom: -' . $this->pageConfig->spacingDesktop . ';';
        echo '}';

        echo '.shopfai-product {';
        $cols = $this->pageConfig->cols;
        echo 'width: ' . (100 / $cols) . '%;';
        echo 'padding-left: calc(' . $this->pageConfig->spacingDesktop . ' / 2);';
        echo 'padding-right: calc(' . $this->pageConfig->spacingDesktop . ' / 2);';
        echo 'margin-bottom: ' . $this->pageConfig->spacingDesktop . ';';
        echo '}';

        echo '}';
    }

    echo  '.shopfai-product-image {';
    echo  '}';

    echo  '.shopfai-product-image-1 {';
    echo  'width: 100%;';
    echo  '}';

    if ($this->pageConfig->hoverEffect != 'none') {
        if ($this->pageConfig->hoverEffect == 'scale' || $this->pageConfig->hoverEffect == 'rotateScale') {
            echo '.shopfai-product-image a .shopfai-product-image-img {';
            echo 'transition: all 0.7s ease;';
            echo '}';
        }

        switch ($this->pageConfig->hoverEffect) {
            case 'scale':
                echo '.shopfai-product-image a:hover .shopfai-product-image-img {';
                echo 'transform: scale(1.1);';
                echo '}';
                break;
            case 'rotateScale':
                echo '.shopfai-product-image a:hover .shopfai-product-image-img {';
                echo 'transform: rotate(3deg) scale(1.1);';
                echo '}';
                break;
            case 'toggleImage':
                echo  '.shopfai-product-image a {';
                echo  'display:block;';
                echo  'position:relative;';
                echo  '}';

                echo  '.shopfai-product-image a .shopfai-product-image-1 {';
                echo  '}';

                echo  '.shopfai-product-image a .shopfai-product-image-2 {';
                echo  'position:absolute;';
                echo  'top:0;';
                echo  'left:0;';
                echo  'right:0;';
                echo  'bottom:0;';
                echo  'width:100%;';
                echo  'height:100%;';
                echo  'opacity:0;';
                echo  'cursor:pointer;';
                echo  'transition: all 0.7s ease;';
                echo  '}';

                echo  '.shopfai-product-image a:hover .shopfai-product-image-1 {';
                echo  '}';

                echo  '.shopfai-product-image a:hover .shopfai-product-image-2 {';
                echo  'opacity:1;';
                echo  '}';
                break;
        }
    }
    echo '</style>';
    ?>
</be-head>


<be-page-content>
    <?php
    $isMobile = \Be\Be::getRequest()->isMobile();
    $nnImage = \Be\Be::getProperty('App.ShopFai')->getWwwUrl() . '/images/product/no-image.jpg';

    echo '<div class="shopfai-products">';
    $i = 0;
    foreach ($this->result['rows'] as $product) {
        $defaultImage = null;
        $hoverImage = null;
        foreach ($product->images as $image) {
            if ($this->pageConfig->hoverEffect == 'toggleImage') {
                if ($image->is_main) {
                    $defaultImage = $image;
                } else {
                    $hoverImage = $image;
                }

                if ($defaultImage && $hoverImage) {
                    break;
                }
            } else {
                if ($image->is_main) {
                    $defaultImage = $image;
                    break;
                }
            }
        }

        if (!$defaultImage && count($product->images) > 0) {
            $defaultImage = $product->images[0];
        }

        if (!$defaultImage) {
            $defaultImage = (object)[
                'id' => '',
                'product_id' => $product->id,
                'small' => $nnImage,
                'medium' => $nnImage,
                'large' => $nnImage,
                'original' => $nnImage,
                'is_main' => 1,
                'ordering' => 0,
            ];
        }

        echo '<div class="shopfai-product">';

        echo '<div class="shopfai-product-image">';
        echo '<a href="' . beUrl('ShopFai.Product.detail', ['id' => $product->id]) . '"';
        if (!$isMobile) {
            echo ' target="_blank"';
        }
        echo '>';
        if ($defaultImage) {
            echo '<img src="' . $defaultImage->medium . '" class="shopfai-product-image-1" />';
            if ($this->pageConfig->hoverEffect == 'toggleImage' && $hoverImage) {
                echo '<img src="' . $hoverImage->medium . '" class="shopfai-product-image-2" />';
            }
        }

        echo '</a>';
        echo '</div>';

        echo '<div class="be-mt-50">';
        $averageRating = round($product->rating_avg);
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $averageRating) {
                echo '<i class="icon-star-fill icon-star-fill-150"></i>';
            } else {
                echo '<i class="icon-star icon-star-150"></i>';
            }
        }
        echo '</div>';

        echo '<div class="be-mt-50">';
        echo '<a class="be-d-block be-t-ellipsis-2" href="' . beUrl('ShopFai.Product.detail', ['id' => $product->id]) . '"';
        if (!$isMobile) {
            echo ' target="_blank"';
        }
        echo '>';
        echo $product->name;
        echo '</a>';
        echo '</div>';

        echo '<div class="be-mt-50">';
        if ($product->original_price_from > 0 && $product->original_price_from != $product->price_from) {
            echo '<span class="be-td-line-through be-mr-50 be-c-999">$';
            if ($product->original_price_from === $product->original_price_to) {
                echo $product->original_price_from;
            } else {
                echo $product->original_price_from . '~' . $product->original_price_to;;
            }
            echo '</span>';
        }

        echo '<span class="be-fw-bold">$';
        if ($product->price_from === $product->price_to) {
            echo $product->price_from;
        } else {
            echo $product->price_from . '~' . $product->price_to;;
        }
        echo '</span>';

        echo '</div>';

        $buttonClass = 'be-btn';
        if (isset($this->pageConfig->buttonClass) && $this->pageConfig->buttonClass !== '') {
            $buttonClass = $this->pageConfig->buttonClass;
        }

        echo '<div class="be-mt-50">';
        if (count($product->items) > 1) {
            echo '<input type="button" class="' . $buttonClass . '" value="Quick Buy" onclick="quickBuy(\'' . $product->id . '\')">';
        } else {
            $productItem = $product->items[0];
            echo '<input type="button" class="' . $buttonClass . '" value="Add to Cart" onclick="addToCart(\'' . $product->id . '\', \'' . $productItem->id . '\')">';
        }
        echo '</div>';

        echo '</div>';
    }
    echo '</div>';

    $total = $this->result['total'];
    $pageSize = $this->result['pageSize'];
    $pages = ceil($total / $pageSize);
    if ($pages > 1) {
        $page = $this->result['page'];
        if ($page > $pages) $page = $pages;

        $paginationUrl = $this->paginationUrl;
        $paginationUrl .= strpos($paginationUrl, '?') === false ? '?' : '&';

        $html = '<nav class="be-mt-300">';
        echo '<ul class="be-pagination" style="justify-content: center;">';
        echo '<li>';
        if ($page > 1) {
            $url = $paginationUrl;
            $url .= http_build_query(['page' => ($page - 1)]);
            echo '<a href="' . $url . '">上一页</a>';
        } else {
            echo '<span>上一页</span>';
        }
        echo '</li>';

        $from = null;
        $to = null;
        if ($pages < 9) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $page - 4;
            if ($from < 1) {
                $from = 1;
            }

            $to = $from + 8;
            if ($to > $pages) {
                $to = $pages;
            }
        }

        if ($from > 1) {
            echo '<li><span>...</span></li>';
        }

        for ($i = $from; $i <= $to; $i++) {
            if ($i == $page) {
                echo '<li class="active">';
                echo '<span>' . $i . '</span>';
                echo '</li>';
            } else {
                $url = $paginationUrl;
                $url .= http_build_query(['page' => $i]);
                echo '<li>';
                echo '<a href="' . $url . '">' . $i . '</a>';
                echo '</li>';
            }
        }

        if ($to < $pages) {
            echo '<li><span>...</span></li>';
        }

        echo '<li>';
        if ($page < $pages) {
            $url = $paginationUrl;
            $url .= http_build_query(['page' => ($page + 1)]);
            echo '<a href="' . $url . '">下一页</a>';
        } else {
            echo '<span>下一页</span>';
        }
        echo '</li>';
        echo '</ul>';
        echo '</nav>';

        echo $html;
    }
    ?>
</be-page-content>
