<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\HasLayers;
use alps\sharepreviews\SharePreviews;
use craft\base\Model;
use craft\elements\Entry;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Image extends Model
{
    use HasLayers;

    const PNG_COMPRESSION_LEVEL = 5;

    public int $width = 1200;

    public int $height = 630;

    public ?int $templateId = null;

    private ?Entry $entry = null;

    private array $variables = [];

    public static function fromEncodedDiff(string $diff): self
    {
        $differ = SharePreviews::getInstance()->imageDiffer;

        $data = $differ->decodeDiff($diff);

        return $differ->createFromDiff($data);
    }

    public function setEntry(?Entry $entry): self
    {
        $this->variables['entry'] = $entry;

        return $this;
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getLayersAttributes(),
        );
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $this->willRender();

        return parent::toArray($fields, $expand, $recursive);
    }

    private function willRender(): self
    {
        foreach ($this->layers as $layer) {
            $layer->willRender($this->variables);
        }

        return $this;
    }

    public function render(): ImageInterface
    {
        foreach ($this->layers as $layer) {
            $layer->scaleTo($this->width, $this->height);
        }

        $image = (new Imagine)->create(
            new Box($this->width, $this->height)
        );

        $this->willRender();

        foreach ($this->layers as $layer) {
            $image = $layer->apply($image);
        }

        return $image;
    }

    private function getTemplate(): ?self
    {
        if ($this->templateId === null) {
            return null;
        }

        $templatesService = SharePreviews::getInstance()->templates;

        $template = $templatesService->getTemplateById($this->templateId) ?? new Template;

        return $template->toImage();
    }

    public function getHash(): string
    {
        return hash('crc32', json_encode($this->toArray()));
    }

    public function getUrl(): string
    {
        $differ = SharePreviews::getInstance()->imageDiffer;

        $diff = $differ->getDiff($this);

        $template = $this->getTemplate() ?? $this;

        $encoded = $differ->encodeDiff($template->getHash(), $diff);

        return SharePreviews::getInstance()->urls->createUrl($encoded);
    }

    public function setVariable(string $name, $value): self
    {
        $this->variables[$name] = $value;

        return $this;
    }

    public function setVariables(array $variables, bool $clearPrevious = false): self
    {
        if ($clearPrevious) {
            $this->variables = $variables;

            return $this;
        }

        $this->variables = array_merge($this->variables, $variables);

        return $this;
    }
}
