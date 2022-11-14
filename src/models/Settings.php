<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\fields\TemplateSelectField;
use alps\sharepreviews\SharePreviews;
use Craft;

/**
 * @property Template[] $templates
 */
class Settings extends \craft\base\Model
{
    public string $routePrefix = 'previews';

    public bool $ensureGitignoreExists = true;

    public bool $disableImageCache = false;

    public bool $showSetUpNavigationItemInCp = true;

    private ?string $customFontsPath = null;

    private array $templates = [];

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'templates',
            'customFontsPath',
        ]);
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function setTemplates(array $templates): self
    {
        $this->templates = array_map(function ($template) {
            if ($template instanceof Template) {
                return $template;
            }

            return new Template($template);
        }, $templates);

        return $this;
    }

    public function getCustomFontsPath(): ?string
    {
        return $this->customFontsPath;
    }

    public function setCustomFontsPath(?string $customFontsPath): self
    {
        if ($customFontsPath === null) {
            $this->customFontsPath = null;

            return $this;
        }

        $customFontsPath = ltrim($customFontsPath, '/');
        $customFontsPath = rtrim($customFontsPath, '/');

        $this->customFontsPath = $customFontsPath;

        return $this;
    }

    public function getPreviewUrl(): string
    {
        return (new Image)->getUrl();
    }

    public function getTemplateSelectFieldName(): string
    {
        return TemplateSelectField::displayName();
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['routePrefix', 'required'],
            ['routePrefix', 'trim'],
            ['routePrefix', 'string', 'length' => [1, 30]],
            ['routePrefix', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/i'],
            ['customFontsPath', 'validateCustomFontsPath'],
        ]);
    }

    public function validateCustomFontsPath(string $attribute, $params, $validator)
    {
        $plugin = SharePreviews::getInstance();

        $relative = $this->{$attribute};
        $path = $plugin->fonts->getPathToCustomFonts($relative);

        $numberOfFiles = $plugin->fileHandler->getNumberOfFilesAndDirectories($path);

        if ($numberOfFiles < 1000) {
            return;
        }

        $this->addError(
            $attribute,
            Craft::t(
                'share-previews',
                'The specified path contains {numberOfFiles} files. Please be more specific.',
                [
                    'numberOfFiles' => number_format($numberOfFiles),
                ]
            ),
        );
    }
}
