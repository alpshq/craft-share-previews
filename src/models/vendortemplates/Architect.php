<?php

namespace alps\sharepreviews\models\vendortemplates;

use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;

class Architect extends AbstractVendorTemplate
{
    protected function getLayerConfig(array $vars = []): array
    {
        $verticalLines = [];
        $horizontalLines = [];

        $gap = 30;

        for ($i = $gap; $i < 1200; $i = $i + $gap) {
            $verticalLines[] = new LineLayer([
                'lineType' => LineLayer::LINE_TYPE_VERTICAL,
                'color' => '#CBD5E1',
                'x' => $i,
                'y' => 0,
                'length' => 630,
            ]);
        }

        for ($i = $gap; $i < 630; $i = $i + $gap) {
            $horizontalLines[] = new LineLayer([
                'color' => '#CBD5E1',
                'x' => 0,
                'y' => $i,
                'length' => 1200,
            ]);
        }

        return [
            ...$verticalLines,
            ...$horizontalLines,
            new LineLayer([
                'color' => '#64748B',
                'x' => 0,
                'y' => $gap * 3,
                'length' => 1200,
            ]),
            new LineLayer([
                'color' => '#64748B',
                'x' => 0,
                'y' => 630 - $gap * 3,
                'length' => 1200,
            ]),
            new LineLayer([
                'color' => '#64748B',
                'lineType' => LineLayer::LINE_TYPE_VERTICAL,
                'x' => $gap * 3,
                'y' => 0,
                'length' => 630,
            ]),
            new LineLayer([
                'color' => '#64748B',
                'lineType' => LineLayer::LINE_TYPE_VERTICAL,
                'x' => 1200 - $gap * 3,
                'y' => 0,
                'length' => 630,
            ]),
            new TextLayer([
                'color' => '#64748B',
                'content' => strtoupper($vars['siteName'] ?? ''),
                'padding' => [
                    $gap * 4,
                    $gap * 18,
                    $gap * 20,
                    $gap * 0,
                ],
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'maxFontSize' => 30,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
            ]),
            new TextLayer([
                'color' => '#64748B',
                'content' => $vars['siteUrl'] ?? '',
                'padding' => [
                    $gap * 20,
                    $gap * 18,
                    $gap * 4,
                    $gap * 0,
                ],
                'fontFamilyWithVariant' => ['roboto-condensed', '400'],
                'maxFontSize' => 20,
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new TextLayer([
                'color' => '#334155',
                'content' => '{{ entry.title }}',
                'padding' => $gap * 6,
                'fontFamilyWithVariant' => ['roboto', '900'],
            ]),
        ];
    }
}
