<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\helpers\StringHelper;
use Imagine\Image\ImageInterface;
use yii\base\Component;

class FileHandler extends Component
{
    private Settings $settings;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->settings = SharePreviews::getInstance()->getSettings();
    }

    public function fontExists(string $familyId, string $variantId): bool
    {
        return file_exists($this->getFontPath($familyId, $variantId));
    }

    public function getFontPath(string $familyId, string $variantId): string
    {
        $filename = StringHelper::slugify($familyId . ' ' . $variantId) . '.ttf';

        return $this->settings->fontCachePath . '/' . $filename;
    }

    public function saveFont(string $familyId, string $variantId, string $contents): self
    {
        $dir = $this->settings->fontCachePath;

        $this
            ->ensureDirectoryExists($dir)
            ->ensureGitIgnoreExists($dir);

        file_put_contents($this->getFontPath($familyId, $variantId), $contents);

        return $this;
    }

    private function ensureDirectoryExists(string $dir): self
    {
        if (is_dir($dir)) {
            return $this;
        }

        mkdir($dir, 0777, true);

        return $this;
    }

    private function ensureGitIgnoreExists(string $dir): self
    {
        $ensure = $this->settings->ensureGitignoreExists;

        if (! $ensure) {
            return $this;
        }

        $filename = $dir . '/.gitignore';

        if (file_exists($filename)) {
            return $this;
        }

        file_put_contents($filename, implode("\n", [
            '*',
            '!.gitignore',
            '',
        ]));

        return $this;
    }

    private function getPublicPath(string $relativeToPublic): string
    {
        $publicPath = rtrim(Craft::getAlias('@webroot'), '/');

        return $publicPath . '/' . ltrim($relativeToPublic, '/');
    }

    public function getImageDirectory(): string
    {
        return $this->getPublicPath(
            $this->settings->routePrefix
        );
    }

    private function getImagePath(string $data): string
    {
        return $this->getImageDirectory() . '/' . $data . '.png';
    }

    public function saveImage(ImageInterface $image, string $data): self
    {
        $routePath = $this->settings->routePrefix;

        $dir = $this->getPublicPath($routePath);

        $this
            ->ensureDirectoryExists($dir)
            ->ensureGitIgnoreExists($dir);

        $filename = $this->getImagePath($data);

        $directories = explode('/', $filename);
        array_pop($directories);

        $directories = implode('/', $directories);

        $this->ensureDirectoryExists($directories);

        $image->save($filename, [
            'png_compression_level' => Image::PNG_COMPRESSION_LEVEL,
        ]);

        return $this;
    }
}
