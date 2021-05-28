<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\Settings;
use alps\sharepreviews\models\Template;
use alps\sharepreviews\SharePreviews;
use craft\elements\Asset;
use craft\elements\Entry;
use alps\sharepreviews\Config;
use craft\helpers\UrlHelper;
use yii\base\Component;

class Urls extends Component
{
    private Settings $settings;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->settings = SharePreviews::getInstance()->settings;
    }

    public function createUrl(string $data): string
    {
        return sprintf(
            '%s/%s/%s.png',
            rtrim(UrlHelper::baseCpUrl(), '/'),
            $this->settings->routePrefix,
            $data
        );
    }

//    public function settings(): string
//    {
//        return UrlHelper::cpUrl('settings/plugins/share-previews');
//    }

    /**
     * @param Template|int $templateOrId
     */
    public function templateEditor($templateOrId): string
    {
        $id = $templateOrId instanceof Template ? $templateOrId->id : $templateOrId;

        return UrlHelper::cpUrl('share-previews/template-editor', [
            'id' => $id,
        ]);
    }
}
