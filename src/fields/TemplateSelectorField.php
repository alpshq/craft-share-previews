<?php

namespace alps\sharepreviews\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Entry;
use alps\sharepreviews\services\Templates;
use alps\sharepreviews\Plugin;
use yii\db\Schema;

class TemplateSelectorField extends Field
{
    public static function displayName(): string
    {
        return Craft::t('site', 'Social-Previews: Template-Selector');
    }

    public static function hasContentColumn(): bool
    {
        return true;
    }

    public static function valueType(): string
    {
        return 'int|null';
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_INTEGER;
    }

    public function getSettingsHtml(): string
    {
        return '';
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $plugin = Plugin::getInstance();

        $templates = collect($plugin->templates->getAvailableTemplates())
            ->map(function(string $name, int $id) use ($element) {
                return [
                    'id' => $id,
                    'name' => $name,
                    'preview' => $this->getPreviewUrl($id, $element),
                ];
            })
            ->all();

        return Craft::$app->getView()->renderTemplate('social-previews/_template-selector', [
            'field' => $this,
            'templates' => $templates,
            'value' => $value,
            'hasError' => $element && $element->hasErrors($this->handle),
        ]);
    }

    private function getPreviewUrl(int $templateId, ElementInterface $element = null): ?string
    {
        $images = Plugin::getInstance()->images;

        if (!$element instanceof Entry) {
            return null;
        }

        $config = $images->createConfigFromEntry($element, $templateId);

        return $config->getUrl();
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        $value = (int) $value;

        $templates = Plugin::getInstance()->templates;

        if ($value === null || $templates->isValidTemplateId($value) === false) {
            return Templates::DEFAULT_TEMPLATE_ID;
        }

        return $value;
    }

    public function getElementValidationRules(): array
    {
        return array_merge(parent::getElementValidationRules(), [
            ['validateValue'],
        ]);
    }

    /**
     * Validates given $element.
     */
    public function validateValue(Element $element)
    {
        $value = $element->getFieldValue($this->handle);

        $isValid = Plugin::getInstance()->templates->isValidTemplateId($value);

        if ($isValid) {
            return;
        }

        $message = Craft::t('site', 'Invalid share preview template.');

        $element->addError($this->handle, $message);
    }
}
