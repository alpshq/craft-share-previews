<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\SharePreviews;
use Craft;
use craft\elements\Entry;
use Imagine\Gd\Font;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Point;
use Laminas\Feed\Reader\Entry\EntryInterface;

class TextLayer extends AbstractRectangleLayer
{
    public string $content;
    public string $fontFamily = 'Roboto';
    public string $fontWeight = '700';
    public array $color = [50,50,50];
    public int $maxFontSize = 60;
    public bool $shrinkToFit = true;

    public function apply(ImageInterface $image): ImageInterface
    {
        if (empty($this->content)) {
            return $image;
        }

        [$font, $content] = $this->getFont();

        $box = $font->box($content);

        $point = $this->getAlignedOriginPoint($box->getWidth(), $box->getHeight());

        $image->draw()->text($content, $font, $point);

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

        if ($shrinkToFit) {
            $maxFontSize += 5;

            do {
                $maxFontSize -= 5;
                $font = new Font($fontFile, $maxFontSize, $color);
                $box = $font->box(wordwrap($content, 1));

                if ($maxFontSize <= 10) {
                    break;
                }
            } while ($box->getWidth() > $maxWidth);
        }

        $maxFontSize += 5;

        do {
            $maxFontSize -= 5;

            $font = new Font($fontFile, $maxFontSize, $color);

            $wrapAfter = 110;

            do {
                $wrapAfter -= 10;
                $box = $font->box(wordwrap($content, $wrapAfter));

                if ($wrapAfter <= 10) {
                    break;
                }

            } while ($box->getWidth() > $maxWidth);

            if (!$shrinkToFit || $maxFontSize <= 10) {
                break;
            }

        } while ($box->getHeight() > $maxHeight);

        return [$font, wordwrap($content, $wrapAfter)];
    }

    private function getFontFile(): string
    {
        $plugin = SharePreviews::getInstance();

        $fileHandler = $plugin->fileHandler;
        $fontFetcher = $plugin->fontFetcher;

        $family = $this->fontFamily;
        $variant = $this->fontWeight;

        if ($fileHandler->fontExists($family, $variant)) {
            return $fileHandler->getFontPath($family, $variant);
        }

        $contents = $fontFetcher->fetch($family, $variant);

        return $fileHandler
            ->saveFont($family, $variant, $contents)
            ->getFontPath($family, $variant);
    }

    public function willRender(Entry $entry)
    {
        $this->content = Craft::$app->getView()->renderString($this->content, [
            'entry' => $entry,
        ]);
    }
}