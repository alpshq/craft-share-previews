<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\behaviors\HasColors;
use alps\sharepreviews\models\concerns\ParsesPercentages;
use Craft;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class LineLayer extends AbstractLayer
{
    const LINE_TYPE_HORIZONTAL = 'h';
    const LINE_TYPE_VERTICAL = 'v';

//    private array $from = [0,630/2];
//    private array $to = [1200,630/2];

    private string $lineType = self::LINE_TYPE_HORIZONTAL;
    private int $length = 1200;
    private int $x = 0;
    private int $y = 630/2;

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Line');
    }

    public function isAvailableInTemplateEditor(): bool
    {
        return true;
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        $from = $to = [$this->x, $this->y];

        if ($this->lineType === self::LINE_TYPE_HORIZONTAL) {
            $to[0] += $this->length - 1;
        } else {
            $to[1] += $this->length - 1;
        }

        $from = new Point(...$from);
        $to = new Point(...$to);

        $image->draw()->rectangle($from, $to, $this->toColor($this->color), true);

        return $image;
    }


    public function getLineType(): string
    {
        return $this->lineType;
    }

    public function setLineType(string $type): self
    {
        $this->lineType = $type === self::LINE_TYPE_VERTICAL
            ? self::LINE_TYPE_VERTICAL
            : self::LINE_TYPE_HORIZONTAL;

        return $this;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function setX($value): self
    {
        $value = (int) $value;

        if ($value >= $this->width) {
            $value = $this->width - 1;
        }

        $this->x = $value;

        return $this;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setY($value): self
    {
        $value = (int) $value;

        if ($value >= $this->height) {
            $value = $this->height - 1;
        }

        $this->y = $value;

        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength($value): self
    {
        $value = (int) $value;

        $this->length = $value;

        return $this;
    }

    protected function getScalableProperties(): array
    {
        $isHorizontal = $this->lineType === self::LINE_TYPE_HORIZONTAL;

        return array_merge(parent::getScalableProperties(), [
            'length' => $isHorizontal ? 'width' : 'height',
            'x' => 'width',
            'y' => 'height',
        ]);
    }

//    public function scaleTo(int $width, int $height): AbstractLayer
//    {
//        $currentWidth = $this->width;
//        $currentHeight = $this->height;
//
//        $this->from = [
//            $this->scaleProperty($currentWidth, $width, $this->from[0]),
//            $this->scaleProperty($currentHeight, $height, $this->from[1]),
//        ];
//
//        $this->to = [
//            $this->scaleProperty($currentWidth, $width, $this->to[0]),
//            $this->scaleProperty($currentHeight, $height, $this->to[1]),
//        ];
//
//        return parent::scaleTo($width, $height);
//    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getColorAttributes(),
            [
                'length',
                'x',
                'y',
                'lineType',
            ],
        );
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            ['class' => HasColors::class, 'properties' => ['color']],
        ]);
    }
}