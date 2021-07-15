<?php

namespace alps\sharepreviews\models\fonts;

use craft\base\Model;
use InvalidArgumentException;

/**
 * @property string                                               $typeLabel
 * @property \alps\sharepreviews\models\fonts\AbstractFontVariant $defaultVariant
 * @property AbstractFontVariant[]                                $variants
 */
abstract class AbstractFontFamily extends Model
{
    public string $id;

    public string $family;

    /** @var AbstractFontVariant[] */
    private array $variants = [];

    abstract public function getTypeLabel(): string;

    public function getDefaultVariant(): AbstractFontVariant
    {
        $variants = array_filter($this->variants, function (AbstractFontVariant $variant) {
            return $variant->isDefault;
        });

        return array_values($variants)[0] ?? $this->variants[0];
    }

    public function hasVariant(string $variantId): bool
    {
        foreach ($this->variants as $variant) {
            if ($variant->id === $variantId) {
                return true;
            }
        }

        return false;
    }

    public function getVariant(string $variantId): AbstractFontVariant
    {
        foreach ($this->variants as $variant) {
            if ($variant->id === $variantId) {
                return $variant;
            }
        }

        $message = sprintf(
            'Variant [%s] not found in family [%s].',
            $variantId,
            $this->family
        );

        throw new InvalidArgumentException($message);
    }

    public function setVariants(array $variants): self
    {
        $variants = array_values($variants);
        $variants = array_map(function (AbstractFontVariant $variant) {
            $variant->family = $this;

            return $variant;
        }, $variants);

        $this->variants = $variants;

        return $this;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }
}
