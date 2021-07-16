<?php

namespace alps\sharepreviews\models\fonts;

class CustomFontVariant extends \alps\sharepreviews\models\fonts\AbstractFontVariant
{
    public string $filepath;

    public function getPathToFontFile(): string
    {
        return $this->filepath;
    }
}
