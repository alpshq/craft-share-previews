<?php

namespace alps\sharepreviews\models\concerns;

trait ScalesProperties
{
    public ?int $width = 1200;

    public ?int $height = 630;

    protected function getScalableProperties(): array
    {
        return [];
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

    protected function scaleProperty(int $source, int $target, int $property): int
    {
        if ($source === $target) {
            return $property;
        }

        $multiplier = $target / $source;

        return round($property * $multiplier);
    }
}
