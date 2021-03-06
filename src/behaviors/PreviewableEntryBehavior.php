<?php

namespace alps\sharepreviews\behaviors;

use alps\sharepreviews\SharePreviews;

class PreviewableEntryBehavior extends \yii\base\Behavior
{
    public $owner;

    public function getSharePreviewUrl(): string
    {
        return SharePreviews::getInstance()
            ->images
            ->createImageFromEntry($this->owner)
            ->getUrl();
    }
}
