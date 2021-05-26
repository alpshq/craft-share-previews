<?php

namespace alps\sharepreviews\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class FontAwesomeAssets extends AssetBundle
{
    public $depends = [CpAsset::class];

    public $sourcePath = '@share-previews/../dist/build/fortawesome';

    public $css = [
        'fontawesome-pro/css/fontawesome.min.css',
        'fontawesome-pro/css/light.min.css',
    ];

    public $publishOptions = [
        'only' => [
            'css/*',
            'js/*',
            'webfonts/*',
            'sprites/*',
            'svgs/*',
        ],
    ];
}
