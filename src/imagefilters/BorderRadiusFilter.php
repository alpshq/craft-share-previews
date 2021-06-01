<?php

namespace alps\sharepreviews\imagefilters;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\Point;

class BorderRadiusFilter implements \Imagine\Filter\FilterInterface
{
    private ImagineInterface $imagine;

    private int $radius;

    public function __construct(ImagineInterface $imagine, int $radius)
    {
        $this->imagine = $imagine;
        $this->radius = $radius;
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        $size = $image->getSize();

        $width = $size->getWidth();
        $height = $size->getHeight();

        $mask = $this->imagine->create($size);

        $black = $image->palette()->color([0, 0, 0]);

        $radius = $this->radius * 2;

        $circles = [
            [$radius - 1, $radius - 1],
            [$width - $radius, $radius + 1],
            [$radius - 1, $height - $radius],
            [$width - $radius, $height - $radius],
        ];

        foreach ($circles as $coords) {
            $mask->draw()->circle(
                $this->getPoint(...$coords),
                $radius,
                $black->lighten(180),
                true,
                0,
            );
        }

        $mask->effects()->blur($radius / 2);

        $circles = [
            [$radius, $radius],
            [$width - $radius - 1, $radius],
            [$radius, $height - $radius - 1],
            [$width - $radius - 1, $height - $radius - 1],
        ];

        foreach ($circles as $coords) {
            $mask->draw()->circle(
                $this->getPoint(...$coords),
                $radius,
                $black,
                true,
                0,
            );
        }

        $borders = [
            [new Point(0, $radius), new Point($width, $height - $radius)],
            [new Point($radius, 0), new Point($width - $radius, $height)],
        ];

        foreach ($borders as [$from, $to]) {
            $mask->draw()->rectangle($from, $to, $black, true, 0);
        }

        return $this->applyMask($image, $mask);
    }

    private function getPoint(int $x, int $y): Point
    {
        if ($x < 0) {
            $x = 0;
        }

        if ($y < 0) {
            $y = 0;
        }

        return new Point($x, $y);
    }

    private function applyMask(ImageInterface $image, ImageInterface $mask)
    {
        if (! $mask instanceof $image) {
            throw new InvalidArgumentException('Cannot mask non-gd images');
        }

        $size = $image->getSize();
        $maskSize = $mask->getSize();

        if ($size != $maskSize) {
            throw new InvalidArgumentException(sprintf('The given mask doesn\'t match current image\'s size, Current mask\'s dimensions are %s, while image\'s dimensions are %s', $maskSize, $size));
        }

        for ($x = 0, $width = $size->getWidth(); $x < $width; ++$x) {
            for ($y = 0, $height = $size->getHeight(); $y < $height; ++$y) {
                $position = new Point($x, $y);
                $color = $image->getColorAt($position);
                $maskColor = $mask->getColorAt($position);
                $delta = (int) round($color->getAlpha() * $maskColor->getRed() / 255) * -1;

                if (false === imagesetpixel($image->getGdResource(), $x, $y, $this->getColor($image, $color->dissolve($delta)))) {
                    throw new RuntimeException('Apply mask operation failed');
                }
            }
        }

        return $image;
    }

    private function getColor(ImageInterface $image, ColorInterface $color)
    {
        if (! $color instanceof RGBColor) {
            throw new InvalidArgumentException('GD driver only supports RGB colors');
        }

        $index = imagecolorallocatealpha($image->getGdResource(), $color->getRed(), $color->getGreen(), $color->getBlue(), round(127 * (100 - $color->getAlpha()) / 100));

        if (false === $index) {
            throw new RuntimeException(sprintf('Unable to allocate color "RGB(%s, %s, %s)" with transparency of %d percent', $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()));
        }

        return $index;
    }
}
