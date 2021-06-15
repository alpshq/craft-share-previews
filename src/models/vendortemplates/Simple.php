<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\TextLayer;

class Simple extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => [255, 255, 255],
            ]),
            new TextLayer([
                'color' => [0, 0, 0],
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
            new TextLayer([
                'color' => '#000',
                'content' => '{{ entry.title }}',
                'padding' => [75, 130, 75, 15],
                'fontFamilyWithVariant' => ['roboto', '900'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
        ];
    }
}
