<?php


namespace alps\sharepreviews\services;


use alps\sharepreviews\SharePreviews;
use Craft;
use craft\helpers\StringHelper;
use GuzzleHttp\Client;
use RuntimeException;
use yii\base\Component;

class Fonts extends Component
{
    private Client $client;
    private ?array $fonts = null;
    private Helpers $helpers;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->client = new Client([
            'http_errors' => true,
        ]);

        $this->helpers = SharePreviews::getInstance()->helpers;
    }

    private function getFonts(): array
    {
        if ($this->fonts) {
            return $this->fonts;
        }

        $json = file_get_contents(__DIR__ . '/../../dist/fonts.json');

        return $this->fonts = json_decode($json, true);
    }

    public function fetch(string $familyId, string $variantId): string
    {
        $data = $this->getFonts();

        $font = collect($data)->first(function(array $font) use ($familyId) {
            return $font['id'] === $familyId;
        });

        $variant = collect($font['variants'])->first(function(array $variant) use ($variantId) {
            return $variant['id'] === $variantId;
        });

        return file_get_contents($variant['ttf']);
    }

    public function isValidVariant(string $familyId, string $variantId): bool
    {
        $font = $this->getFontFamily($familyId);

        if (!$font) {
            return false;
        }

        $variant = array_filter($font['variants'], function(array $variant) use ($variantId) {
            return $variant['id'] === $variantId;
        });

        return count($variant) > 0;
    }

    public function isValidFamily(string $familyId): bool
    {
        return $this->getFontFamily($familyId) !== null;
    }

    public function getDefaults(string $familyId = null): array
    {
        if ($familyId === null || $font = $this->getFontFamily($familyId) === null) {
            return ['roboto', 'regular'];
        }

        return [$familyId, $font['defaultVariant']];
    }

    public function getAvailableFontFamiliesAsOptions(): array
    {
        $families = array_map(function(array $font) {
            return [
                'value' => $font['id'],
                'label' => $font['family'],
            ];
        }, $this->getFonts());

        return $this->helpers->sortOptions($families);
    }

    private function getFontFamily(string $id): ?array
    {
        $fonts = array_filter($this->getFonts(), function(array $font) use ($id) {
            return $font['id'] === $id;
        });

        return array_values($fonts)[0] ?? null;
    }

    public function getVariantsForFamilyAsOptions(string $familyId): array
    {
        $font = $this->getFontFamily($familyId);

        if (!$font) {
            return [];
        }

        return array_map(function(array $variant) use ($font) {
            return [
                'value' => $variant['id'],
                'label' => $this->getVariantLabel($variant),
                'default' => $variant['id'] === $font['defaultVariant'],
            ];
        }, $font['variants']);
    }

    public function getAvailableVariantsAsOptions(): array
    {
        $hashmap = [];

        foreach ($this->getFonts() as $font) {
            $variants = array_map(function(array $variant) use ($font) {
                return [
                    'value' => $variant['id'],
                    'label' => $this->getVariantLabel($variant),
                    'default' => $variant['id'] === $font['defaultVariant'],
                ];
            }, $font['variants']);

            $hashmap[$font['id']] = $variants;
        }

        return $hashmap;
    }

    private function getVariantLabel(array $variant): string
    {
        ['weight' => $weight, 'style' => $style] = $variant;

        $label = [];

        switch ($weight) {
            case '100':
                $label[]= Craft::t('share-previews', 'Thin');
                break;
            case '200':
                $label[]= Craft::t('share-previews', 'Ultra Light');
                break;
            case '300':
                $label[]= Craft::t('share-previews', 'Light');
                break;
            case '400':
                $label[]= Craft::t('share-previews', 'Regular');
                break;
            case '500':
                $label[]= Craft::t('share-previews', 'Medium');
                break;
            case '600':
                $label[]= Craft::t('share-previews', 'Semi Bold');
                break;
            case '700':
                $label[]= Craft::t('share-previews', 'Bold');
                break;
            case '800':
                $label[]= Craft::t('share-previews', 'Extra Bold');
                break;
            case '900':
                $label[]= Craft::t('share-previews', 'Black');
                break;
        }

        if ($style === 'italic') {
            $label[]= Craft::t('share-previews', 'Italic');
        }

        return implode(' ', $label);
    }
}
