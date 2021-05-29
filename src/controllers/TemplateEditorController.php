<?php

namespace alps\sharepreviews\controllers;

use alps\Module;
use alps\sharepreviews\models\AbstractLayer;
use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\ImageLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\Template;
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
use craft\helpers\UrlHelper;
use craft\web\Controller;
use craft\web\Request;
use craft\web\Response;
use League\OAuth2\Client\Provider\Google;
use yii\web\HttpException;
use yii\web\JqueryAsset;

class TemplateEditorController extends Controller
{
    public function actionEdit()
    {
        $id = (int) $this->request->getParam('id', 0);

        $template = SharePreviews::getInstance()
            ->templates
            ->getTemplateById($id);

        $f = SharePreviews::getInstance()->fontFetcher;



        $template = $template ?? new Template;

        return $this->renderEditor($template);
    }

    public function actionPost()
    {
        $op = $this->request->getBodyParam('op', '');

        if ($op === 'cancel') {
            return $this->redirectToPostedUrl();
        }

        $template = $this->createTemplateFromParam(
            $this->request->getBodyParam('template', [])
        );

        if ($op === 'save') {
            return $this->handleSave($template);
        }

        if ($op === 'delete') {
            return $this->handleDelete($template);
        }

        if ($op === 'duplicate') {
            return $this->handleDuplicate($template);
        }

        return $this->renderEditor($template);
    }

    public function actionPreview()
    {
        $template = $this->createTemplateFromParam(
            $this->request->getBodyParam('template', [])
        );

        $image = $template->toImage();

        $preview = $this->request->getBodyParam('preview');

        $image = $this->attachEntry($image, $preview);

        $image->render()->show('png', [
            'png_compression_level' => Renderer::PNG_COMPRESSION_LEVEL,
        ]);
    }

    private function createTemplateFromParam(array $template): Template
    {
        $layers = $template['layers'] ?? [];

        ksort($layers);

        $layers = $this->removeLayers($layers);
        $layers = $this->addLayers($layers);
        $layers = $this->moveLayers($layers);

        $template['layers'] = $layers;

        return new Template($template);
    }

    private function renderEditor(Template $template)
    {
        $data = [
            'template' => $template,
            'availableLayers' => $this->getAvailableLayers(),
        ];

        $isFetch = $this->request->getHeaders()->get('x-requested-with') === 'fetch';

        if (!$isFetch) {
            return $this->renderTemplate('share-previews/template-editor/edit', $data);
        }

        $view = $this->getView();

        $view->startJsBuffer();

        $html = $view->renderTemplate('share-previews/template-editor/_fields', $data);

        $js = $view->clearJsBuffer(false);

        return $this->asJson([
            'html' => $html,
            'js' => !empty($js) ? $js : null,
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

    private function handleSave(Template $template)
    {
        SharePreviews::getInstance()->templates->saveTemplate($template);

        $message = Craft::t('share-previews', 'Template "{name}" saved.', [
            'name' => $template->getHumanFriendlyName(),
        ]);

        $this->setSuccessFlash($message);

        return $this->redirectToPostedUrl();
    }

    private function handleDelete(Template $template)
    {
        SharePreviews::getInstance()->templates->deleteTemplateById($template->id);

        $message = Craft::t('share-previews', 'Template "{name}" deleted.', [
            'name' => $template->getHumanFriendlyName(),
        ]);

        $this->setSuccessFlash($message);

        return $this->redirectToPostedUrl();
    }

    private function handleDuplicate(Template $template)
    {
        $template->id = null;
        $template->isDefault = false;

        $originalName = $template->getHumanFriendlyName();

        $template->name = Craft::t('share-previews', '{name} - copy', [
            'name' => $originalName,
        ]);

        $plugin = SharePreviews::getInstance();
        $plugin->templates->saveTemplate($template);

        $message = Craft::t('share-previews', 'Template "{name}" duplicated.', [
            'name' => $originalName,
        ]);

        $this->setSuccessFlash($message);

        return $this->redirect(
            $plugin->urls->templateEditor($template)
        );
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

    private function attachEntry(Image $image, ?array $preview): Image
    {
        if (!$preview) {
            return $image;
        }

        $entryId = (int) ($preview['entryId'][0] ?? 0);

        if ($entryId < 1) {
            return $image;
        }

        $entry = Entry::findOne($entryId);

        if (!$entry) {
            return $image;
        }

        $image->setEntry($entry);

        return $image;
    }
}
