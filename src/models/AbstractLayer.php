<?php


namespace alps\sharepreviews\models;

use craft\base\Model;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

abstract class AbstractLayer extends Model
{
    public ?int $width = null;
    public ?int $height = null;

    public int $paddingTop = 0;
    public int $paddingBottom = 0;
    public int $paddingLeft = 0;
    public int $paddingRight = 0;

    abstract public function apply(ImageInterface $image): ImageInterface;

    public function scaleTo(int $width, int $height): self
    {
        if ($this->width === null) {
            $this->width = $width;
        }

        if ($this->height === null) {
            $this->height = $height;
        }

        $this->paddingLeft = $this->scaleProperty($this->width, $width, $this->paddingLeft);
        $this->paddingRight = $this->scaleProperty($this->width, $width, $this->paddingRight);
        $this->paddingTop = $this->scaleProperty($this->height, $height, $this->paddingTop);
        $this->paddingBottom = $this->scaleProperty($this->height, $height, $this->paddingBottom);

        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    private function scaleProperty(int $source, int $target, int $property): int
    {
        if ($source === $target) {
            return $property;
        }

        $multiplier = $target / $source;

        return round($property * $multiplier);
    }

    protected function toColor($color): RGB
    {
        if (is_array($color)) {
            $color = sprintf(
                '#%02x%02x%02x',
                $color[0] ?? 0,
                $color[1] ?? 0,
                $color[2] ?? 0,
            );
        }

        return (new RGBPalette)->color($color);
    }

    public function setPadding(int $padding): self
    {
        $this->paddingTop = $padding;
        $this->paddingBottom = $padding;
        $this->paddingLeft = $padding;
        $this->paddingRight = $padding;

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
}