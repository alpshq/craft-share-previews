<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\AbstractLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\ImageLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\Template;
use alps\sharepreviews\models\TextLayer;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\elements\Asset;
use craft\elements\Entry;
use yii\base\Component;

class ImageDiffer extends Component
{
    private Templates $templatesService;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->templatesService = SharePreviews::getInstance()->templates;
    }

    public function getDiff(Image $image): array
    {
        if ($image->templateId === null) {
            return array_filter($image->toArray());
        }

        $template = $this
            ->templatesService
            ->getTemplateById($image->templateId) ?? new Template;

        $template = $template->toImage()->toArray();
        $image = $image->toArray();

        $diff = [
            'templateId' => $image['templateId'],
        ];

        if ($template['width'] !== $image['width']) {
            $diff['width'] = $image['width'];
        }

        if ($template['height'] !== $image['height']) {
            $diff['height'] = $image['height'];
        }

        $layers = [];

        foreach ($template['layers'] as $idx => $firstLayer) {
            $secondLayer = $image['layers'][$idx] ?? null;

            if ($secondLayer === null) {
                $layers[]= null;
                continue;
            }

            $diffedLayer = [];

            foreach ($firstLayer as $prop => $baseValue) {
                if ($baseValue === $secondLayer[$prop]) {
                    continue;
                }

                $diffedLayer[$prop] = $secondLayer[$prop];
            }

            $layers[]= $diffedLayer;
        }

        $diff['layers'] = array_filter($layers);

        return $diff;
    }

    public function createFromDiff(array $diff): Image
    {
        $templatesService = $this->templatesService;

        $image = null;

        $templateId = $diff['templateId'] ?? null;

        $template = null;

        if ($templateId) {
            $template = $templatesService->getTemplateById((int) $templateId);
        }

        $image = ($template ?? new Template)->toImage();

        $image->width = $diff['width'] ?? $image->width;
        $image->height = $diff['height'] ?? $image->height;

        $diffLayers = $diff['layers'] ?? [];

        if (empty($diffLayers)) {
            return $image;
        }

        $layers = $image->layers;

        foreach ($diffLayers as $idx => $diffLayer) {
            $baseLayer = $layers[$idx] ?? AbstractLayer::make($diffLayer);

            unset($diffLayer['type']);

            $layers[$idx] = $this->createLayerFromDiff($baseLayer, $diffLayer);
        }

        $image->layers = $layers;

        return $image;
    }

    private function createLayerFromDiff(AbstractLayer $layer, $diff): AbstractLayer
    {
        foreach ($diff as $prop => $value) {
            if ($layer->hasProperty($prop)) {
                $layer->{$prop} = $value;
            }
        }

        return $layer;
    }

    public function encodeDiff(string $templateHash, array $diff): string
    {
        $zlibAvailable = extension_loaded('zlib');

        $diff = json_encode($diff);

        if ($zlibAvailable) {
            $diff = gzdeflate($diff, 9);
        }

        $base64 = base64_encode($diff);

        if ($zlibAvailable) {
            $base64 = 'g.' . $base64;
        }

        $base64 = str_replace(['+', '/', '='], ['-', '_', ''], $base64);
        $base64 = str_split($base64, 128);
        $base64 = implode('/', $base64);

        return $templateHash . '/' . $base64;
    }

    public function decodeDiff(string $diff): array
    {
        $parts = explode('/', $diff);

        array_shift($parts);

        $base64 = implode('', $parts);

        $base64 = str_replace(['-', '_'], ['+', '/'], $base64);

        $compressed = substr($base64, 0, 2) === 'g.';

        if ($compressed) {
            $base64 = substr($base64, 2);
        }

        $json = base64_decode($base64);

        if ($compressed) {
            $json = gzinflate($json);
        }

        return json_decode($json, true) ?? [];
    }
}
