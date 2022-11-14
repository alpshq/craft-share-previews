<?php

namespace alps\sharepreviews\events;

use alps\sharepreviews\models\Image;

/**
 * @property Image $sender
 */
class ImageBeforeRenderEvent extends \yii\base\Event
{
}
