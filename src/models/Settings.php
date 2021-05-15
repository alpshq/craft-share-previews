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

//    public array $renderer = [
//        'content' => [
//            'padding' => [
//                'left' => 70,
//                'top' => 175,
//                'right' => 530,
//                'bottom' => 240,
//            ],
//        ],
//
//        'background' => [
//            'type' => 'image',
//            'type' => 'color',
//
//            'color' => [195,130,250],
//
//            'gradient' => [
//                'from' => [195,130,250],
//                'to' => [240,70,70],
//                'angle' => 45,
//            ],
//
//            'image' => [
//                'path' => 'share-previews/2.png',
//                'path' => null,
//            ],
//        ],
//
//        'image' => [
//            'public_path' => null,
//            'position' => 'right',
//            'max_width' => 430,
//            'max_height' => 630,
//            'border' => 15,
//            'border_color' => [255,255,255],
//
//            'padding' => [
//                'left' => 740,
//                'top' => 145,
//                'right' => 40,
//                'bottom' => 210,
//            ],
//        ],
//
//        'font' => [
//            'family' => 'Roboto',
//            'weight' => '700',
//            'color' => [50,50,50],
//            'size' => 60,
//        ],
//    ];

    public Image $image;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->fontCachePath = Craft::$app->path->getRuntimePath() . '/spfonts';

        $image = new Image([
//            'layers' => [
//                new ColorLayer([
//                    'color' => [0,0,0],
//                ]),
//            ],
        ]);

        $this->image = $image;
    }
}
