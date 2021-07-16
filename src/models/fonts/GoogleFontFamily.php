<?php

namespace alps\sharepreviews\models\fonts;

use Craft;

class GoogleFontFamily extends \alps\sharepreviews\models\fonts\AbstractFontFamily
{
    public function getTypeLabel(): string
    {
        return Craft::t('share-previews', 'Google Fonts');
    }

    public static function fromArray(array $data): self
    {
        $variants = array_map(function (array $variantData) use ($data) {
            return new GoogleFontVariant([
                'id' => $variantData['id'],
                'style' => $variantData['style'],
                'weight' => $variantData['weight'],
                'url' => $variantData['ttf'],
                'isDefault' => $variantData['id'] === $data['defaultVariant'],
            ]);
        }, $data['variants']);

        return new self([
            'id' => $data['id'],
            'family' => $data['family'],
            'variants' => $variants,
        ]);
    }
}
