<?php

namespace alps\sharepreviews\controllers;

use alps\Module;
use alps\sharepreviews\models\AbstractLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\ImageLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;
use alps\sharepreviews\SharePreviews;
use craft\elements\Entry;
use alps\sharepreviews\Config;
use alps\sharepreviews\services\FileHandler;
use alps\sharepreviews\services\FontFetcher;
use alps\sharepreviews\Plugin;
use alps\sharepreviews\services\Renderer;
use alps\youtube\Client;
use Craft;
use craft\web\Controller;
use League\OAuth2\Client\Provider\Google;
use yii\web\HttpException;

class TemplateEditorController extends Controller
{
    public function actionEdit()
    {
        $template = SharePreviews::getInstance()->getSettings()->getTemplates()[0];

        return $this->renderEditor($template, $this->request->getParam('id'));
    }

    public function actionPost()
    {
        $shouldCancel = $this->request->getParam('cancel', false) !== false;

        if ($shouldCancel) {
            return $this->redirectToPostedUrl();
        }

        $id = $this->request->getBodyParam('templateId');
        $template = $this->request->getBodyParam('template', []);

        $layers = $template['layers'] ?? [];

        ksort($layers);

        $layers = $this->removeLayers($layers);
        $layers = $this->addLayers($layers);
        $layers = $this->moveLayers($layers);

        $template['layers'] = $layers;
        $template = new Image($template);

        $shouldSave = $this->request->getParam('save', false) !== false;

        if ($shouldSave) {
            return $this->handleSave($id, $template);
        }

        return $this->renderEditor($template, $id);
    }

    private function renderEditor(Image $template, ?int $id)
    {
        return $this->renderTemplate('share-previews/template-editor/edit', [
            'template' => $template,
            'availableLayers' => $this->getAvailableLayers(),
            'id' => $id,
        ]);
    }

    private function removeLayers(array $layers): array
    {
        return array_filter($layers, function($layer) {
            return array_key_exists('delete', $layer) === false;
        });
    }

    private function addLayers(array $layers): array
    {
        $add = $this->request->getBodyParam('addLayer');

        if (!$add) {
            return $layers;
        }

        [$type, $position] = explode('|', $add);
        $position = (int) $position;

        array_splice($layers, $position, 0, [
            ['type' => $type],
        ]);

        return $layers;
    }

    private function moveLayers(array $layers): array
    {
        do {
            $itemsToMove = array_filter($layers, function($layer) {
                return array_key_exists('move', $layer);
            });

            if (empty($itemsToMove)) {
                continue;
            }

            $idx = array_keys($itemsToMove)[0];

            $move = $layers[$idx]['move'];
            $movingLayers = array_splice($layers, $idx, 1);
            unset($movingLayers[0]['move']);
            $newIdx = $move === 'up' ? $idx + 1 : $idx - 1;

            if ($newIdx < 0) {
                $newIdx = 0;
            }

            array_splice($layers, $newIdx, 0, $movingLayers);
        } while (!empty($itemsToMove));


        return $layers;
    }

    private function handleSave(int $id, Image $template)
    {
        $pluginService = Craft::$app->getPlugins();
        $plugin = SharePreviews::getInstance();

        $settings = $plugin->getSettings();

        $templates = $settings->getTemplates();
        $templates[$id] = $template;
        $settings->templates = array_values($templates);

        $pluginService->savePluginSettings($plugin, $settings->toArray());

        $message = Craft::t('share-previews', 'Template saved.');

        $this->setSuccessFlash($message);

        return $this->redirectToPostedUrl();
    }

    private function getAvailableLayers()
    {
        $layers = array_values(AbstractLayer::getTypes());

        $layers = array_map(function(string $className) {
            $instance = new $className;

            return [
                'type' => $instance->type,
                'name' => $instance->getTitle(),
                'available' => $instance->isAvailableInTemplateEditor(),
            ];
        }, $layers);

        $layers = array_filter($layers, function(array $layer) {
            return $layer['available'];
        });

        return array_values($layers);
    }
}
