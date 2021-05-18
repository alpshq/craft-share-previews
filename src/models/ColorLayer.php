<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\imagefilters\BorderRadiusFilter;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Fill\Gradient\Vertical;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\Point;

class ColorLayer extends AbstractRectangleLayer
{
    public array $color = [0,0,0];
    public int $borderRadius = 0;

    public function apply(ImageInterface $image): ImageInterface
    {
        [$width, $height] = $this->getCanvasDimensions();

        $rect = (new Imagine)->create(
            new Box($width, $height), $this->toColor([0,0,0,0])
        );

        $rect->draw()->rectangle(
            new Point(0, 0),
            new Point($width, $height),
            $this->toColor($this->color),
            true,
            0
        );

        if ($this->borderRadius > 0) {
            $rect = (new BorderRadiusFilter(new Imagine, $this->borderRadius))->apply($rect);
        }

        return $image
            ->paste($rect, $this->getAlignedOriginPoint($width, $height));
    }

    protected function getScalableProperties(): array
    {
        return array_merge(parent::getScalableProperties(), [
            'borderRadius' => 'width',
        ]);
    }
}