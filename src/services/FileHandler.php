<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\SharePreviews;
use craft\helpers\StringHelper;
use Imagine\Image\ImageInterface;
use alps\sharepreviews\Config;
use alps\sharepreviews\models\Settings;
use yii\base\Component;

class FileHandler extends Component
{
    private Settings $settings;
    private Config $config;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->config = new Config;
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

        if (!$ensure) {
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

    public function imageExists(string $data): bool
    {
        return file_exists($this->getImagePath($data));
    }

    private function getImagePath(string $data): string
    {
        return $this->config->getPublicPath(
            $this->settings->routePrefix . '/' . $data . '.png'
        );
    }

    public function saveImage(ImageInterface $image, string $data): self
    {
        $routePath = $this->settings->routePrefix;

        $dir = $this->config->getPublicPath($routePath);

        $this
            ->ensureDirectoryExists($dir)
            ->ensureGitIgnoreExists($dir);

        $filename = $this->getImagePath($data);

        $directories = explode('/', $filename);
        array_pop($directories);

        $directories = implode('/', $directories);

        $this->ensureDirectoryExists($directories);

        $image->save($filename, [
            'png_compression_level' => Renderer::PNG_COMPRESSION_LEVEL,
        ]);

        return $this;
    }
}
