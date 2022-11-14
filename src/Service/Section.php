<?php

namespace Be\App\Shop\Service;

use Be\Be;

class Section
{

    /**
     * 生成商品列表部件
     *
     * @param object $section
     * @param string $class
     * @param array $products
     * @param string $moreLink
     * @return string
     */
    public function makeProductsSection(object $section, string $class, array $products, string $moreLink = null): string
    {
        $count = count($products);
        if ($count === 0) {
            return '';
        }

        $html = '';
        $html .= '<style type="text/css">';

        $html .= Be::getService('App.Shop.Ui')->getProductGlobalCss();

        $html .= $section->getCssBackgroundColor($class);
        $html .= $section->getCssPadding($class);
        $html .= $section->getCssMargin($class);

        if ($count === 1) {
            $itemWidthMobile = $itemWidthTablet = '100%';
        } elseif ($count === 2) {
            $itemWidthMobile = $itemWidthTablet = '50%';
        } else {
            $itemWidthMobile = '50%';
            $itemWidthTablet = (100 / 3) . '%';
        }

        $cols = $section->config->cols ?? 4;
        $itemWidthDesktop = (100 / $cols) . '%;';

        $html .= $section->getCssSpacing($class . '-products', $class . '-product', $itemWidthMobile, $itemWidthTablet, $itemWidthDesktop);

        // 手机端小于 320px 时, 100% 宽度
        $html .= '@media (max-width: 320px) {';
        $html .= '#' . $section->id . ' .' . $class . '-product {';
        $html .= 'width: 100% !important;';
        $html .= '}';
        $html .= '}';

        $html .= '#' . $section->id . ' .' . $class . '-title {';
        $html .= 'position: relative;';
        $html .= '}';

        $html .= '#' . $section->id . ' .' . $class . '-title h3 {';
        $html .= 'text-align: ' . $section->config->titleAlign . ';';
        $html .= '}';

        $html .= '#' . $section->id . ' .' . $class . '-title a {';
        $html .= 'position: absolute;';
        $html .= 'top: 0;';
        $html .= 'right: 0;';
        $html .= '}';

        $html .= '#' . $section->id . ' .' . $class . '-product-image {';
        $html .= '}';

        $html .= '#' . $section->id . ' .' . $class . '-product-image .' . $class . '-product-image-1 {';
        $html .= 'width: 100%;';
        $html .= '}';

        if ($section->config->hoverEffect != 'none') {
            if ($section->config->hoverEffect == 'scale' || $section->config->hoverEffect == 'rotateScale') {
                $html .= '#' . $section->id . ' .' . $class . '-product-image a .' . $class . '-product-image-1 {';
                $html .= 'transition: all 0.7s ease;';
                $html .= '}';
            }

            switch ($section->config->hoverEffect) {
                case 'scale':
                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-1 {';
                    $html .= 'transform: scale(1.1);';
                    $html .= '}';
                    break;
                case 'rotateScale':
                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-1 {';
                    $html .= 'transform: rotate(3deg) scale(1.1);';
                    $html .= '}';
                    break;
                case 'toggleImage':
                    $html .= '#' . $section->id . ' .' . $class . '-product-image a {';
                    $html .= 'display:block;';
                    $html .= 'position:relative;';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a .' . $class . '-product-image-1 {';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a .' . $class . '-product-image-2 {';
                    $html .= 'position:absolute;';
                    $html .= 'top:0;';
                    $html .= 'left:0;';
                    $html .= 'right:0;';
                    $html .= 'bottom:0;';
                    $html .= 'width:100%;';
                    $html .= 'height:100%;';
                    $html .= 'opacity:0;';
                    $html .= 'cursor:pointer;';
                    $html .= 'transition: all 0.7s ease;';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-1 {';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-2 {';
                    $html .= 'opacity:1;';
                    $html .= '}';
                    break;
            }
        }
        $html .= '</style>';

        $isMobile = \Be\Be::getRequest()->isMobile();

        $html .= '<div class="' . $class . '">';

        if ($section->position === 'middle' && $section->config->width === 'default') {
            $html .= '<div class="be-container">';
        }

        if ($section->config->title !== '') {
            $html .= $section->page->tag0('be-section-title', true);

            $html .= '<div class="' . $class . '-title">';
            $html .= '<h3 class="be-h3">' . $section->config->title . '</h3>';

            if ($moreLink !== null && isset($section->config->more) && $section->config->more !== '') {
                $html .= '<a href="' . $moreLink . '"';
                if (!$isMobile) {
                    $html .= ' target="_blank"';
                }
                $html .= '>' . $section->config->more . '</a>';
            }
            $html .= '</div>';

            $html .= $section->page->tag1('be-section-title', true);
        }

        $nnImage = Be::getProperty('App.Shop')->getWwwUrl() . '/images/product/no-image.jpg';

        $html .= $section->page->tag0('be-section-content', true);
        $html .= '<div class="' . $class . '-products">';
        foreach ($products as $product) {
            $defaultImage = null;
            $hoverImage = null;
            foreach ($product->images as $image) {
                if ($section->config->hoverEffect == 'toggleImage') {
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

            $html .= '<div class="' . $class . '-product">';

            $html .= '<div class="' . $class . '-product-image">';
            $html .= '<a href="' . beUrl('Shop.Product.detail', ['id' => $product->id]) . '"';
            if (!$isMobile) {
                $html .= ' target="_blank"';
            }
            $html .= '>';
            if ($defaultImage) {
                $html .= '<img src="' . $defaultImage->medium . '" class="' . $class . '-product-image-1" />';
                if ($section->config->hoverEffect == 'toggleImage' && $hoverImage) {
                    $html .= '<img src="' . $hoverImage->medium . '" class="' . $class . '-product-image-2" />';
                }
            }

            $html .= '</a>';
            $html .= '</div>';

            $html .= '<div class="be-mt-50">';
            $averageRating = round($product->rating_avg);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $averageRating) {
                    $html .= '<i class="icon-star-fill icon-star-fill-150"></i>';
                } else {
                    $html .= '<i class="icon-star icon-star-150"></i>';
                }
            }
            $html .= '</div>';

            $html .= '<div class="be-mt-50">';
            $html .= '<a class="be-d-block be-t-ellipsis-2" href="' . beUrl('Shop.Product.detail', ['id' => $product->id]) . '"';
            if (!$isMobile) {
                $html .= ' target="_blank"';
            }
            $html .= '>';
            $html .= $product->name;
            $html .= '</a>';
            $html .= '</div>';

            $html .= '<div class="be-mt-50">';
            if ($product->original_price_from > 0 && $product->original_price_from != $product->price_from) {
                $html .= '<span class="be-td-line-through be-mr-50 be-c-999">$';
                if ($product->original_price_from === $product->original_price_to) {
                    $html .= $product->original_price_from;
                } else {
                    $html .= $product->original_price_from . '~' . $product->original_price_to;;
                }
                $html .= '</span>';
            }

            $html .= '<span class="be-fw-bold">$';
            if ($product->price_from === $product->price_to) {
                $html .= $product->price_from;
            } else {
                $html .= $product->price_from . '~' . $product->price_to;;
            }
            $html .= '</span>';

            $html .= '</div>';

            $buttonClass = 'be-btn';
            if (isset($section->config->buttonClass) && $section->config->buttonClass !== '') {
                $buttonClass = $section->config->buttonClass;
            } elseif (isset($section->page->pageConfig->buttonClass) && $section->page->pageConfig->buttonClass !== '') {
                $buttonClass = $section->page->pageConfig->buttonClass;
            }

            $html .= '<div class="be-mt-50">';
            if (count($product->items) > 1) {
                $html .= '<input type="button" class="' . $buttonClass . '" value="Quick Buy" onclick="quickBuy(\'' . $product->id . '\')">';
            } else {
                $productItem = $product->items[0];
                $html .= '<input type="button" class="' . $buttonClass . '" value="Add to Cart" onclick="addToCart(\'' . $product->id . '\', \'' . $productItem->id . '\')">';
            }
            $html .= '</div>';

            $html .= '</div>';
        }
        $html .= '</div>';

        $html .= $section->page->tag1('be-section-content', true);

        if ($section->position === 'middle' && $section->config->width === 'default') {
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * 生成分页商品列表部件
     *
     * @param object $section
     * @param string $class
     * @param array $result
     * @param string $paginationUrl
     * @return string
     */
    public function makePaginationProductsSection(object $section, string $class, array $result, string $paginationUrl = null): string
    {
        if ($result['total'] === 0) {
            return '';
        }

        $html = '';
        $html .= '<style type="text/css">';

        $html .= Be::getService('App.Shop.Ui')->getProductGlobalCss();

        $html .= $section->getCssBackgroundColor($class);
        $html .= $section->getCssPadding($class);
        $html .= $section->getCssMargin($class);

        if ($result['total'] === 1) {
            $itemWidthMobile = $itemWidthTablet = '100%';
        } elseif ($result['total'] === 2) {
            $itemWidthMobile = $itemWidthTablet = '50%';
        } else {
            $itemWidthMobile = '50%';
            $itemWidthTablet = (100 / 3) . '%';
        }

        $cols = $section->config->cols ?? 4;
        $itemWidthDesktop = (100 / $cols) . '%;';

        $html .= $section->getCssSpacing($class . '-products', $class . '-product', $itemWidthMobile, $itemWidthTablet, $itemWidthDesktop);

        $html .= '#' . $section->id . ' .' . $class . '-product-image {';
        $html .= '}';

        $html .= '#' . $section->id . ' .' . $class . '-product-image .' . $class . '-product-image-1 {';
        $html .= 'width: 100%;';
        $html .= '}';

        if ($section->config->hoverEffect != 'none') {
            if ($section->config->hoverEffect == 'scale' || $section->config->hoverEffect == 'rotateScale') {
                $html .= '#' . $section->id . ' .' . $class . '-product-image a .' . $class . '-product-image-1 {';
                $html .= 'transition: all 0.7s ease;';
                $html .= '}';
            }

            switch ($section->config->hoverEffect) {
                case 'scale':
                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-1 {';
                    $html .= 'transform: scale(1.1);';
                    $html .= '}';
                    break;
                case 'rotateScale':
                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-1 {';
                    $html .= 'transform: rotate(3deg) scale(1.1);';
                    $html .= '}';
                    break;
                case 'toggleImage':
                    $html .= '#' . $section->id . ' .' . $class . '-product-image a {';
                    $html .= 'display:block;';
                    $html .= 'position:relative;';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a .' . $class . '-product-image-1 {';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a .' . $class . '-product-image-2 {';
                    $html .= 'position:absolute;';
                    $html .= 'top:0;';
                    $html .= 'left:0;';
                    $html .= 'right:0;';
                    $html .= 'bottom:0;';
                    $html .= 'width:100%;';
                    $html .= 'height:100%;';
                    $html .= 'opacity:0;';
                    $html .= 'cursor:pointer;';
                    $html .= 'transition: all 0.7s ease;';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-1 {';
                    $html .= '}';

                    $html .= '#' . $section->id . ' .' . $class . '-product-image a:hover .' . $class . '-product-image-2 {';
                    $html .= 'opacity:1;';
                    $html .= '}';
                    break;
            }
        }
        $html .= '</style>';

        $isMobile = \Be\Be::getRequest()->isMobile();

        $html .= '<div class="' . $class . '">';

        if ($section->position === 'middle' && $section->config->width === 'default') {
            $html .= '<div class="be-container">';
        }

        $nnImage = \Be\Be::getProperty('App.Shop')->getWwwUrl() . '/images/product/no-image.jpg';

        $html .= $section->page->tag0('be-section-content', true);

        $html .= '<div class="' . $class . '-products">';

        $i = 0;
        foreach ($result['rows'] as $product) {
            $defaultImage = null;
            $hoverImage = null;
            foreach ($product->images as $image) {
                if ($section->config->hoverEffect == 'toggleImage') {
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

            $html .= '<div class="' . $class . '-product">';

            $html .= '<div class="' . $class . '-product-image">';
            $html .= '<a href="' . beUrl('Shop.Product.detail', ['id' => $product->id]) . '"';
            if (!$isMobile) {
                $html .= ' target="_blank"';
            }
            $html .= '>';
            if ($defaultImage) {
                $html .= '<img src="' . $defaultImage->medium . '" class="' . $class . '-product-image-1" />';
                if ($section->config->hoverEffect == 'toggleImage' && $hoverImage) {
                    $html .= '<img src="' . $hoverImage->medium . '" class="' . $class . '-product-image-2" />';
                }
            }

            $html .= '</a>';
            $html .= '</div>';

            $html .= '<div class="be-mt-50">';
            $averageRating = round($product->rating_avg);
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $averageRating) {
                    $html .= '<i class="icon-star-fill icon-star-fill-150"></i>';
                } else {
                    $html .= '<i class="icon-star icon-star-150"></i>';
                }
            }
            $html .= '</div>';

            $html .= '<div class="be-mt-50">';
            $html .= '<a class="be-d-block be-t-ellipsis-2" href="' . beUrl('Shop.Product.detail', ['id' => $product->id]) . '"';
            if (!$isMobile) {
                $html .= ' target="_blank"';
            }
            $html .= '>';
            $html .= $product->name;
            $html .= '</a>';
            $html .= '</div>';

            $html .= '<div class="be-mt-50">';
            if ($product->original_price_from > 0 && $product->original_price_from != $product->price_from) {
                $html .= '<span class="be-td-line-through be-mr-50 be-c-999">$';
                if ($product->original_price_from === $product->original_price_to) {
                    $html .= $product->original_price_from;
                } else {
                    $html .= $product->original_price_from . '~' . $product->original_price_to;;
                }
                $html .= '</span>';
            }

            $html .= '<span class="be-fw-bold">$';
            if ($product->price_from === $product->price_to) {
                $html .= $product->price_from;
            } else {
                $html .= $product->price_from . '~' . $product->price_to;;
            }
            $html .= '</span>';

            $html .= '</div>';


            $buttonClass = 'be-btn';
            if (isset($section->config->buttonClass) && $section->config->buttonClass !== '') {
                $buttonClass = $section->config->buttonClass;
            } elseif (isset($section->page->pageConfig->buttonClass) && $section->page->pageConfig->buttonClass !== '') {
                $buttonClass = $section->page->pageConfig->buttonClass;
            }

            $html .= '<div class="be-mt-50">';
            if (count($product->items) > 1) {
                $html .= '<input type="button" class="' . $buttonClass . '" value="Quick Buy" onclick="quickBuy(\'' . $product->id . '\')">';
            } else {
                $productItem = $product->items[0];
                $html .= '<input type="button" class="' . $buttonClass . '" value="Add to Cart" onclick="addToCart(\'' . $product->id . '\', \'' . $productItem->id . '\')">';
            }
            $html .= '</div>';

            $html .= '</div>';
        }
        $html .= '</div>';

        $total = $result['total'];
        $pageSize = $result['pageSize'];
        $pages = ceil($total / $pageSize);
        if ($pages > 1) {
            $page = $result['page'];
            if ($page > $pages) $page = $pages;

            $paginationUrl .= strpos($paginationUrl, '?') === false ? '?' : '&';

            $html = '<nav class="be-mt-300">';
            $html .= '<ul class="be-pagination" style="justify-content: center;">';
            $html .= '<li>';
            if ($page > 1) {
                $url = $paginationUrl;
                $url .= http_build_query(['page' => ($page - 1)]);
                $html .= '<a href="' . $url . '">Preview</a>';
            } else {
                $html .= '<span>Preview</span>';
            }
            $html .= '</li>';

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
                $html .= '<li><span>...</span></li>';
            }

            for ($i = $from; $i <= $to; $i++) {
                if ($i == $page) {
                    $html .= '<li class="active">';
                    $html .= '<span>' . $i . '</span>';
                    $html .= '</li>';
                } else {
                    $url = $paginationUrl;
                    $url .= http_build_query(['page' => $i]);
                    $html .= '<li>';
                    $html .= '<a href="' . $url . '">' . $i . '</a>';
                    $html .= '</li>';
                }
            }

            if ($to < $pages) {
                $html .= '<li><span>...</span></li>';
            }

            $html .= '<li>';
            if ($page < $pages) {
                $url = $paginationUrl;
                $url .= http_build_query(['page' => ($page + 1)]);
                $html .= '<a href="' . $url . '">Next</a>';
            } else {
                $html .= '<span>Next</span>';
            }
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</nav>';

        }

        $html .= $section->page->tag1('be-section-content', true);

        if ($section->position === 'middle' && $section->config->width === 'default') {
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

}
