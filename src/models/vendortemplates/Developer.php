<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;

class Developer extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => '#E0E7FF',
            ]),
            new ColorLayer([
                'color' => '#312E81',
                'padding' => [70, 70, 40, 40],
            ]),
            new ColorLayer([
                'color' => '312E81',
                'padding' => 54,
            ]),
            new ColorLayer([
                'color' => [255, 255, 255],
                'padding' => 55,
            ]),
            new AssetLayer([
                'assetId' => $vars['logoId'] ?? null,
                'padding' => [100, 630 - 150, 100, 70],
                'horizontalAlign' => AssetLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new TextLayer([
                'color' => '#818CF8',
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [100, 630 - 150, 100, 70],
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'maxFontSize' => 25,
            ]),
            new LineLayer([
                'x' => 100,
                'y' => 630 - 170,
                'length' => 1000,
                'color' => '#818CF8',
            ]),
            new TextLayer([
                'color' => '#312E81',
                'content' => '{{ entry.title }}',
                'padding' => [100, 100, 100, 190],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
        ];
    }
}
