<?php

namespace alps\sharepreviews\fields;

use alps\sharepreviews\models\Template;
use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Entry;
use alps\sharepreviews\services\Templates;
use alps\sharepreviews\SharePreviews;
use yii\db\Schema;

/**
 *
 * @property-read int[]    $availableTemplates
 * @property-read null|int $defaultTemplate
 */
class TemplateSelectField extends Field
{
    private ?array $availableTemplates = null;
    private ?int $defaultTemplate = null;

    public static function displayName(): string
    {
        return Craft::t('share-previews', 'Template-Selector (Share Previews)');
    }

    public static function hasContentColumn(): bool
    {
        return true;
    }

    public static function valueType(): string
    {
        return 'int|null';
    }

    public function getAvailableTemplates(): ?array
    {
        return $this->availableTemplates;
    }

    public function setAvailableTemplates($templates): self
    {
        if ($templates === null || $templates === '*') {
            $this->availableTemplates = null;

            return $this;
        }

        if (!is_array($templates)) {
            $templates = [$templates];
        }

        $templates = array_map('intval', $templates);
        $templates = array_filter($templates);
        $templates = array_values($templates);

        $this->availableTemplates = $templates;

        return $this;
    }

    public function getDefaultTemplate(): ?int
    {
        return $this->defaultTemplate;
    }

    public function setDefaultTemplate($defaultTemplate): self
    {
        $defaultTemplate = (int) $defaultTemplate;

        $this->defaultTemplate = $defaultTemplate > 0 ? $defaultTemplate : null;

        return $this;
    }

    public function settingsAttributes(): array
    {
        return array_merge(parent::settingsAttributes(), [
            'availableTemplates',
            'defaultTemplate',
        ]);
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_INTEGER;
    }

    private function getTemplateOptions(): array
    {
        $plugin = SharePreviews::getInstance();

        $helpers = $plugin->helpers;
        $templates = $plugin->templates->getAvailableTemplates();

        $options = array_map(function(Template $template) {
            $label = $template->getHumanFriendlyName();

            if ($template->isDefault) {
                $label .= ' ' . Craft::t('share-previews', '(Global Default)');
            }

            return [
                'value' => $template->id,
                'label' => $label,
                'default' => $template->isDefault,
            ];
        }, $templates);

        return $helpers->sortOptions($options, function($a, $b, $comparison) {
            $aDefault = (int) $a['default'];
            $bDefault = (int) $b['default'];

            if ($aDefault === $bDefault) {
                return $comparison;
            }

            return $aDefault > $bDefault ? -1 : 1;
        });
    }

    private function getTemplateDefaultOptions(): array
    {
        $plugin = SharePreviews::getInstance();

        $templates = $plugin->templates->getAvailableTemplates();

        $options = array_map(function(Template $template) {
            $label = $template->getHumanFriendlyName();

            return [
                'value' => $template->id,
                'label' => $label,
            ];
        }, $templates);

        $options = $plugin->helpers->sortOptions($options);

        array_unshift($options, [
            'value' => '',
            'label' => Craft::t('share-previews', 'No, just use the global default'),
        ]);

        return $options;
    }

    public function getSettingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('share-previews/fields/template-select-settings', [
            'field' => $this,
            'templateOptions' => $this->getTemplateOptions(),
            'templateDefaultOptions' => $this->getTemplateDefaultOptions(),
        ]);
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $plugin = SharePreviews::getInstance();

//        $templates = collect($plugin->templates->getAvailableTemplates())
//            ->map(function(string $name, int $id) use ($element) {
//                return [
//                    'id' => $id,
//                    'name' => $name,
//                    'preview' => $this->getPreviewUrl($id, $element),
//                ];
//            })
//            ->all();

        $templatesService = $plugin->templates;
        $templates = $templatesService->getAvailableTemplates();
        $templates = $this->filterTemplates($templates);

        $images = [];

        foreach ($templates as $template) {
            $images[$template->id] = $template->toImage();

            if ($element instanceof Entry) {
                $images[$template->id]->setEntry($element);
            }
        }

        $value = (int) $value;

        if (!$templatesService->isValidTemplateId($value)) {
            $value = null;
        }

        return Craft::$app->getView()->renderTemplate('share-previews/fields/template-select', [
            'field' => $this,
            'templates' => $templates,
            'images' => $images,
            'value' => $value,
            'defaultValue' => $this->getDefaultValue(),
//            'hasError' => $element && $element->hasErrors($this->handle),
        ]);
    }

    private function getDefaultValue(): ?int
    {
        if ($this->defaultTemplate) {
            return $this->defaultTemplate;
        }

        $default = SharePreviews::getInstance()->templates->getDefaultTemplate();

        if ($default) {
            return $default->id;
        }

        return null;
    }

    private function filterTemplates(array $templates): array
    {
        if ($this->availableTemplates === null) {
            return $templates;
        }

        $templates = array_filter($templates, function (Template $template) {
            if ($this->defaultTemplate === $template->id) {
                return true;
            }

            return in_array($template->id, $this->availableTemplates);
        });

        return array_values($templates);
    }

//
//    private function getPreviewUrl(int $templateId, ElementInterface $element = null): ?string
//    {
//        $images = SharePreviews::getInstance()->images;
//
//        if (!$element instanceof Entry) {
//            return null;
//        }
//
//        $config = $images->createConfigFromEntry($element, $templateId);
//
//        return $config->getUrl();
//    }

//    public function normalizeValue($value, ElementInterface $element = null)
//    {
//        $value = (int) $value;
//
//        $templates = SharePreviews::getInstance()->templates;
//
//        if ($value === null || $templates->isValidTemplateId($value) === false) {
//            return Templates::DEFAULT_TEMPLATE_ID;
//        }
//
//        return $value;
//    }

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

        $isValid = SharePreviews::getInstance()->templates->isValidTemplateId($value);

        if ($isValid) {
            return;
        }

        $message = Craft::t('share-previews', 'Invalid share preview template.');

        $element->addError($this->handle, $message);
    }
}
