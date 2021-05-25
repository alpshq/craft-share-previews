<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\Config;
use Craft;
use craft\base\Element;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\elements\db\AssetQuery;
use craft\elements\Entry;
use craft\fields\Assets;
use Imagine\Filter\Advanced\Border;
use Imagine\Filter\Transformation;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;

class AssetLayer extends ImageLayer
{
    private ?int $assetId = null;
    public ?int $fieldId = null;
    private ?Asset $asset = null;

    public function getTitle(): string
    {
        return Craft::t('share-previews', 'Asset Layer');
    }

    public function isAvailableInTemplateEditor(): bool
    {
        return true;
    }

    public function willRender(Entry $entry)
    {
        if (!$this->fieldId) {
            return;
        }

        $field = Craft::$app->getFields()->getFieldById($this->fieldId);

        if (!$field) {
            return;
        }

        /** @var AssetQuery|null $query */
        $query = $entry->getFieldValues([$field->handle])[$field->handle] ?? null;

        if (!$query) {
            return;
        }

        $asset = $query->one();

        if (!$asset || !$asset instanceof Asset) {
            return;
        }

        $this->assetId = $asset->id;
        $this->asset = $asset;
    }

    public function setAssetId($assetId): self
    {
        $this->asset = null;

        if ($assetId instanceof Asset) {
            $this->asset = $assetId;
            $this->assetId = $assetId->id;

            return $this;
        }

        if (is_array($assetId)) {
            $assetId = $assetId[0] ?? null;
        }

        $assetId = (int) $assetId;

        $this->assetId = $assetId > 0 ? $assetId : null;

        return $this;
    }

    public function getAssetId(): ?int
    {
        return $this->assetId;
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'assetId',
        ]);
    }

    public function getAsset(): ?Asset
    {
        if (!$this->assetId) {
            return null;
        }

        if ($this->asset && $this->asset->id === $this->assetId) {
            return $this->asset;
        }

        return $this->asset = Asset::findOne($this->assetId);
    }

    protected function getPath(): ?string
    {
        $asset = $this->getAsset();

        if (!$asset) {
            return null;
        }

        return $asset->getImageTransformSourcePath();
    }

    public function getAvailableAssetFieldsAsOptions(): array
    {
        $fields = Craft::$app->getFields()->getFieldsByElementType(Entry::class);

        $fields = array_filter($fields, function($field) {
            return $field instanceof Assets;
        });

        $fields = array_values($fields);

        $options = array_map(function(Assets $field) {
            return [
                'value' => $field->id,
                'label' => sprintf('%s [%s]', $field->name, $field->handle),
            ];
        }, $fields);

        usort($options, function ($a, $b) {
            /* Source: http://stackoverflow.com/a/3650743/1402176 */
            $clean = function ($str) {
                return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1' . chr(255) . '$2', htmlentities($str, ENT_QUOTES, 'UTF-8'));
            };

            return strcmp($clean($a['label']), $clean($b['label']));
        });

        array_unshift($options, [
            'value' => null,
            'label' => Craft::t('share-previews', 'No Replacement'),
        ]);

        return $options;
    }
}