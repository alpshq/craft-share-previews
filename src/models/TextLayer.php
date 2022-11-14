<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\behaviors\HasColors;
use alps\sharepreviews\SharePreviews;
use alps\sharepreviews\TextDrawer;
use Craft;
use Imagine\Gd\Font;
use Imagine\Image\AbstractFont;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\PointInterface;

class TextLayer extends AbstractRectangleLayer
{
    public string $content = '{{ entry.title }}';

    public string $fontFamily = 'roboto';

    public string $fontVariant = 'regular';

    public int $maxFontSize = 60;

    public bool $shrinkToFit = true;

    public int $lineHeight = 100;

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Text');
    }

    public function isAvailableInTemplateEditor(): bool
    {
        return true;
    }

    public function setFontFamilyWithVariant(array $familyWithVariant): self
    {
        $familyWithVariant[1] = $familyWithVariant[1] ?? $this->fontVariant;

        [$familyId, $variantId] = $familyWithVariant;

        $fontsService = SharePreviews::getInstance()->fonts;

        $family = $fontsService->getFontFamily($familyId) ?? $fontsService->getDefaultFontFamily();

        $variant = $family->hasVariant($variantId)
            ? $family->getVariant($variantId)
            : $family->getDefaultVariant();

        $this->fontFamily = $variant->family->id;
        $this->fontVariant = $variant->id;

        return $this;
    }

    private function isDefaultLineHeight(): bool
    {
        return $this->lineHeight === 100;
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        if (empty($this->content)) {
            return $image;
        }

        [$font, $content] = $this->getFont();

        $box = $this->isDefaultLineHeight()
            ? $font->box($content)
            : (new TextDrawer)->fontBox($font, $content, $this->lineHeight / 100);

        $point = $this->getAlignedOriginPoint($box->getWidth(), $box->getHeight());

        if ($this->isDefaultLineHeight()) {
            $image->draw()->text($content, $font, $point);

            return $image;
        }

        $drawer = new TextDrawer($image->getGdResource());
        $drawer->text($content, $font, $point, 0, null, $this->lineHeight / 100);

        return $image;
    }

    private function getFont(): array
    {
        [$maxWidth, $maxHeight] = $this->getCanvasDimensions();

        $fontFile = $this->getFontFile();
        $content = $this->content;
        $maxFontSize = $this->maxFontSize;
        $color = $this->toColor($this->color);
        $shrinkToFit = $this->shrinkToFit;

        $customDrawer = new TextDrawer;

        $lineSpacing = $this->lineHeight / 100;
        if ($shrinkToFit) {
            $maxFontSize += 1;

            do {
                $maxFontSize -= 1;
                $font = new Font($fontFile, $maxFontSize, $color);
                $wrappedContent = wordwrap($content, 1);

                $box = $this->isDefaultLineHeight()
                    ? $font->box($wrappedContent)
                    : $customDrawer->fontBox($font, $wrappedContent, $lineSpacing);

                if ($maxFontSize <= 10) {
                    break;
                }
            } while ($box->getWidth() > $maxWidth);
        }

        $maxFontSize += 1;

        do {
            $maxFontSize -= 1;

            $font = new Font($fontFile, $maxFontSize, $color);

            $wrapAfter = 110;

            do {
                $wrapAfter -= 3;
                $wrappedContent = wordwrap($content, $wrapAfter);

                $box = $this->isDefaultLineHeight()
                    ? $font->box($wrappedContent)
                    : $customDrawer->fontBox($font, $wrappedContent, $lineSpacing);

                if ($wrapAfter <= 10) {
                    break;
                }
            } while ($box->getWidth() > $maxWidth);

            if (! $shrinkToFit || $maxFontSize <= 10) {
                break;
            }
        } while ($box->getHeight() > $maxHeight);

        return [$font, wordwrap($content, $wrapAfter)];
    }

    private function getFontFile(): string
    {
        $fonts = SharePreviews::getInstance()->fonts;

        $family = $fonts->getFontFamily($this->fontFamily) ?? $fonts->getDefaultFontFamily();

        return $family
            ->getVariant($this->fontVariant)
            ->getPathToFontFile();
    }

    protected function getPropertiesWithVariables(): array
    {
        return [
            'content',
        ];
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getColorAttributes(),
        );
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            ['class' => HasColors::class, 'properties' => ['color']],
        ]);
    }
}
