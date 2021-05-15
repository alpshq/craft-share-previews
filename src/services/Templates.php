<?php

namespace alps\sharepreviews\services;

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
            2 => 'Variant B',
            3 => 'Variant C',
        ];
    }

    public function hasImage(int $templateId): bool
    {
        return $templateId !== 3;
    }

    public function applyTemplateConfig(int $templateId, Config $config): Config
    {
        if ($templateId === 1) {
            return $config->setFromArray([
                'contentPadding' => [
                    'left' => 40,
                    'top' => 40,
                    'right' => 475,
                    'bottom' => 180,
                ],
                'imagePadding' => [
                    'left' => 755,
                    'top' => 40,
                    'right' => 40,
                    'bottom' => 180,
                ],
                'backgroundImagePath' => 'share-previews/1.png',
                'font' => [
                    'color' => [255,255,255],
                    'size' => 55,
                ],
            ]);
        }

        if ($templateId === 2) {
            return $config->setFromArray([
                'contentPadding' => [
                    'left' => 70,
                    'top' => 175,
                    'right' => 530,
                    'bottom' => 240,
                ],
                'backgroundImagePath' => 'share-previews/2.png',
            ]);
        }

        if ($templateId === 3) {
            return $config->setFromArray([
                'contentPadding' => [
                    'left' => 70,
                    'top' => 185,
                    'right' => 70,
                    'bottom' => 280,
                ],
                'backgroundImagePath' => 'share-previews/3.png',
            ]);
        }

        return $config;
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

    public function getTemplateIdFromEntry(Entry $entry): int
    {
        $id = (int) $entry->previewTemplateId;

        if ($this->isValidTemplateId($id)) {
            return $id;
        }

        return self::DEFAULT_TEMPLATE_ID;
    }
}
