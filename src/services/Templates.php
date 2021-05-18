<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\ImageLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;
use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use alps\sharepreviews\Config;
use yii\base\Component;

class Templates extends Component
{
    const DEFAULT_TEMPLATE_ID = 1;

    public function getAvailableTemplates(): array
    {
        return [
            1 => 'Variant A',
        ];
    }

    public function isValidTemplateId(?int $id): bool
    {
        if ($id === null) {
            return false;
        }

        $id = (int) $id;

        $templateIds = array_keys($this->getAvailableTemplates());

        return in_array($id, $templateIds);
    }

    public function getDefaultTemplate(): Image
    {
        return $this->getTemplate(self::DEFAULT_TEMPLATE_ID);
    }

    public function getTemplateOrDefault(?int $id): Image
    {
        if ($id === null) {
            return $this->getDefaultTemplate();
        }

        return $this->getTemplate($id) ?? $this->getDefaultTemplate();
    }

    public function getTemplate(int $id): ?Image
    {
        if ($id !== 1) {
            return null;
        }

        $layers = [
            new GradientLayer([
                'from' => [195,130,250],
                'to' => [240,70,70],
                'angle' => 45,
            ]),
            new LineLayer([
                'color' => [255,255,255,0.5],
                'from' => [40, 630-150],
                'to' => [1200-40, 630-150],
            ]),
            new TextLayer([
                'content' => 'ECG-Liste.at',
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'padding' => [40, 630-150+50, 40, 50],
                'color' => [255,255,255,0.8],
                'maxFontSize' => 30,
            ]),
            new ImageLayer([
                'path' => Craft::getAlias('@webroot/assets/logos/logo-mtv.png'),
                'padding' => [40, 630-150+50, 40, 50],
                'horizontalAlign' => ImageLayer::HORIZONTAL_ALIGN_RIGHT,
            ]),
            new ColorLayer([
                'color' => [255,255,255,0.8],
                'padding' => [40,120,400,280],
                'borderRadius' => 5,
            ]),
            new TextLayer([
                'content' => 'This could be a blog entry title: {{ entry.title }}',
                'horizontalAlign' => TextLayer::HORIZONTAL_ALIGN_LEFT,
                'color' => [50,50,50,0.5],
                'padding' => [70,150,430,310]
            ]),
            new ImageLayer([
                'path' => Craft::getAlias('@webroot/assets/images/testimonials-background.jpg'),
                'padding' => [1200-400+80,120,40,280],
                'horizontalAlign' => ImageLayer::HORIZONTAL_ALIGN_RIGHT,
                'borderWidth' => 15,
                'borderColor' => [255,255,255,0.5],
            ]),
        ];

        return new Image([
            'layers' => $layers,
            'templateId' => $id,
//            'entry' => Entry::find()->one(),
//            'width' => 1200/2,
//            'height' => 630/2,
        ]);
    }
}
