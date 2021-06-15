<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\TextLayer;

class DoubleStatement extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        return [
            new ColorLayer([
                'color' => [0, 0, 0],
            ]),
            new ColorLayer([
                'color' => [255, 255, 255],
                'paddingBottom' => 315,
            ]),
            new TextLayer([
                'color' => [0, 0, 0],
                'content' => '{{ entry.title }}',
                'padding' => [50, 50, 50, 315],
                'fontFamilyWithVariant' => ['roboto', '900'],
            ]),
            new TextLayer([
                'color' => [255, 255, 255, 0.5],
                'content' => sprintf(
                    '%s: %s',
                    $vars['siteName'] ?? '',
                    $vars['siteUrl'] ?? ''
                ),
                'padding' => [50, 315, 50, 50],
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'maxFontSize' => 30,
            ]),
        ];
    }
}
