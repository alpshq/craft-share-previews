<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\behaviors\HasColors;
use alps\sharepreviews\imagefilters\BorderRadiusFilter;
use alps\sharepreviews\models\concerns\TransformsColors;
use Craft;
use Imagine\Exception\InvalidArgumentException;
use Imagine\Exception\RuntimeException;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Fill\Gradient\Vertical;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Palette\Color\RGB as RGBColor;
use Imagine\Image\Point;

/** @property array $color
 * @method getColorAttributes()
 */
class ColorLayer extends AbstractRectangleLayer
{
//    private array $color = [0,0,0];
    public int $borderRadius = 0;

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Color Layer');
    }

    public function isAvailableInTemplateEditor(): bool
    {
        return true;
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        [$width, $height] = $this->getCanvasDimensions();

        $rect = (new Imagine)->create(
            new Box($width, $height), $this->toColor([0,0,0,0])
        );

        $rect->draw()->rectangle(
            new Point(0, 0),
            new Point($width, $height),
            $this->toColor($this->color),
            true,
            0
        );

        if ($this->borderRadius > 0) {
            $rect = (new BorderRadiusFilter(new Imagine, $this->borderRadius))->apply($rect);
        }

        return $image
            ->paste($rect, $this->getAlignedOriginPoint($width, $height));
    }

    protected function getScalableProperties(): array
    {
        return array_merge(parent::getScalableProperties(), [
            'borderRadius' => 'width',
        ]);
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getColorAttributes(),
        );
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            ['class' => HasColors::class, 'properties' => ['color']],
        ]);
    }
}