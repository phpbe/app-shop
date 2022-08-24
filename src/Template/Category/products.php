<be-head>

</be-head>


<be-middle>
    <?php
    $configPage = \Be\Be::getConfig('Theme.ShopFai.Page.CategoryProducts');
    if (isset($configPage->middleSections) && count($configPage->middleSections) > 0) {
        $sectionType = 'middle';
        foreach ($configPage->middleSections as $sectionKey => $sectionName) {
            $sectionData = $configPage->middleSectionsData[$sectionKey];
            echo '<div id="be-section-'.$sectionType.'-'.$sectionKey.'">';
            include \Be\Be::getRuntime()->getRootPath() . '/' . \Be\Be::getProperty('Theme.ShopFai')->getPath() . '/Section/'.$sectionName.'.php';
            echo '</div>';
        }
    }
    ?>
</be-middle>
