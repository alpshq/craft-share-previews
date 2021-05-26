<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\behaviors\HasColors;
use alps\sharepreviews\Config;
use Craft;
use Imagine\Filter\Advanced\Border;
use Imagine\Filter\Transformation;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;

class ImageLayer extends AbstractRectangleLayer
{
    const FILL_MODE_CONTAIN = 'contain';
    const FILL_MODE_COVER = 'cover';

    public ?string $path = null;

    public int $borderWidth = 0;

    public string $fillMode = self::FILL_MODE_CONTAIN;

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Image');
    }

    public function isAvailableInTemplateEditor(): bool
    {
        return false;
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        $path = $this->getPath();

        if (empty($path) || !file_exists($path)) {
            return $image;
        }

        [$maxWidth, $maxHeight] = $this->getCanvasDimensions();

        $settings = $this->fillMode === self::FILL_MODE_CONTAIN
            ? ManipulatorInterface::THUMBNAIL_INSET
            : ManipulatorInterface::THUMBNAIL_OUTBOUND;

        $openedImage = (new Imagine)
            ->open($path)
            ->thumbnail(new Box($maxWidth, $maxHeight), $settings);

        $size = $openedImage->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();

        $openedImage = $this->applyBorder($openedImage, $width, $height);

        return $image
            ->paste($openedImage, $this->getAlignedOriginPoint($width, $height));
    }

    protected function getPath(): ?string
    {
        return $this->path;
    }

    private function applyBorder(ImageInterface $image, int $width, int $height): ImageInterface
    {
        if ($this->borderWidth < 1) {
            return $image;
        }

        $color = $this->toColor($this->borderColor);

        $points = [
            [
                new Point(0, $this->borderWidth),
                new Point($this->borderWidth - 1, $height - $this->borderWidth - 1),
            ],
            [
                new Point($width - $this->borderWidth, $this->borderWidth),
                new Point($width - 1, $height - $this->borderWidth - 1),
            ],
            [
                new Point(0, 0),
                new Point($width, $this->borderWidth - 1),
            ],
            [
                new Point(0, $height - $this->borderWidth),
                new Point($width, $height),
            ],
        ];

        foreach ($points as [$topLeft, $rightBottom]) {
            $image->draw()->rectangle($topLeft, $rightBottom, $color, true);
        }

        return $image;
    }


    protected function getScalableProperties(): array
    {
        return array_merge(parent::getScalableProperties(), [
            'borderWidth' => 'width',
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
            ['class' => HasColors::class, 'properties' => ['borderColor']],
        ]);
    }
}