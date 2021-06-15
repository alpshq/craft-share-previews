<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;

class Quote extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => [255, 255, 255],
            ]),
            new LineLayer([
                'color' => '#94A3B8',
                'x' => 200,
                'y' => 420,
                'length' => 800,
            ]),
            new TextLayer([
                'color' => '#475569',
                'content' => '{{ entry.title }}',
                'padding' => [50, 50, 50, 240],
                'fontFamilyWithVariant' => ['roboto', '900'],
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_BOTTOM,
            ]),
            new TextLayer([
                'color' => '#94A3B8',
                'content' => sprintf(
                    '%s: %s',
                    $vars['siteName'] ?? '',
                    $vars['siteUrl'] ?? ''
                ),
                'padding' => [200, 450, 200, 50],
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'maxFontSize' => 30,
                'verticalAlign' => TextLayer::VERTICAL_ALIGN_TOP,
            ]),
        ];
    }
}
