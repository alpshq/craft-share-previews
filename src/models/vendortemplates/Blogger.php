<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;

class Blogger extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new AssetLayer([
                'assetId' => $vars['entryAssetFallbackId'] ?? null,
                'fieldId' => $vars['entryAssetFieldId'] ?? null,
                'paddingRight' => 595,
                'fillMode' => AssetLayer::FILL_MODE_COVER,
            ]),
            new GradientLayer([
                'from' => '#4ADE80',
                'to' => '#3B82F6',
                'angle' => 45,
                'paddingLeft' => 595,
            ]),
            new ColorLayer([
                'color' => [255, 255, 255],
                'padding' => [595, 0, 595, 0],
            ]),
            new TextLayer([
                'color' => [255, 255, 255],
                'content' => '{{ entry.title }}',
                'padding' => [640, 50, 40, 120],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
            ]),
            new LineLayer([
                'color' => [255, 255, 255, 0.7],
                'x' => 640,
                'y' => 530,
                'length' => 520,
            ]),
            new TextLayer([
                'color' => [255, 255, 255, 0.5],
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [640, 550, 40, 20],
                'maxFontSize' => 25,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
            ]),
        ];
    }
}
