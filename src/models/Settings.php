<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\Config;
use Craft;

class Settings extends \craft\base\Model
{
    public string $routePrefix = 'previews';

    public bool $ensureGitignoreExists = true;

    public bool $disableImageCache = true;

    public string $fontCachePath = '';

    private array $templates = [];

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->fontCachePath = Craft::$app->path->getRuntimePath() . '/spfonts';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
           'templates',
        ]);
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function setTemplates(array $templates): self
    {
//        $templates[0]['layers'][0] = array_merge($templates[0]['layers'][0], [
//            'color' => 'fff',
//            'colorOpacity' => 50,
//            'paddingTop' => '12',
//            'borderRadius' => '5',
//            'colorOpacity' => '50',
//        ]);
//
//        $templates[0]['layers'][4] = array_merge($templates[0]['layers'][4], [
//            'type' => 'text',
////            'color' => 'fff',
////            'colorOpacity' => 50,
////            'paddingTop' => '12',
////            'borderRadius' => '5',
////            'colorOpacity' => '50',
//        ]);
//
//        $templates[0]['layers'] = [$templates[0]['layers'][4]];
//
//        dd(
//            $templates[0]['layers'][2],
//            (new Image($templates[0]))->layers[2],
//            (new Image($templates[0]))->layers[2]->toArray(),
////            (new Image($templates[0]))->layers[2]->from,
////            (new Image($templates[0]))->layers[2]->fromOpacity,
//        );


        $this->templates = array_map(function($template) {
            $layers = $template['layers'] ?? [];

            ksort($layers);

            $layers = array_values($layers);

            $template['layers'] = $layers;

            return new Image($template);
        }, $templates);

//        dd($_POST['settings'], $templates[0]['layers'], $this->templates[0]->layers, $this->toArray());

        return $this;
    }
}
