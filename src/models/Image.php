<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\SharePreviews;
use BadMethodCallException;
use Craft;
use craft\base\Model;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class Image extends Model
{
    public int $width = 1200;
    public int $height = 630;

    /** @var AbstractLayer[] */
    private array $layers = [];

//    private Settings $settings;

    private ?ImageInterface $image = null;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * @param AbstractLayer[] $layers
     */
    public function setLayers(array $layers): self
    {
        $this->layers = [];

        foreach ($layers as $layer) {
            $this->layers[]= $layer->scaleTo($this->width, $this->height);
        }

        return $this;
    }

    public function render(): ImageInterface
    {
        if ($this->image) {
            return $this->image;
        }

        foreach ($this->layers as $layer) {
            $layer->scaleTo($this->width, $this->height);
        }

        $image = (new Imagine)->create(
            new Box($this->width, $this->height)
        );

        foreach ($this->layers as $layer) {
            $image = $layer->apply($image);
        }

        return $this->image = $image;
    }
}
