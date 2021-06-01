<?php

namespace alps\sharepreviews\behaviors;

class HasColors extends \yii\base\Behavior
{
    public array $defaults = [];

    public array $properties = [];

    private array $colors = [];

    public function canSetProperty($name, $checkVars = true)
    {
        if ($this->isColorProperty($name) || $this->isOpacityProperty($name)) {
            return true;
        }

        return parent::canSetProperty($name, $checkVars);
    }

    public function canGetProperty($name, $checkVars = true)
    {
        if ($this->isColorProperty($name)) {
            return true;
        }

        return parent::canGetProperty($name, $checkVars);
    }

    private function isColorProperty(string $name): bool
    {
        return in_array($name, $this->properties);
    }

    private function isOpacityProperty(string $name): bool
    {
        if (substr($name, -7) !== 'Opacity') {
            return false;
        }

        $property = substr($name, 0, -7);

        return in_array($property, $this->properties);
    }

    public function __set($name, $value)
    {
        if ($this->isColorProperty($name)) {
            $this->colors[$name] = $this->transformToRgb($value);

            return;
        }

        if (! $this->isOpacityProperty($name)) {
            parent::__set($name, $value);

            return;
        }

        if (! is_int($value) && empty($value) && $value !== '0') {
            $value = 100;
        }

        $property = substr($name, 0, -7);

        $color = $this->colors[$property];

        if (count($color) !== 4) {
            $color[] = 1;
        }

        $alpha = (int) $value;

        if ($alpha > 100) {
            $alpha = 100;
        }

        $color[3] = $alpha / 100;

        $this->colors[$property] = $color;
    }

    public function __get($name)
    {
        if ($this->isColorProperty($name)) {
            return $this->colors[$name] ?? $this->defaults[$name] ?? [0, 0, 0];
        }

        return parent::__get($name);
    }

    /**
     * @param string|array $color
     */
    private function transformToRgb($color): array
    {
        if (is_array($color)) {
            return array_slice($color, 0, 4);
        }

        $color = (string) $color;
        $color = ltrim($color, '#');
        $color = str_split($color, strlen($color) > 4 ? 2 : 1);

        return array_map(function ($hex) {
            $hex = str_pad($hex, 2, $hex);

            return hexdec($hex);
        }, $color);
    }

    public function getColorAttributes(): array
    {
        return $this->properties;
    }
}
