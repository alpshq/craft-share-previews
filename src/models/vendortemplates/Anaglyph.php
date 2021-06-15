<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\TextLayer;

class Anaglyph extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new TextLayer([
                'color' => [80, 80, 80],
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [20, 570, 610, 10],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
                'maxFontSize' => 20,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
            new TextLayer([
                'color' => [80, 80, 80],
                'content' => $vars['siteUrl'] ?? '',
                'padding' => [610, 570, 20, 10],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
                'maxFontSize' => 20,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new TextLayer([
                'color' => [30, 242, 241],
                'content' => '{{ entry.title }}',
                'padding' => [46, 46, 54, 104],
                'fontFamilyWithVariant' => ['roboto', '900'],
            ]),
            new TextLayer([
                'color' => [246, 5, 10],
                'content' => '{{ entry.title }}',
                'padding' => [53, 53, 47, 97],
                'fontFamilyWithVariant' => ['roboto', '900'],
            ]),
            new TextLayer([
                'color' => [0, 0, 0],
                'content' => '{{ entry.title }}',
                'padding' => [50, 50, 50, 100],
                'fontFamilyWithVariant' => ['roboto', '900'],
            ]),
        ];
    }
}
