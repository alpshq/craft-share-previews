<?php

namespace alps\sharepreviews\models\fonts;

use Craft;
use craft\base\Model;

/**
 * @property string $variantLabel
 * @property string $pathToFontFile
 */
abstract class AbstractFontVariant extends Model
{
    const WEIGHT_THIN = '100';

    const WEIGHT_ULTRA_LIGHT = '200';

    const WEIGHT_LIGHT = '300';

    const WEIGHT_REGULAR = '400';

    const WEIGHT_MEDIUM = '500';

    const WEIGHT_SEMI_BOLD = '600';

    const WEIGHT_BOLD = '700';

    const WEIGHT_EXTRA_BOLD = '800';

    const WEIGHT_BLACK = '900';

    public AbstractFontFamily $family;

    public string $id;

    public string $style;

    public string $weight;

    public bool $isDefault = false;

    abstract public function getPathToFontFile(): string;

    public function getVariantLabel(): string
    {
        $label = [];

        switch ($this->weight) {
            case self::WEIGHT_THIN:
                $label[] = Craft::t('share-previews', 'Thin');
                break;
            case self::WEIGHT_ULTRA_LIGHT:
                $label[] = Craft::t('share-previews', 'Ultra Light');
                break;
            case self::WEIGHT_LIGHT:
                $label[] = Craft::t('share-previews', 'Light');
                break;
            case self::WEIGHT_REGULAR:
                $label[] = Craft::t('share-previews', 'Regular');
                break;
            case self::WEIGHT_MEDIUM:
                $label[] = Craft::t('share-previews', 'Medium');
                break;
            case self::WEIGHT_SEMI_BOLD:
                $label[] = Craft::t('share-previews', 'Semi Bold');
                break;
            case self::WEIGHT_BOLD:
                $label[] = Craft::t('share-previews', 'Bold');
                break;
            case self::WEIGHT_EXTRA_BOLD:
                $label[] = Craft::t('share-previews', 'Extra Bold');
                break;
            case self::WEIGHT_BLACK:
                $label[] = Craft::t('share-previews', 'Black');
                break;
        }

        if ($this->style === 'italic') {
            $label[] = Craft::t('share-previews', 'Italic');
        }

        return implode(' ', $label);
    }
}
