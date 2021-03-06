<?php

namespace alps\sharepreviews\services;

use alps\sharepreviews\models\Settings;
use alps\sharepreviews\models\Template;
use alps\sharepreviews\SharePreviews;
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

    public function settings(): string
    {
        return UrlHelper::cpUrl('settings/plugins/share-previews');
    }

    /**
     * @param Template|int $templateOrId
     */
    public function templateEditor($templateOrId = null): string
    {
        if ($templateOrId === null) {
            return UrlHelper::cpUrl('share-previews/template-editor');
        }

        $id = $templateOrId instanceof Template ? $templateOrId->id : $templateOrId;

        return UrlHelper::cpUrl('share-previews/template-editor', [
            'id' => $id,
        ]);
    }

    public function createNewField(): string
    {
        return UrlHelper::cpUrl('settings/fields/new');
    }

    public function cachesUtility(): string
    {
        return UrlHelper::cpUrl('utilities/clear-caches');
    }

    public function setUp(): string
    {
        return UrlHelper::cpUrl('share-previews/setup');
    }

    public function setUpInstructionsDownload(): string
    {
        return UrlHelper::cpUrl('share-previews/setup/instructions');
    }

    public function email(?string $to, string $subject = null, $body = null): string
    {
        if (is_array($body)) {
            $body = implode("\n", $body);
        }

        if ($body !== null) {
            $body = (string) $body;
        }

        $params = array_filter([
            'subject' => $subject,
            'body' => $body,
        ]);

        $query = [];

        foreach ($params as $key => $value) {
            $query[] = sprintf('%s=%s', $key, rawurlencode($value));
        }

        $query = implode('&', $query);

        return sprintf('mailto:%s?%s', $to, $query);
    }

    public function issueUrl(): string
    {
        return 'https://github.com/alpshq/craft-share-previews/issues';
    }
}
