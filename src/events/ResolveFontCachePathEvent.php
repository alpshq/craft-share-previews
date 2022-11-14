<?php

namespace alps\sharepreviews\events;

use alps\sharepreviews\services\FileHandler;

/**
 * @property FileHandler $sender
 */
class ResolveFontCachePathEvent extends \yii\base\Event
{
    /** @var string|null the path where you want to cache fonts */
    public ?string $path = null;
}
