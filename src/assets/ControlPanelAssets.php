<?php

namespace alps\sharepreviews\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ControlPanelAssets extends AssetBundle
{
    public function init()
    {
        parent::init();

        $this->sourcePath = '@share-previews/../dist/build/resources';

        $this->depends = [CpAsset::class];

        $this->css = [
            'cp-styles.css',
        ];

        $this->js = [
            'app.js',
        ];
    }
}
