<be-head>
    <style type="text/css">

    </style>
</be-head>

<be-page-content>
    <?php
    if ($this->promotionActivity->poster) {
        ?>
        <div class="be-d-none be-d-md-block be-ta-center">
            <img src="<?php echo $this->promotionActivity->poster_desktop; ?>" alt="<?php echo $this->promotionActivity->name; ?>">
        </div>
        <div class="be-d-block be-d-md-none be-ta-center">
            <img src="<?php echo $this->promotionActivity->poster_mobile; ?>" alt="<?php echo $this->promotionActivity->name; ?>">
        </div>
        <?php
    }
    ?>

    <h1 class="be-h1 be-ta-center be-lh-300"><?php echo $this->promotionActivity->name; ?></h1>

    <?php
    $sectionPrefix = 'promotion-activity';
    $sectionType = 'detail';
    $sectionKey = '0';

    echo '<div class="be-mt-200" id="' . $sectionPrefix . '-' . $sectionType . '-' . $sectionKey . '">';
    $result = $this->products;
    if ($result['total'] > 0) {
        $paginationUrl = beUrl('ShopFai.PromotionActivity.detail', ['id' => $this->promotionActivity->id]);
        $sectionData = [
            'spacingMobile' => 20,
            'spacingTablet' => 30,
            'spacingDesktop' => 40,
            'quantityPerRow' => 4,
            'hoverEffect' => 'toggleImage',
        ];
        echo \Be\Be::getService('Theme.ShopFai.Product')->makeProducts($sectionPrefix, $sectionType, $sectionKey, $sectionData, $result, $paginationUrl);
    } else {
        echo '<div class="be-py-400 be-ta-center">-</div>';
    }
    ?>

</be-page-content>