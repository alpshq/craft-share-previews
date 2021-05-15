<?php


namespace alps\sharepreviews\models;

use craft\base\Model;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

abstract class AbstractAlignableLayer extends AbstractLayer
{
    const HORIZONTAL_ALIGN_LEFT = 'hl';
    const HORIZONTAL_ALIGN_RIGHT = 'hr';
    const HORIZONTAL_ALIGN_CENTER = 'hc';

    const VERTICAL_ALIGN_TOP = 'vt';
    const VERTICAL_ALIGN_BOTTOM = 'vb';
    const VERTICAL_ALIGN_MIDDLE = 'vm';

    public string $horizontalAlign = self::HORIZONTAL_ALIGN_CENTER;
    public string $verticalAlign = self::VERTICAL_ALIGN_MIDDLE;

    protected function getAlignedOriginPoint(int $width, int $height): PointInterface
    {
        [$maxWidth, $maxHeight] = $this->getCanvasDimensions();

        if ($this->horizontalAlign === self::HORIZONTAL_ALIGN_LEFT) {
            $x = $this->paddingLeft;
        } elseif ($this->horizontalAlign === self::HORIZONTAL_ALIGN_RIGHT) {
            $x = $this->width - $width - $this->paddingRight;
        } else {
            $x = ($maxWidth - $width) / 2 + $this->paddingLeft;
        }

        if ($this->verticalAlign === self::VERTICAL_ALIGN_TOP) {
            $y = $this->paddingTop;
        } elseif ($this->verticalAlign === self::VERTICAL_ALIGN_BOTTOM) {
            $y = $this->height - $height - $this->paddingBottom;
        } else {
            $y = ($maxHeight - $height) / 2 + $this->paddingTop;
        }

        return new Point($x, $y);
    }
}