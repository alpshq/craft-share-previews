<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\fonts\AbstractFontFamily;
use alps\sharepreviews\models\fonts\AbstractFontVariant;
use alps\sharepreviews\models\fonts\CustomFontFamily;
use alps\sharepreviews\models\fonts\GoogleFontFamily;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\helpers\FileHelper;
use SplFileInfo;
use yii\base\Component;

class Fonts extends Component
{
    private ?array $fonts = null;

    private Helpers $helpers;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->helpers = SharePreviews::getInstance()->helpers;
    }

    /**
     * @return AbstractFontFamily[]
     */
    private function getFonts(): array
    {
        if ($this->fonts) {
            return $this->fonts;
        }

        return $this->fonts = array_merge(
            $this->getCustomFonts(),
            $this->getGoogleFonts()
        );
    }

    public function getPathToCustomFonts(): ?string
    {
        $customFontPath = SharePreviews::getInstance()->settings->customFontsPath;

        if (empty($customFontPath)) {
            return null;
        }

        return Craft::getAlias('@root/' . $customFontPath, false);
    }

    private function getCustomFonts(): array
    {
        $path = $this->getPathToCustomFonts();

        if (! $path) {
            return [];
        }

        if (! is_dir($path)) {
            return [];
        }

        $files = array_map(function (string $file) {
            return new SplFileInfo($file);
        }, FileHelper::findFiles($path));

        $grouped = [];
        foreach ($files as $file) {
            $ext = $file->getExtension();

            if (empty($ext)) {
                continue;
            }

            if (! array_key_exists($ext, $grouped)) {
                $grouped[$ext] = [];
            }

            $grouped[$ext][] = $file;
        }

        $filtered = [];
        $existing = [];

        foreach (['ttf', 'otf', 'woff2', 'woff'] as $type) {
            if (! array_key_exists($type, $grouped)) {
                continue;
            }

            foreach ($grouped[$type] as $file) {
                $name = strtolower($file->getBasename('.' . $file->getExtension()));

                if (in_array($name, $existing)) {
                    continue;
                }

                $existing[] = $name;
                $filtered[] = $file;
            }
        }

        return array_map(function (SplFileInfo $file) {
            return CustomFontFamily::fromFileInfo($file);
        }, $filtered);
    }

    private function getGoogleFonts(): array
    {
        $json = file_get_contents(__DIR__ . '/../../dist/fonts.json');

        $fonts = json_decode($json, true);

        return array_map(function (array $data) {
            return GoogleFontFamily::fromArray($data);
        }, $fonts);
    }

    public function getAvailableFontFamiliesAsOptions(): array
    {
        $families = [];

        foreach ($this->getFonts() as $font) {
            $type = $font->getTypeLabel();

            if (! array_key_exists($type, $families)) {
                $families[$type] = [];
            }

            $families[$type][] = [
                'value' => $font->id,
                'label' => $font->family,
            ];
        }

        if (count($families) === 1) {
            return $this->helpers->sortOptions(
                array_values($families)[0]
            );
        }

        $options = [];

        foreach ($families as $type => $fontOptions) {
            $options = array_merge(
                $options,
                [['optgroup' => $type]],
                $this->helpers->sortOptions($fontOptions)
            );
        }

        return $options;
    }

    public function getDefaultFontFamily(): AbstractFontFamily
    {
        return $this->getFontFamily('roboto') ?? $this->getFonts()[0];
    }

    public function getFontFamily(string $id): ?AbstractFontFamily
    {
        $fonts = array_filter($this->getFonts(), function (AbstractFontFamily $font) use ($id) {
            return $font->id === $id;
        });

        return array_values($fonts)[0] ?? null;
    }

    public function getVariantsForFamilyAsOptions(string $familyId): array
    {
        $font = $this->getFontFamily($familyId);

        if (! $font) {
            return [];
        }

        return array_map(function (AbstractFontVariant $variant) {
            return [
                'value' => $variant->id,
                'label' => $variant->getVariantLabel(),
                'default' => $variant->isDefault,
            ];
        }, $font->variants);
    }

    public function getAvailableVariantsAsOptions(): array
    {
        $hashmap = [];

        foreach ($this->getFonts() as $font) {
            $hashmap[$font->id] = array_map(function (AbstractFontVariant $variant) {
                return [
                    'value' => $variant->id,
                    'label' => $variant->getVariantLabel(),
                    'default' => $variant->isDefault,
                ];
            }, $font->variants);
        }

        return $hashmap;
    }
}
