<?php


namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\ParsesPercentages;
use alps\sharepreviews\models\concerns\ScalesProperties;
use alps\sharepreviews\validators\FilterValidator;
use craft\base\Model;
use craft\elements\Entry;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use Laminas\Feed\Reader\Entry\EntryInterface;
use phpDocumentor\Reflection\Types\Static_;
use ReflectionClass;
use ReflectionProperty;
use yii\debug\components\search\Filter;

abstract class AbstractLayer extends Model
{
    use ScalesProperties;

    public static function getTypes(): array
    {
        return [
            'color' => ColorLayer::class,
            'gradient' => GradientLayer::class,
            'image' => ImageLayer::class,
            'line' => LineLayer::class,
            'text' => TextLayer::class,
            'asset' => AssetLayer::class,
        ];
    }

    public static function make(array $attributes): self
    {
        $instance = self::makeFromType($attributes['type']);

        unset($attributes['type']);

        $attributes = $instance->castAttributes($attributes);

        foreach ($attributes as $prop => $value) {
            if ($instance->hasProperty($prop)) {
                $instance->{$prop} = $value;
            }
        }
//        $instance->setAttributes($attributes, false);

        return $instance;
    }

    public static function makeFromType(string $type): self
    {
        $className = self::getTypes()[$type];

        return new $className;
    }

    abstract public function getTitle(): string;

    abstract public function apply(ImageInterface $image): ImageInterface;

    abstract public function isAvailableInTemplateEditor(): bool;

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'type'
        ]);
    }

    public function getType(): string
    {
        $types = array_flip(self::getTypes());

        return $types[static::class];
    }

    public function willRender(Entry $entry)
    {
        //
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

    /**
     * @return int[]
     */
    protected function getCanvasDimensions(): array
    {
        return [
            $this->width,
            $this->height,
        ];
    }

    public function copy(): self
    {
        return new static($this);
    }

//    public function setAttributes($values, $safeOnly = true)
//    {
//        $casted = [];
//
//        $reflection = new ReflectionClass($this);
//
//        foreach ($values as $prop => $value) {
//            if ($value !== null && $reflection->hasProperty($prop)) {
//                $reflectionProperty = $reflection->getProperty($prop);
//
//                if ($reflectionProperty->isPublic() && $reflectionProperty->hasType()) {
//                    settype($value, $reflectionProperty->getType()->getName());
//                }
//            }
//
//            $casted[$prop] = $value;
//        }
//
//        parent::setAttributes($casted, $safeOnly);
//    }
    private function castAttributes(array $attributes)
    {
        $reflection = new ReflectionClass($this);

        $casted = [];

        foreach ($attributes as $prop => $value) {
            if ($value !== null && $reflection->hasProperty($prop)) {
                $reflectionProperty = $reflection->getProperty($prop);

                if ($reflectionProperty->isPublic() && $reflectionProperty->hasType()) {
                    settype($value, $reflectionProperty->getType()->getName());
                }
            }

            $casted[$prop] = $value;
        }

        return $casted;
    }
}