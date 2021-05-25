<?php

namespace alps\sharepreviews\models;

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
    public ?int $id = null;
    public string $name;
    public ?Image $image = null;

//    public function attributes()
//    {
//        return array_merge(parent::attributes(), [
//            'layers',
//        ]);
//    }
}
