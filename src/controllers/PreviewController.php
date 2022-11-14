<?php

namespace alps\sharepreviews\controllers;

use alps\sharepreviews\models\Image;
use alps\sharepreviews\SharePreviews;
use craft\web\Controller;
use yii\web\Response;

class PreviewController extends Controller
{
    public int|bool|array $allowAnonymous = true;

    public function actionIndex(string $data)
    {
        $plugin = SharePreviews::getInstance();

        $image = Image::fromEncodedDiff($data);

        $settings = $plugin->getSettings();

        $image = $image->render();

        if ($settings->disableImageCache !== true) {
            $plugin->fileHandler->saveImage($image, $data);
        }

        $this->response->headers->add('Content-Type', 'image/png');
        $this->response->format = Response::FORMAT_RAW;

        return $image->get('png', [
            'png_compression_level' => Image::PNG_COMPRESSION_LEVEL,
        ]);
    }
}
