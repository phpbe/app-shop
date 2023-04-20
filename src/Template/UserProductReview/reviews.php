<be-head>
    <?php
    $wwwUrl = \Be\Be::getProperty('App.Shop')->getWwwUrl();
    ?>
    <link rel="stylesheet" href="<?php echo $wwwUrl; ?>/css/user-center/user-center.css" />
</be-head>


<be-page-content>
    <div class="be-d-none be-md-d-block">
        <h4 class="be-h4">My Reviews</h4>

        <table class="be-mt-200 be-table">
            <thead>
            <tr>
                <th></th>
                <th class="be-ta-left">Products</th>
                <th class="be-ta-center">Content</th>
                <th class="be-ta-center"></th>
            </tr>
            </thead>
            <?php
            if (count($this->reviews) > 0) {
                ?>
                <tbody>
                <?php
                foreach ($this->reviews as $review) {
                    $product = $review->product;
                    ?>
                    <tr>
                        <td class="be-table-image">
                            <a href="<?php echo $product->url; ?>" target="_blank">
                                <img src="<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>">
                            </a>
                        </td>
                        <td class="be-ta-left">
                            <a href="<?php echo $product->url; ?>" target="_blank">
                                <?php echo $product->name; ?>
                            </a>
                        </td>
                        <td class="be-ta-center"><?php echo $review->content; ?></td>
                        <td class="be-ta-center">
                            <a class="be-btn be-btn-outline" href="<?php echo beUrl('Shop.UserCenter.reviewDetail', ['reviewId' => $review->id]); ?>">View more</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <?php
            } else {
                ?>
                <tbody>
                <tr>
                    <td class="be-table-no-record" colspan="4">No records</td>
                </tr>
                </tbody>
                <?php
            }
            ?>
        </table>

        <?php
        if ($this->total > 0) {
            $paginationUrl = [
                'route' => 'Shop.UserProductReview.reviews',
                'params' => []
            ];

            $total = $this->total;
            $pageSize = $this->pageSize;
            $page = $this->page;
            $pages = $this->pages;

            echo '<nav class="be-mt-150">';
            echo '<ul class="be-pagination" style="justify-content: center;">';
            echo '<li>';
            if ($page > 1) {
                $url = beUrl($paginationUrl['route'], array_merge($paginationUrl['params'], ['page' => ($page - 1)]));
                echo '<a href="' . $url . '">Previous</a>';
            } else {
                echo '<span>Previous</span>';
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
                    $url = beUrl($paginationUrl['route'], array_merge($paginationUrl['params'], ['page' => $i]));
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
                $url = beUrl($paginationUrl['route'], array_merge($paginationUrl['params'], ['page' => ($page + 1)]));
                echo '<a href="' . $url . '">Next</a>';
            } else {
                echo '<span>Next</span>';
            }
            echo '</li>';
            echo '</ul>';
            echo '</nav>';
        }
        ?>
    </div>
</be-page-content>