<?php

namespace alps\sharepreviews\models\concerns;

use alps\sharepreviews\models\AbstractLayer;
use craft\elements\Asset;

/**
 * @property AbstractLayer[] $layers
 */
trait HasAssets
{
    private function setAssetId(string $propertyName, $id)
    {
        if (is_array($id)) {
            $id = array_values($id)[0];
        }

        $id = (int) $id;

        $this->{$propertyName . 'Id'} = $id > 0 ? $id : null;
    }

    private function getAsset(string $propertyName): ?Asset
    {
        $idPropertyName = $propertyName . 'Id';

        if ($this->{$idPropertyName} === null) {
            return null;
        }

        if ($this->{$propertyName} && $this->{$propertyName}->id !== $this->{$idPropertyName}) {
            return $this->{$propertyName};
        }

        return $this->{$propertyName} = Asset::findOne($this->{$idPropertyName});
    }
}
