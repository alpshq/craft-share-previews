<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\Config;
use Imagine\Filter\Advanced\Border;
use Imagine\Filter\Transformation;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class ImageLayer extends AbstractAlignableLayer
{
    public string $path;

    public int $borderWidth = 0;
    public array $borderColor = [255,255,255];

    public function apply(ImageInterface $image): ImageInterface
    {
        [$maxWidth, $maxHeight] = $this->getCanvasDimensions();

        $openedImage = (new Imagine)
            ->open($this->path)
            ->thumbnail(new Box($maxWidth, $maxHeight));

        if ($this->borderWidth > 0) {
            (new Transformation)->applyFilter(
                $openedImage,
                new Border($this->toColor($this->borderColor), $this->borderWidth, $this->borderWidth)
            );
        }

        $size = $openedImage->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        return $image
            ->paste($openedImage, $this->getAlignedOriginPoint($width, $height));
    }
}