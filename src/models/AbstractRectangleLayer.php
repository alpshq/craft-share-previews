<?php


namespace alps\sharepreviews\models;

use craft\base\Model;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

abstract class AbstractRectangleLayer extends AbstractLayer
{
    const HORIZONTAL_ALIGN_LEFT = 'hl';
    const HORIZONTAL_ALIGN_RIGHT = 'hr';
    const HORIZONTAL_ALIGN_CENTER = 'hc';

    const VERTICAL_ALIGN_TOP = 'vt';
    const VERTICAL_ALIGN_BOTTOM = 'vb';
    const VERTICAL_ALIGN_MIDDLE = 'vm';

    public ?int $width = 1200;
    public ?int $height = 630;

    public int $paddingTop = 0;
    public int $paddingBottom = 0;
    public int $paddingLeft = 0;
    public int $paddingRight = 0;

    public string $horizontalAlign = self::HORIZONTAL_ALIGN_CENTER;
    public string $verticalAlign = self::VERTICAL_ALIGN_MIDDLE;

    abstract public function apply(ImageInterface $image): ImageInterface;

    public function setPadding($padding): self
    {
        if (!is_array($padding)) {
            $padding = (int) $padding;

            $this->paddingLeft = $padding;
            $this->paddingTop = $padding;
            $this->paddingRight = $padding;
            $this->paddingBottom = $padding;

            return $this;
        }

        $this->paddingLeft = $padding[0] ? (int) $padding[0] : $this->paddingLeft;
        $this->paddingTop = $padding[1] ? (int) $padding[1] : $this->paddingTop;
        $this->paddingRight = $padding[2] ? (int) $padding[2] : $this->paddingRight;
        $this->paddingBottom = $padding[3] ? (int) $padding[3] : $this->paddingBottom;

        return $this;
    }

    /**
     * @return int[]
     */
    protected function getCanvasDimensions(): array
    {
        return [
            $this->width - $this->paddingLeft - $this->paddingRight,
            $this->height - $this->paddingTop - $this->paddingBottom,
        ];
    }

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

        $x = max(0, $x);
        $y = max(0, $y);

        return new Point($x, $y);
    }
}