<?php

namespace alps\sharepreviews\behaviors;

use alps\sharepreviews\SharePreviews;

class PreviewableEntryBehavior extends \yii\base\Behavior
{
    public $owner;

    public function getSharePreviewUrl(): string
    {
        $config = SharePreviews::getInstance()->images->createConfigFromEntry($this->owner);

        return $config->getUrl();
    }
}