<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\ScalesProperties;
use Craft;
use craft\base\Model;
use Exception;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\RGB;
use Imagine\Image\Palette\RGB as RGBPalette;
use ReflectionClass;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
            'type',
        ]);
    }

    public function getType(): string
    {
        $types = array_flip(self::getTypes());

        return $types[static::class];
    }

    protected function getPropertiesWithVariables(): array
    {
        return [];
    }

    protected function evaluateTwigExpression(string $expression, array $vars): ?string
    {
        try {
            return Craft::$app->getView()->renderString($expression, $vars);
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }

        return $expression;
    }

    public function willRender(array $vars)
    {
        foreach ($this->getPropertiesWithVariables() as $prop) {
            if ($prop === null) {
                continue;
            }

            $this->{$prop} = $this->evaluateTwigExpression($this->{$prop}, $vars);
        }
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

    private function castAttributes(array $attributes)
    {
        $reflection = new ReflectionClass($this);

        $casted = [];

        foreach ($attributes as $prop => $value) {
            if ($value !== null && $reflection->hasProperty($prop)) {
                $reflectionProperty = $reflection->getProperty($prop);

                if ($reflectionProperty->isPublic() && $reflectionProperty->hasType()) {
                    try {
                        settype($value, $reflectionProperty->getType()->getName());
                    } catch (Exception $e) {
                    }
                }
            }

            $casted[$prop] = $value;
        }

        return $casted;
    }
}
