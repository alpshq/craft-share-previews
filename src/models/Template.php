<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\HasLayers;
use alps\sharepreviews\SharePreviews;
use BadMethodCallException;
use Craft;
use craft\base\Model;
use craft\elements\Entry;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class Template extends Model
{
    use HasLayers;

    private int $width = 1200;
    private int $height = 630;

    private ?int $id = null;
    public ?string $name = null;
    public bool $isDefault = false;

//    public function attributes()
//    {
//        return array_merge(parent::attributes(), [
//            'layers',
//        ]);
//    }

    public function setId($id): self
    {
        $id = (int) $id;

        if ($id < 1) {
            $id = null;
        }

        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function toImage(): Image
    {
        return new Image([
            'layers' => $this->layers,
        ]);
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getLayersAttributes(),
            [
                'id',
                'layers',
            ]
        );
    }

}
