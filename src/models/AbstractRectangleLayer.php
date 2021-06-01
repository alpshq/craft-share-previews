<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\HasPadding;
use alps\sharepreviews\models\concerns\HidesFields;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

/**
 * @property int[] $canvasDimensions
 * @property array $scalableProperties
 */
abstract class AbstractRectangleLayer extends AbstractLayer
{
    use HidesFields;
    use HasPadding;

    const HORIZONTAL_ALIGN_LEFT = 'hl';

    const HORIZONTAL_ALIGN_RIGHT = 'hr';

    const HORIZONTAL_ALIGN_CENTER = 'hc';

    const VERTICAL_ALIGN_TOP = 'vt';

    const VERTICAL_ALIGN_BOTTOM = 'vb';

    const VERTICAL_ALIGN_MIDDLE = 'vm';

    public ?int $width = 1200;

    public ?int $height = 630;

    public string $horizontalAlign = self::HORIZONTAL_ALIGN_CENTER;

    public string $verticalAlign = self::VERTICAL_ALIGN_MIDDLE;

    public function getHiddenFields(): array
    {
        return $this->getHiddenPaddingFields();
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getPaddingAttributes(),
        );
    }

    /**
     * @return int[]
     */
    public function getCanvasDimensions(): array
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

    protected function getScalableProperties(): array
    {
        return array_merge(
            parent::getScalableProperties(),
            $this->getScalablePaddingProperties(),
        );
    }
}
