<?php

namespace alps\sharepreviews\models\fonts;

use Craft;
use craft\helpers\StringHelper;
use SplFileInfo;

class CustomFontFamily extends \alps\sharepreviews\models\fonts\AbstractFontFamily
{
    public function getTypeLabel(): string
    {
        return Craft::t('share-previews', 'Custom Fonts');
    }

    public static function fromFileInfo(SplFileInfo $fileInfo): self
    {
        $name = $fileInfo->getBasename('.' . $fileInfo->getExtension());
        $id = StringHelper::slugify($name);

        return new self([
            'id' => $id,
            'family' => $name,
            'variants' => [
                new CustomFontVariant([
                    'id' => 'regular',
                    'style' => 'normal',
                    'weight' => AbstractFontVariant::WEIGHT_REGULAR,
                    'filepath' => $fileInfo->getPathname(),
                    'isDefault' => true,
                ]),
            ],
        ]);
    }
}
