<?php

namespace alps\sharepreviews\models\vendortemplates;

use craft\helpers\StringHelper;

abstract class AbstractVendorTemplate extends \alps\sharepreviews\models\Template
{
    public static function getTemplates()
    {
        return [
            1 => Electra::class,
            2 => Noir::class,
            3 => Developer::class,
            4 => Pride::class,
            5 => Simple::class,
            6 => SimpleBlack::class,
            7 => BlackTouchesWhite::class,
            8 => SimpleImage::class,
            9 => SimpleImageBlack::class,
            10 => EveningExpress::class,
            11 => Blogger::class,
            12 => DoubleStatement::class,
            13 => Quote::class,
            14 => Architect::class,
            15 => Dancer::class,
            16 => Anaglyph::class,
        ];
    }

    public function init()
    {
        parent::init();

        $map = array_flip(self::getTemplates());

        $className = static::class;

        $this->id = $map[$className] ?? $this->id;

        $parts = explode('\\', $className);

        $name = array_pop($parts);

        $name = StringHelper::humanize(StringHelper::delimit($name, ' '));

        $this->name = $name;

        $this->layers = $this->getLayerConfig();
    }

    abstract protected function getLayerConfig(array $vars = []): array;

    public function setUp(array $vars = []): self
    {
        $this->layers = $this->getLayerConfig($vars);

        return $this;
    }
}
