<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\ImageLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\models\TextLayer;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use alps\sharepreviews\Config;
use yii\base\Component;

class Templates extends Component
{
    const DEFAULT_TEMPLATE_ID = 0;

    private Settings $settings;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->settings = SharePreviews::getInstance()->settings;
    }

    public function getAvailableTemplates(): array
    {
        return [
            0 => 'Foo Template',
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
        if (!$this->isValidTemplateId($id)) {
            return null;
        }

        return new Image([
            'templateId' => $id,
        ]);
    }
}
