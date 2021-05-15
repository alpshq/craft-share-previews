<?php

namespace alps\sharepreviews\models;

use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class ColorLayer extends AbstractLayer
{
    public array $color = [0,0,0];

    public function apply(ImageInterface $image): ImageInterface
    {
        $leftTop = new Point($this->paddingLeft, $this->paddingTop);
        $rightBottom = new Point(
            $this->width - $this->paddingRight,
            $this->height - $this->paddingBottom,
        );

        $image->draw()->rectangle(
            $leftTop,
            $rightBottom,
            $this->toColor($this->color),
            true,
            0
        );

        return $image;
    }
}