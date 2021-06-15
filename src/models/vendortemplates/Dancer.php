<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\TextLayer;

class Dancer extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new TextLayer([
                'color' => '#94A3B8',
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [20, 570, 610, 10],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
                'maxFontSize' => 20,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
            new TextLayer([
                'color' => '#94A3B8',
                'content' => $vars['siteUrl'] ?? '',
                'padding' => [610, 570, 20, 10],
                'fontFamilyWithVariant' => ['roboto-condensed', '700'],
                'maxFontSize' => 20,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new TextLayer([
                'color' => '#475569',
                'content' => '{{ entry.title }}',
                'padding' => [50, 50, 50, 100],
                'fontFamilyWithVariant' => ['dancing-script', '700'],
            ]),
        ];
    }
}
