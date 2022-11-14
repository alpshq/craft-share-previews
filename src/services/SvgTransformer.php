<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\SharePreviews;
use craft\elements\Asset;
use craft\helpers\StringHelper;
use RuntimeException;
use yii\base\Component;

class SvgTransformer extends Component
{
    private ?FileHandler $fileHandler = null;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->fileHandler = SharePreviews::getInstance()->fileHandler;
    }

    public function getPathToTransformedSvg(Asset $asset): string
    {
        $path = $this->getPath($asset);

        if (! file_exists($path)) {
            $this->transform($asset);
        }

        return $path;
    }

    private function getPath(Asset $asset): string
    {
        $updateDate = $asset->dateUpdated ?? $asset->dateModified ?? $asset->dateCreated;
        $timestamp = $updateDate?->getTimestamp() ?? 0;

        $filename = StringHelper::slugify($asset->id . ' ' . $timestamp) . '.png';

        return $this->fileHandler->getSvgCachePath() . '/' . $filename;
    }

    private function transform(Asset $asset): self
    {
        if (! $this->isInkscapeAvailable()) {
            throw new RuntimeException('Inkscape not available. It is required to handle SVG files.');
        }

        $dir = $this->fileHandler->getSvgCachePath();

        $this
            ->fileHandler
            ->ensureDirectoryExists($dir)
            ->ensureGitIgnoreExists($dir);

        $command = sprintf(
            'inkscape -w %d %s -o %s',
            2460,
            escapeshellarg($asset->getImageTransformSourcePath()),
            escapeshellarg($this->getPath($asset)),
        );

        system($command, $output);

        if ($output === 0) {
            return $this;
        }

        throw new RuntimeException("SVG transform failed.\n\n" . $output);
    }

    private function isInkscapeAvailable(): bool
    {
        system('which inkscape > /dev/null', $output);

        return $output === 0;
    }
}
