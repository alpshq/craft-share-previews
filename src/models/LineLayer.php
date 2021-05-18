<?php

namespace alps\sharepreviews\models;

use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class LineLayer extends AbstractLayer
{
    public array $color = [0,0,0];
    public array $from = [0,0];
    public array $to = [1200,630];

    public function apply(ImageInterface $image): ImageInterface
    {
        $from = new Point(...$this->from);
        $to = new Point(...$this->to);

        $image->draw()->rectangle($from, $to, $this->toColor($this->color), true);

        return $image;
    }

    public function scaleTo(int $width, int $height): AbstractLayer
    {
        $currentWidth = $this->width;
        $currentHeight = $this->height;

        $this->from = [
            $this->scaleProperty($currentWidth, $width, $this->from[0]),
            $this->scaleProperty($currentHeight, $height, $this->from[1]),
        ];

        $this->to = [
            $this->scaleProperty($currentWidth, $width, $this->to[0]),
            $this->scaleProperty($currentHeight, $height, $this->to[1]),
        ];

        return parent::scaleTo($width, $height);
    }
}