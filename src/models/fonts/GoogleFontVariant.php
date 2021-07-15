<?php

namespace alps\sharepreviews\models\fonts;

use alps\sharepreviews\SharePreviews;

class GoogleFontVariant extends \alps\sharepreviews\models\fonts\AbstractFontVariant
{
    public string $url;

    public function getPathToFontFile(): string
    {
        $fileHandler = SharePreviews::getInstance()->fileHandler;

        if ($fileHandler->fontExists($this)) {
            return $fileHandler->getFontPath($this);
        }

        $contents = file_get_contents($this->url);

        return $fileHandler
            ->saveFont($this, $contents)
            ->getFontPath($this);
    }
}
