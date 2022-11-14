<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\events\ResolveFontCachePathEvent;
use alps\sharepreviews\events\ResolveSvgCachePathEvent;
use alps\sharepreviews\models\fonts\AbstractFontVariant;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\helpers\StringHelper;
use Imagine\Image\ImageInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use yii\base\Component;

class FileHandler extends Component
{
    const EVENT_RESOLVE_FONT_CACHE_PATH = 'resolveFontCachePath';

    const EVENT_RESOLVE_SVG_CACHE_PATH = 'resolveSvgCachePath';

    private Settings $settings;

    private array $fileCount = [];

    private ?string $fontCachePath = null;

    private ?string $svgCachePath = null;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->settings = SharePreviews::getInstance()->getSettings();
    }

    public function getNumberOfFilesAndDirectories(string $path): int
    {
        if (array_key_exists($path, $this->fileCount)) {
            return $this->fileCount[$path];
        }

        if (! is_dir($path)) {
            return $this->fileCount[$path] = 0;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::SELF_FIRST
        );

        return $this->fileCount[$path] = iterator_count($iterator);
    }

    public function fontExists(AbstractFontVariant $variant): bool
    {
        return file_exists($this->getFontPath($variant));
    }

    private function getFontCachePath(): string
    {
        if ($this->fontCachePath) {
            return $this->fontCachePath;
        }

        $event = new ResolveFontCachePathEvent;

        $this->trigger(self::EVENT_RESOLVE_FONT_CACHE_PATH, $event);

        return $this->fontCachePath = $event->path ?? Craft::$app->path->getRuntimePath() . '/share-previews/fonts';
    }

    public function getSvgCachePath(): string
    {
        if ($this->svgCachePath) {
            return $this->svgCachePath;
        }

        $event = new ResolveSvgCachePathEvent;

        $this->trigger(self::EVENT_RESOLVE_SVG_CACHE_PATH, $event);

        return $this->svgCachePath = $event->path ?? Craft::$app->path->getRuntimePath() . '/share-previews/svgs';
    }

    public function getFontPath(AbstractFontVariant $variant): string
    {
        $filename = StringHelper::slugify($variant->family->id . ' ' . $variant->id) . '.ttf';

        return $this->getFontCachePath() . '/' . $filename;
    }

    public function saveFont(AbstractFontVariant $variant, string $contents): self
    {
        $dir = $this->getFontCachePath();

        $this
            ->ensureDirectoryExists($dir)
            ->ensureGitIgnoreExists($dir);

        file_put_contents($this->getFontPath($variant), $contents);

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
