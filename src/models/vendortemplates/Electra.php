<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\TextLayer;

class Electra extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new GradientLayer([
                'from' => [195, 130, 250],
                'to' => [240, 70, 70],
                'angle' => 45,
            ]),
            new AssetLayer([
                'assetId' => $vars['logoId'] ?? null,
                'padding' => [40, 630 - 100, 40, 40],
                'horizontalAlign' => AssetLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => $vars['siteName'] ?? '',
                'padding' => [150, 630 - 100, 1200 / 2 - 20, 40],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_BOTTOM,
                'maxFontSize' => 30,
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => $vars['siteUrl'] ?? '',
                'padding' => [1200 / 2 + 20, 630 - 100, 40, 40],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_RIGHT,
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_BOTTOM,
                'maxFontSize' => 20,
            ]),
            new AssetLayer([
                'assetId' => $vars['entryAssetFallbackId'] ?? null,
                'fieldId' => $vars['entryAssetFieldId'] ?? null,
                'padding' => [730, 40, 40, 130],
                'fillMode' => AssetLayer::FILL_MODE_COVER,
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => '{{ entry.title }}',
                'padding' => [40, 40, 520, 170],
                'fontFamilyWithVariant' => ['roboto', '700'],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
        ];
    }
}
