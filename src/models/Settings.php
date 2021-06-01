<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\fields\TemplateSelectField;
use Craft;

/**
 * @property Template[] $templates
 */
class Settings extends \craft\base\Model
{
    public string $routePrefix = 'previews';

    public bool $ensureGitignoreExists = true;

    public bool $disableImageCache = false;

    public string $fontCachePath = '';

    private array $templates = [];

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->fontCachePath = Craft::$app->path->getRuntimePath() . '/spfonts';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
           'templates',
        ]);
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function setTemplates(array $templates): self
    {
        $this->templates = array_map(function ($template) {
            return new Template($template);
        }, $templates);

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
}
