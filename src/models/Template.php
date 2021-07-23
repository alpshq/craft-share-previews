<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\HasLayers;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\base\Model;
use craft\elements\Entry;

/**
 * @property ?int    $id
 * @property ?string $name
 */
class Template extends Model
{
    use HasLayers;

    public int $width = 1200;

    public int $height = 630;

    private ?int $id = null;

    private ?string $name = null;

    public bool $isDefault = false;

    private ?Entry $previewEntry = null;

    private ?int $previewEntryId = null;

    private array $variables = [];

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            $this->getLayersAttributes(),
            [
                'id',
                'name',
                'previewEntryId',
            ]
        );
    }

    public function setId($id): self
    {
        $id = (int) $id;

        if ($id < 1) {
            $id = null;
        }

        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(?string $name): self
    {
        $this->name = empty($name) ? null : $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getHumanFriendlyName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $name = Craft::t('share-previews', 'Untitled {id}', [
            'id' => $this->id,
        ]);

        return trim($name);
    }

    public function getPreviewEntryId(): ?int
    {
        return $this->previewEntryId;
    }

    public function setPreviewEntryId($entryId): self
    {
        $this->previewEntry = null;

        if ($entryId instanceof Entry) {
            $this->previewEntry = $entryId;
            $this->previewEntryId = $entryId->id;

            return $this;
        }

        if (is_array($entryId)) {
            $entryId = $entryId[0] ?? null;
        }

        $entryId = (int) $entryId;

        $this->previewEntryId = $entryId > 0 ? $entryId : null;

        return $this;
    }

    public function getPreviewEntry(): ?Entry
    {
        $entryId = $this->previewEntryId;

        if (! $entryId) {
            return null;
        }

        if ($this->previewEntry && $this->previewEntry->id === $entryId) {
            return $this->previewEntry;
        }

        $entry = Entry::findOne($entryId);

        if (! $entry) {
            return null;
        }

        return $this->previewEntry = $entry;
    }

    public function toImage(): Image
    {
        return new Image([
            'templateId' => $this->id,
            'width' => $this->width,
            'height' => $this->height,
            'layers' => $this->toArray(['layers'])['layers'],
            'variables' => $this->variables,
        ]);
    }

    public function toPreviewImage(): Image
    {
        $image = $this->toImage();

        $entry = $this->getPreviewEntry();

        if ($entry) {
            $image->setEntry($entry);
        }

        return $image;
    }

    public function exists(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $templatesService = SharePreviews::getInstance()->templates;

        return $templatesService->getTemplateById($this->id) !== null;
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
