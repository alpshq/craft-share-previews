<?php

namespace alps\sharepreviews\controllers;

use alps\sharepreviews\models\ColorLayer;
use alps\sharepreviews\models\GradientLayer;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\models\ImageLayer;
use alps\sharepreviews\models\LineLayer;
use alps\sharepreviews\models\TextLayer;
use alps\sharepreviews\SharePreviews;
use craft\elements\Entry;
use alps\sharepreviews\services\FileHandler;
use alps\sharepreviews\services\Fonts;
use Craft;
use craft\web\Controller;
use yii\web\HttpException;

class PreviewController extends Controller
{
    public $allowAnonymous = true;

    public function actionIndex(string $data)
    {
        $plugin = SharePreviews::getInstance();

        $image = Image::fromEncodedDiff($data);

        $settings = $plugin->getSettings();

        $image = $image->render();

        if ($settings->disableImageCache !== true) {
            $plugin->fileHandler->saveImage($image, $data);
        }

        $image->show('png', [
            'png_compression_level' => Image::PNG_COMPRESSION_LEVEL,
        ]);
    }
}
