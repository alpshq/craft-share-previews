<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\TextLayer;

class BlackTouchesWhite extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => [255, 255, 255],
                'paddingRight' => 600,
            ]),
            new ColorLayer([
                'color' => [0, 0, 0],
                'paddingLeft' => 600,
            ]),
            new AssetLayer([
                'assetId' => $vars['logoId'] ?? null,
                'padding' => [460, 530, 650, 50],
                'horizontalAlign' => AssetLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new TextLayer([
                'color' => '#94A3B8',
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [50, 530, 760, 50],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_BOTTOM,
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'maxFontSize' => 30,
            ]),
            new AssetLayer([
                'assetId' => $vars['entryAssetFallbackId'] ?? null,
                'fieldId' => $vars['entryAssetFieldId'] ?? null,
                'padding' => [50, 50, 650, 200],
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_TOP,
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => '{{ entry.title }}',
                'padding' => [650, 50, 50, 50],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
            ]),
        ];
    }
}
