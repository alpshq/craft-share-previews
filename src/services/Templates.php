<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\models\Template;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\services\Plugins;
use yii\base\Component;

class Templates extends Component
{
    private Settings $settings;

    private ?SharePreviews $plugin;

    private Plugins $pluginService;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->plugin = SharePreviews::getInstance();
        $this->settings = $this->plugin->settings;
        $this->pluginService = Craft::$app->getPlugins();
    }

    public function getAvailableTemplates(): array
    {
        return $this->settings->templates;
    }

    public function getDefaultTemplate(bool $fallback = false): ?Template
    {
        $templates = array_filter($this->getAvailableTemplates(), function (Template $template) {
            return $template->isDefault;
        });

        $templates = array_values($templates);

        $fallbackTemplate = $fallback ? new Template : null;

        return $templates[0] ?? $this->getAvailableTemplates()[0] ?? $fallbackTemplate;
    }

    public function getTemplateById(int $id): ?Template
    {
        $templates = $this->settings->templates;

        $templates = array_filter($templates, function (Template $template) use ($id) {
            return $template->id === $id;
        });

        return array_values($templates)[0] ?? null;
    }

    public function saveTemplate(Template $template): self
    {
        $settings = $this->settings;

        $existingTemplates = $settings->templates;
        $existingTemplates = $this->assignTemplate($existingTemplates, $template);

        if ($template->isDefault) {
            $existingTemplates = $this->makeDefault($existingTemplates, $template);
        }

        $existingTemplates = $this->ensureDefaultTemplateExists($existingTemplates);

        $settings->templates = $existingTemplates;

        $this->pluginService->savePluginSettings($this->plugin, $settings->toArray());

        return $this;
    }

    private function makeDefault(array $existingTemplates, Template $template): array
    {
        $template->isDefault = true;

        return array_map(function (Template $iteratingTemplate) use ($template) {
            $iteratingTemplate->isDefault = $iteratingTemplate->id === $template->id;

            return $iteratingTemplate;
        }, $existingTemplates);
    }

    private function ensureDefaultTemplateExists(array $templates): array
    {
        if (empty($templates)) {
            return $templates;
        }

        $defaultTemplates = array_filter($templates, function (Template $template) {
            return $template->isDefault;
        });

        if (! empty($defaultTemplates)) {
            return $templates;
        }

        $firstIdx = array_keys($templates)[0];

        $templates[$firstIdx]->isDefault = true;

        return $templates;
    }

    public function deleteTemplateById(int $id): self
    {
        $settings = $this->settings;
        $originalTemplates = $settings->templates;

        $templates = array_filter($originalTemplates, function (Template $template) use ($id) {
            return $template->id !== $id;
        });

        $templates = array_values($templates);

        if (count($templates) === count($originalTemplates)) {
            return $this;
        }

        $templates = $this->ensureDefaultTemplateExists($templates);

        $settings->templates = $templates;
        $settings = $settings->toArray();

        $this->pluginService->savePluginSettings($this->plugin, $settings);

        return $this;
    }

    private function assignTemplate(array $existingTemplates, Template $template): array
    {
        $ids = array_map(function (Template $template) {
            return $template->id;
        }, $existingTemplates);

        $idx = $template->id
            ? array_search($template->id, $ids, true)
            : false;

        if ($idx === false) {
            $template->id = empty($ids) ? 1 : max($ids) + 1;
            $idx = count($existingTemplates);
        }

        $existingTemplates[$idx] = $template;

        return array_values($existingTemplates);
    }

    public function isValidTemplateId(int $id)
    {
        $templateIds = array_map(function (Template $template) {
            return $template->id;
        }, $this->settings->templates);

        return in_array($id, $templateIds, true);
    }
}
