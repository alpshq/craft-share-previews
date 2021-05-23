<?php

namespace alps\sharepreviews\twig;

use craft\elements\Entry;
use alps\sharepreviews\Plugin;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PreviewExtension extends \Twig\Extension\AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            // Use a closure or define the function outside with [$this, 'nameOfTheFunction'].
            new TwigFunction('preview_image_src', [$this, 'getPreviewImageSource'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('rgb_to_hex', [$this, 'rgbToHex']),
            new TwigFilter('rgba_to_opacity', [$this, 'rgbaToOpacity']),
        ];
    }

    public function rgbToHex(array $rgb): string
    {
        return sprintf(
            '#%02x%02x%02x',
            $rgb[0],
            $rgb[1],
            $rgb[2],
        );
    }

    public function rgbaToOpacity(array $rgba): int
    {
        return ($rgba[3] ?? 1) * 100;
    }

    public function getPreviewImageSource(Entry $entry): ?string
    {
        return Plugin::getInstance()
            ->images
            ->getShareImagePreviewUrl($entry);
    }
}
