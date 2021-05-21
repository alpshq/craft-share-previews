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

        $this->padding = array_map('intval', $padding);

        return $this;
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

    public function setPaddingLeft($value): self
    {
        $this->padding[0] = (int) $value;

        return $this;
    }

    public function setPaddingTop($value): self
    {
        $this->padding[1] = (int) $value;

        return $this;
    }

    public function setPaddingRight($value): self
    {
        $this->padding[2] = (int) $value;

        return $this;
    }

    public function setPaddingBottom($value): self
    {
        $this->padding[3] = (int) $value;

        return $this;
    }
}