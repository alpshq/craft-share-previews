<?php

namespace alps\sharepreviews\twig;

use craft\elements\Entry;
use alps\sharepreviews\Plugin;
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

    public function getPreviewImageSource(Entry $entry): ?string
    {
        return Plugin::getInstance()
            ->images
            ->getShareImagePreviewUrl($entry);
    }
}
