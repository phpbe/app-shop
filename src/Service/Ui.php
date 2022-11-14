<?php

namespace Be\App\Shop\Service;

use Be\Be;

class Ui
{

    /**
     * 获取商品公共CSS
     *
     * @return string
     */
    public function getProductGlobalCss(): string
    {
        $css = '';
        $key = 'Shop:Product:GlobalCss';
        if (!Be::hasContext($key)) {
            $css .= '.icon-star, .icon-star-fill {';
            $css .= 'display: inline-block;';
            $css .= 'width: 1rem;';
            $css .= 'height: 1rem;';
            $css .= 'margin-right: 0.2rem;';
            $css .= 'border: none;';
            $css .= 'background-color: transparent;';
            $css .= 'background-repeat: no-repeat;';
            $css .= 'background-position: center center;';
            $css .= 'background-size: 1rem 1rem;';
            $css .= '}';

            $css .= '.icon-star {';
            $css .= 'background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'%23bbb\' d=\'M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z\'%3e%3c/path%3e%3c/svg%3e");';
            $css .= '}';

            $css .= '.icon-star-fill {';
            $css .= 'background-image: url("data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 16 16\'%3e%3cpath fill=\'%23fd9427\' d=\'M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z\'%3e%3c/path%3e%3c/svg%3e");';
            $css .= '}';

            $css .= '.icon-star-120, .icon-star-fill-120 {';
            $css .= 'width: 1.2rem;';
            $css .= 'height: 1.2rem;';
            $css .= 'margin-right: 0.25rem;';
            $css .= 'background-size: 1.2rem 1.2rem;';
            $css .= '}';

            $css .= '.icon-star-150, .icon-star-fill-150 {';
            $css .= 'width: 1.5rem;';
            $css .= 'height: 1.5rem;';
            $css .= 'margin-right: 0.3rem;';
            $css .= 'background-size: 1.5rem 1.5rem;';
            $css .= '}';

            Be::setContext($key, 1);
        }
        return $css;
    }



}
