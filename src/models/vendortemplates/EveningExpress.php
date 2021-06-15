<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\AssetLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;

class EveningExpress extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => '#FFF7ED',
            ]),
            new LineLayer([
                'color' => '#D97706',
                'x' => 0,
                'y' => 50,
                'length' => 1150,
            ]),
            new LineLayer([
                'color' => '#D97706',
                'x' => 0,
                'y' => 60,
                'length' => 1130,
            ]),
            new LineLayer([
                'color' => '#D97706',
                'x' => 0,
                'y' => 70,
                'length' => 1110,
            ]),
            new LineLayer([
                'color' => '#D97706',
                'x' => 0,
                'y' => 80,
                'length' => 1090,
            ]),
            new LineLayer([
                'color' => '#D97706',
                'lineType' => LineLayer::LINE_TYPE_VERTICAL,
                'x' => 300,
                'y' => 80,
                'length' => 550,
            ]),
            new AssetLayer([
                'assetId' => $vars['entryAssetFallbackId'] ?? null,
                'fieldId' => $vars['entryAssetFieldId'] ?? null,
                'padding' => [20, 120, 920, 250],
                'fillMode' => AssetLayer::FILL_MODE_COVER,
            ]),
            new TextLayer([
                'color' => '#9A3412',
                'content' => '{{ entry.title }}',
                'padding' => [350, 140, 50, 100],
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_TOP,
                'fontFamilyWithVariant' => ['merriweather', '900'],
            ]),
            new TextLayer([
                'color' => '#9A3412',
                'content' => 'Published on: {{ siteName|upper }}',
                'padding' => [350, 530, 50, 0],
                'maxFontSize' => 20,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'fontFamilyWithVariant' => ['merriweather', '700'],
            ]),
        ];
    }
}
