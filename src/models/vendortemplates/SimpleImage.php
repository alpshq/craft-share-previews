<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\TextLayer;

class SimpleImage extends AbstractVendorTemplate
{
    public function init()
    {
        parent::init();

        $this->name = 'Simple White (with Image)';
    }

    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new AssetLayer([
                'assetId' => $vars['entryAssetFallbackId'] ?? null,
                'fieldId' => $vars['entryAssetFieldId'] ?? null,
                'padding' => [0, 0, 0, 0],
                'fillMode' => AssetLayer::FILL_MODE_COVER,
            ]),
            new ColorLayer([
                'color' => [0, 0, 0, 0.2],
                'padding' => [0, 0, 0, 530],
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [75, 15, 550, 530],
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'maxFontSize' => 30,
            ]),
            new AssetLayer([
                'assetId' => $vars['logoId'] ?? null,
                'padding' => [600, 15, 75, 530],
                'horizontalAlign' => AssetLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new GradientLayer([
                'from' => [0, 0, 0, 0],
                'to' => [0, 0, 0, 0.8],
                'angle' => 90,
                'paddingTop' => 275,
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => '{{ entry.title }}',
                'padding' => [75, 350, 75, 15],
                'fontFamilyWithVariant' => ['roboto', '900'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
        ];
    }
}
