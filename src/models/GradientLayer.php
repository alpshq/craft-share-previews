<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\behaviors\HasColors;
use Craft;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Fill\Gradient\Horizontal;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

/**
 * @property array $from
 * @property array $to
 */
class GradientLayer extends AbstractRectangleLayer
{
    public int $angle = 0;

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Gradient Layer');
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        $angle = $this->angle;
        $from = $this->toColor($this->from);
        $to = $this->toColor($this->to);

        $width = $this->width - $this->paddingLeft - $this->paddingRight;
        $height = $this->height - $this->paddingTop - $this->paddingBottom;

        $transparent = $this->toColor([0,0,0,0]);

        $gradientImage = (new Imagine)
            ->create(new Box($width + 2, $height + 2), $transparent);

        if ($angle !== 0) {
            $gradientImage->rotate($angle * -1);
        }

        $fill = new Horizontal($gradientImage->getSize()->getWidth(), $from, $to);

        $gradientImage->fill($fill);

        if ($angle !== 0) {
            $gradientImage->rotate($angle);
        }

        $size = $gradientImage->getSize();
        $x = $size->getWidth() / 2 - ($width / 2);
        $y = $size->getHeight() / 2 - ($height / 2);
        $gradientImage->crop(new Point($x, $y), new Box($width, $height));

        return $image->paste($gradientImage, new Point($this->paddingLeft, $this->paddingTop));
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getColorAttributes(),
        );
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            ['class' => HasColors::class, 'properties' => ['from', 'to']],
        ]);
    }
}