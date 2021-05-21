<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\Config;
use Craft;
use craft\elements\Asset;
use Imagine\Filter\Advanced\Border;
use Imagine\Filter\Transformation;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;

class AssetLayer extends ImageLayer
{
    public ?int $assetId = null;

    private ?Asset $asset;

    private function getAsset(): ?Asset
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

        $folder = Craft::$app->getAssets()->getRootFolderByVolumeId($asset->volumeId);

        return $folder->path . $asset->getPath();
    }
}