<?php

namespace alps\sharepreviews;

use Imagine\Factory\ClassFactory;
use Imagine\Image\AbstractFont;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\PointInterface;
use InvalidArgumentException;
use RuntimeException;

class TextDrawer
{
    private $resource;

    private array $info;

    public function __construct($resource = null)
    {
        $this->resource = $resource;
        $this->loadGdInfo();
    }

    public function fontBox(AbstractFont $font, $string, $lineSpacing = 1.0, $angle = 0)
    {
        if (! function_exists('imageftbbox')) {
            throw new RuntimeException('GD must have been compiled with `--with-freetype-dir` option to use the Font feature.');
        }

        $fontfile = $font->getFile();

        if ($fontfile && DIRECTORY_SEPARATOR === '\\') {
            // On Windows imageftbbox() throws a "Could not find/open font" error if $fontfile is not an absolute path.
            $fontfileRealpath = realpath($fontfile);
            if ($fontfileRealpath !== false) {
                $fontfile = $fontfileRealpath;
            }
        }

        $angle = -1 * $angle;
        $info = imageftbbox($font->getSize(), $angle, $fontfile, $string, [
            'linespacing' => $lineSpacing,
        ]);
        $xs = [$info[0], $info[2], $info[4], $info[6]];
        $ys = [$info[1], $info[3], $info[5], $info[7]];
        $width = abs(max($xs) - min($xs));
        $height = abs(max($ys) - min($ys));

        return (new ClassFactory)->createBox($width, $height);
    }

    public function text($string, AbstractFont $font, PointInterface $position, $angle = 0, $width = null, $lineSpacing = 1.0)
    {
        if (! $this->info['FreeType Support']) {
            throw new RuntimeException('GD is not compiled with FreeType support');
        }

        $angle = -1 * $angle;
        $fontsize = $font->getSize();
        $fontfile = $font->getFile();
        $x = $position->getX();
        $y = $position->getY() + $fontsize;

        if ($width !== null) {
            $string = $font->wrapText($string, $width, $angle);
        }

        if (false === imagealphablending($this->resource, true)) {
            throw new RuntimeException('Font mask operation failed');
        }

        if ($fontfile && DIRECTORY_SEPARATOR === '\\') {
            // On Windows imagefttext() throws a "Could not find/open font" error if $fontfile is not an absolute path.
            $fontfileRealpath = realpath($fontfile);
            if ($fontfileRealpath !== false) {
                $fontfile = $fontfileRealpath;
            }
        }
        if (false === imagefttext($this->resource, $fontsize, $angle, $x, $y, $this->getColor($font->getColor()), $fontfile, $string, ['linespacing' => $lineSpacing])) {
            imagealphablending($this->resource, false);
            throw new RuntimeException('Font mask operation failed');
        }

        if (false === imagealphablending($this->resource, false)) {
            throw new RuntimeException('Font mask operation failed');
        }

        return $this;
    }

    /**
     * Generates a GD color from Color instance.
     *
     * @throws \Imagine\Exception\RuntimeException
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @return resource
     */
    private function getColor(ColorInterface $color)
    {
        if (! $color instanceof RGBColor) {
            throw new InvalidArgumentException('GD driver only supports RGB colors');
        }

        $gdColor = imagecolorallocatealpha($this->resource, $color->getRed(), $color->getGreen(), $color->getBlue(), (100 - $color->getAlpha()) * 127 / 100);
        if (false === $gdColor) {
            throw new RuntimeException(sprintf('Unable to allocate color "RGB(%s, %s, %s)" with transparency of %d percent', $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()));
        }

        return $gdColor;
    }

    private function loadGdInfo()
    {
        if (! function_exists('gd_info')) {
            throw new RuntimeException('Gd not installed');
        }

        $this->info = gd_info();
    }
}
