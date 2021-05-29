<?php


namespace alps\sharepreviews\models\concerns;

/**
 * @property int        $paddingLeft
 * @property int        $paddingTop
 * @property int        $paddingRight
 * @property int        $paddingBottom
 */
trait HasPadding
{
    public ?int $width = 1200;
    public ?int $height = 630;

    protected array $padding = [0, 0, 0, 0];

    protected function getHiddenPaddingFields(): array
    {
        return [
            'paddingLeft',
            'paddingTop',
            'paddingRight',
            'paddingBottom',
        ];
    }

    protected function getPaddingAttributes(): array
    {
        return [
            'padding',
            'paddingLeft',
            'paddingTop',
            'paddingRight',
            'paddingBottom',
        ];
    }

    protected function getScalablePaddingProperties(): array
    {
        return [
            'paddingLeft' => 'width',
            'paddingTop' => 'height',
            'paddingRight' => 'width',
            'paddingBottom' => 'height',
        ];
    }

    public function getPadding(): array
    {
        return $this->padding;
    }

    public function setPadding($padding): self
    {
        if (!is_array($padding)) {
            $padding = array_fill(0, 4, (int) $padding);
        }

        $padding = array_slice($padding, 0, 4);

        $expand = 4 - count($padding);

        if ($expand > 0) {
            $padding = array_pad($padding, $expand, 0);
        }

        $padding = array_map('intval', $padding);

        [
            $this->padding[0], $this->padding[2]
        ] = $this->validateOverflow($this->width, $padding[0], $padding[2]);

        [
            $this->padding[1], $this->padding[3]
        ] = $this->validateOverflow($this->height, $padding[1], $padding[3]);

        return $this;
    }

    private function validateOverflow(int $length, int $first, int $second): array
    {
        $buffer = $length - $first - $second - 1;

        if ($buffer >= 0) {
            return [$first, $second];
        }

        $buffer *= -1;

        if ($first === 0) {
            return [$first, $second - $buffer];
        }

        if ($second === 0) {
            return [$first - $buffer, $second];
        }

        $distribution = $first / (($first + $second) / 100);
        $firstOverflow = round($buffer / 100 * $distribution);
        $secondOverflow = $buffer - $firstOverflow;

        return [
            $first - $firstOverflow,
            $second - $secondOverflow,
        ];
    }

    public function getPaddingLeft(): int
    {
        return $this->padding[0];
    }

    public function getPaddingTop(): int
    {
        return $this->padding[1];
    }

    public function getPaddingRight(): int
    {
        return $this->padding[2];
    }

    public function getPaddingBottom(): int
    {
        return $this->padding[3];
    }

    private function setSinglePaddingValue(int $idx, $value): self
    {
        $padding = $this->padding;

        $padding[$idx] = (int) $value;

        $this->setPadding($padding);

        return $this;
    }

    public function setPaddingLeft($value): self
    {
        return $this->setSinglePaddingValue(0, $value);
    }

    public function setPaddingTop($value): self
    {
        return $this->setSinglePaddingValue(1, $value);
    }

    public function setPaddingRight($value): self
    {
        return $this->setSinglePaddingValue(2, $value);
    }

    public function setPaddingBottom($value): self
    {
        return $this->setSinglePaddingValue(3, $value);
    }
}