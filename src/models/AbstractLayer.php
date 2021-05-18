<?php


namespace alps\sharepreviews\models;

use craft\base\Model;
use craft\elements\Entry;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Laminas\Feed\Reader\Entry\EntryInterface;
use phpDocumentor\Reflection\Types\Static_;

abstract class AbstractLayer extends Model
{
    public ?int $width = 1200;
    public ?int $height = 630;

    public int $paddingTop = 0;
    public int $paddingBottom = 0;
    public int $paddingLeft = 0;
    public int $paddingRight = 0;

    public static function getTypes(): array
    {
        return [
            'color' => ColorLayer::class,
            'gradient' => GradientLayer::class,
            'image' => ImageLayer::class,
            'line' => LineLayer::class,
            'text' => TextLayer::class,
        ];
    }

    public static function makeFromType(string $type): self
    {
        $className = self::getTypes()[$type];

        return new $className;
    }

    abstract public function apply(ImageInterface $image): ImageInterface;

    public function fields()
    {
        return array_merge(parent::fields(), [
            'type'
        ]);
    }

    public function getType(): string
    {
        $types = array_flip(self::getTypes());

        return $types[static::class];
    }

    protected function getScalableProperties(): array
    {
        return [
            'paddingLeft' => 'width',
            'paddingTop' => 'height',
            'paddingRight' => 'width',
            'paddingBottom' => 'height',
        ];
    }

    public function scaleTo(int $width, int $height): self
    {
        if ($this->width === null) {
            $this->width = $width;
        }

        if ($this->height === null) {
            $this->height = $height;
        }

        $props = $this->getScalableProperties();

        foreach ($props as $prop => $scaleBase) {
            if ($this->{$prop} === null) {
                continue;
            }

            $target = $scaleBase === 'width' ? $width : $height;

            $this->{$prop} = $this->scaleProperty($this->{$scaleBase}, $target, $this->{$prop});
        }

        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function willRender(Entry $entry)
    {
        //
    }

    protected function scaleProperty(int $source, int $target, int $property): int
    {
        if ($source === $target) {
            return $property;
        }

        $multiplier = $target / $source;

        return round($property * $multiplier);
    }

    protected function toColor($color): RGB
    {
        $alpha = 100;

        if (is_array($color) && count($color) === 4) {
            $alpha = (int) round(100 * $color[3]);

            $color = [
                $color[0],
                $color[1],
                $color[2],
            ];
        }

        return (new RGBPalette)->color($color, $alpha);
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

    public function copy(): self
    {
        return new static($this);
    }
}