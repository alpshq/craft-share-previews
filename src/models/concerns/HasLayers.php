<?php

namespace alps\sharepreviews\models\concerns;

use alps\sharepreviews\models\AbstractLayer;

/**
 * @property AbstractLayer[] $layers
 */
trait HasLayers
{
    /** @var AbstractLayer[] */
    protected array $layers = [];

    private function getLayersAttributes(): array
    {
        return [
            'layers',
        ];
    }

    /**
     * @param AbstractLayer[] $layers
     */
    public function setLayers(array $layers): self
    {
        ksort($layers);

        $layers = array_values($layers);

        $this->layers = [];

        foreach ($layers as $layer) {
            if (! $layer instanceof AbstractLayer) {
                $layer = AbstractLayer::make($layer);
            }

            $this->layers[] = $layer->scaleTo($this->width, $this->height);
        }

        return $this;
    }

    public function getLayers(): array
    {
        return array_map(function (AbstractLayer $layer) {
            return AbstractLayer::make($layer->toArray());
        }, $this->layers);
    }
}
