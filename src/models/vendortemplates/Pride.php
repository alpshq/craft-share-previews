<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\TextLayer;

class Pride extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => '#ff0018',
            ]),
            new ColorLayer([
                'color' => '#ffa52c',
                'paddingTop' => 105,
            ]),
            new ColorLayer([
                'color' => '#ffff41',
                'paddingTop' => 105 * 2,
            ]),
            new ColorLayer([
                'color' => '#008018',
                'paddingTop' => 105 * 3,
            ]),
            new ColorLayer([
                'color' => '#0000f9',
                'paddingTop' => 105 * 4,
            ]),
            new ColorLayer([
                'color' => '#86007d',
                'paddingTop' => 105 * 5,
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
            new TextLayer([
                'color' => '#fff',
                'content' => '{{ entry.title }}',
                'padding' => [75, 330, 75, 15],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
        ];
    }
}
