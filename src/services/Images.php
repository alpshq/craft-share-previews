<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\fields\TemplateSelectField;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\Template;
use alps\sharepreviews\SharePreviews;
use craft\elements\Entry;
use yii\base\Component;

class Images extends Component
{
    private Templates $templatesService;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->templatesService = SharePreviews::getInstance()->templates;
    }

    public function createImageFromEntry(Entry $entry): Image
    {
        $template = $this->getTemplateFromEntry($entry);

        return $template->toImage()->setEntry($entry);
    }

    private function getTemplateFromEntry(Entry $entry): Template
    {
        $field = $this->getTemplateSelectField($entry);
        $templatesService = $this->templatesService;

        if ($field === null) {
            return $templatesService->getDefaultTemplate(true);
        }

        $value = (int) $entry->getFieldValues([$field->handle])[$field->handle] ?? 0;

        if ($templatesService->isValidTemplateId($value)) {
            return $templatesService->getTemplateById($value);
        }

        $default = (int) $field->defaultTemplate;

        if ($templatesService->isValidTemplateId($default)) {
            return $templatesService->getTemplateById($default);
        }

        return $templatesService->getDefaultTemplate(true);
    }

    private function getTemplateSelectField(Entry $entry): ?TemplateSelectField
    {
        $fieldLayout = $entry->getFieldLayout();

        if (! $fieldLayout) {
            return null;
        }

        $fields = $fieldLayout->getFields();

        if (empty($fields)) {
            return null;
        }

        $fields = array_filter($fields, function ($field) {
            return $field instanceof TemplateSelectField;
        });

        $fields = array_values($fields);

        if (empty($fields)) {
            return null;
        }

        return $fields[0];
    }
}
