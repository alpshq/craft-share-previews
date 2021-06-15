<?php

namespace alps\sharepreviews\models;

use alps\sharepreviews\models\concerns\HasAssets;
use alps\sharepreviews\models\vendortemplates\AbstractVendorTemplate;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\elements\Asset;

class SetUp extends \craft\base\Model
{
    use HasAssets;

    const SCENARIO_START = 'start';

    const SCENARIO_ROUTE = 'route';

    const SCENARIO_TEMPLATE_SETTINGS = 'template-settings';

    const SCENARIO_TEMPLATES = 'templates';

    const SCENARIO_FINAL = 'final';

    public ?string $routePrefix = null;

    public ?string $siteName = null;

    public ?string $siteUrl = null;

    public ?int $fieldId = null;

    private ?int $logoId = null;

    private ?Asset $logo = null;

    private ?int $fallbackId = null;

    private ?Asset $fallback = null;

    public static function fromState(string $state, array $data = []): self
    {
        $json = base64_decode($state);

        $attributes = json_decode($json, true) ?? [];

        $attributes = array_merge($attributes, $data);

        return new self($attributes);
    }

    public function init()
    {
        parent::init();

        if ($this->scenario === self::SCENARIO_DEFAULT) {
            $this->scenario = self::SCENARIO_START;
        }

        $site = Craft::$app->sites->currentSite;

        if ($this->siteName === null) {
            $this->siteName = $site->name;
        }

        if ($this->siteUrl === null) {
            $this->siteUrl = parse_url($site->baseUrl, PHP_URL_HOST);
        }

        if ($this->routePrefix === null) {
            $this->routePrefix = SharePreviews::getInstance()->settings->routePrefix;
        }
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'logoId',
            'fallbackId',
            'scenario',
        ]);
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_START => [],
            self::SCENARIO_ROUTE => ['routePrefix'],
            self::SCENARIO_TEMPLATE_SETTINGS => [
                'siteName', 'siteUrl', 'logoId', 'fieldId', 'fallbackId',
            ],
            self::SCENARIO_TEMPLATES => [],
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['routePrefix', 'siteName', 'siteUrl', 'logoId', 'fieldId', 'fallbackId'], 'required'],
            ['routePrefix', 'validateRoutePrefix'],
            [['logoId', 'fallbackId'], 'validateAsset'],
            ['fieldId', 'validateFieldId'],
            ['siteName', 'string', 'length' => [1, 30]],
        ]);
    }

    public function validateRoutePrefix(string $attribute, $params, $validator)
    {
        $prefix = $this->{$attribute};

        $settings = SharePreviews::getInstance()->settings;

        $settings->routePrefix = $prefix;

        $attributeNameOnSettings = 'routePrefix';

        if ($settings->validate([$attributeNameOnSettings])) {
            return;
        }

        $this->addError($attribute, $settings->getFirstError($attributeNameOnSettings));
    }

    public function validateAsset(string $attribute, $params, $validator)
    {
        $assetId = $this->{$attribute};
        $asset = Asset::findOne($assetId);

        if (! $asset) {
            $this->addError(
                $attribute,
                Craft::t('share-previews', 'Invalid asset.'),
            );

            return;
        }

        if ($asset->kind !== Asset::KIND_IMAGE) {
            $this->addError(
                $attribute,
                Craft::t('share-previews', 'Image based asset required.'),
            );

            return;
        }
    }

    public function validateFieldId(string $attribute, $params, $validator)
    {
        $fieldId = $this->{$attribute};
        $field = Craft::$app->getFields()->getFieldById($fieldId);

        if ($field) {
            return;
        }

        $this->addError(
            $attribute,
            Craft::t('share-previews', 'Invalid field.'),
        );
    }

    public function toState(): string
    {
        $json = json_encode($this->toArray());

        return base64_encode($json);
    }

    public function getLogoId(): ?int
    {
        return $this->logoId;
    }

    /**
     * @param int|null $assetId
     */
    public function setLogoId($assetId)
    {
        $this->setAssetId('logo', $assetId);
    }

    public function getLogo(): ?Asset
    {
        return $this->getAsset('logo');
    }

    public function getFallbackId(): ?int
    {
        return $this->fallbackId;
    }

    /**
     * @param int|null $assetId
     */
    public function setFallbackId($assetId)
    {
        $this->setAssetId('fallback', $assetId);
    }

    public function getFallback(): ?Asset
    {
        return $this->getAsset('fallback');
    }

    public function getTemplates(): array
    {
        $templatesService = SharePreviews::getInstance()->templates;

        $templates = $templatesService->getVendorTemplates();

        $templates = array_map(function (AbstractVendorTemplate $template) {
            return $this->setUpTemplate($template);
        }, $templates);

        return $templates;
    }

    public function getAvailableAssetFieldsAsOptions(): array
    {
        return (new AssetLayer)->getAvailableAssetFieldsAsOptions(false);
    }

    private function setUpTemplate(AbstractVendorTemplate $template): AbstractVendorTemplate
    {
        $template->setVariables([
            'entry' => [
                'title' => 'This will be a title of one of your entries.',
            ],
        ]);

        $template->setUp([
            'siteName' => $this->siteName,
            'siteUrl' => $this->siteUrl,
            'logoId' => $this->logoId,
            'entryAssetFallbackId' => $this->fallbackId,
            'entryAssetFieldId' => $this->fieldId,
        ]);

        return $template;
    }
}
