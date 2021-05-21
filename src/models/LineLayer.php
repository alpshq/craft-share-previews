<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\behaviors\HasColors;
use Craft;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class LineLayer extends AbstractLayer
{
    private array $from = [0,0];
    private array $to = [1200,630];

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Line Layer');
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        $from = new Point(...$this->from);
        $to = new Point(...$this->to);

        $image->draw()->rectangle($from, $to, $this->toColor($this->color), true);

        return $image;
    }

    /**
     * @return int[]
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @return int[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    /**
     * @param int[] $from
     */
    public function setFrom(array $from): void
    {
        $this->from = $this->castCoordinates($from);
    }

    /**
     * @param array|int[] $to
     */
    public function setTo(array $to): void
    {
        $this->to = $this->castCoordinates($to);
    }

    private function castCoordinates(array $coords): array
    {
        $x = $coords[0] ?? 0;
        $y = $coords[1] ?? 0;

        return [
            (int) $x,
            (int) $y,
        ];
    }

    public function scaleTo(int $width, int $height): AbstractLayer
    {
        $currentWidth = $this->width;
        $currentHeight = $this->height;

        $this->from = [
            $this->scaleProperty($currentWidth, $width, $this->from[0]),
            $this->scaleProperty($currentHeight, $height, $this->from[1]),
        ];

        $this->to = [
            $this->scaleProperty($currentWidth, $width, $this->to[0]),
            $this->scaleProperty($currentHeight, $height, $this->to[1]),
        ];

        return parent::scaleTo($width, $height);
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getColorAttributes(),
            [
                'from',
                'to',
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