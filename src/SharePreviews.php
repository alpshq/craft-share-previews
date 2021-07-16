<?php

namespace alps\sharepreviews;

use alps\sharepreviews\assets\ControlPanelAssets;
use alps\sharepreviews\assets\FontAwesomeAssets;
use alps\sharepreviews\behaviors\PreviewableEntryBehavior;
use alps\sharepreviews\fields\TemplateSelectField;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\services\FileHandler;
use alps\sharepreviews\services\Fonts;
use alps\sharepreviews\services\Helpers;
use alps\sharepreviews\services\ImageDiffer;
use alps\sharepreviews\services\Images;
use alps\sharepreviews\services\Templates;
use alps\sharepreviews\services\Urls;
use alps\sharepreviews\twig\PreviewExtension;
use alps\sharepreviews\twig\TabsExtension;
use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\DefineGqlTypeFieldsEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\gql\TypeManager;
use craft\helpers\FileHelper;
use craft\services\Fields;
use craft\utilities\ClearCaches;
use craft\web\twig\variables\Cp;
use craft\web\UrlManager;
use craft\web\View;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use yii\base\Event;

/**
 * @property FileHandler $fileHandler
 * @property Fonts       $fonts
 * @property Helpers     $helpers
 * @property ImageDiffer $imageDiffer
 * @property Images      $images
 * @property Settings    $settings
 * @property Templates   $templates
 * @property Urls        $urls
 *
 * @method Settings getSettings
 */
class SharePreviews extends Plugin
{
    public $hasCpSettings = true;

    public function init()
    {
        Craft::setAlias('@share-previews', __DIR__);

        parent::init();

        $this->setComponents([
            'fileHandler' => FileHandler::class,
            'fonts' => Fonts::class,
            'helpers' => Helpers::class,
            'imageDiffer' => ImageDiffer::class,
            'images' => Images::class,
            'templates' => Templates::class,
            'urls' => Urls::class,
        ]);

        $this
            ->registerRoutes()
            ->registerTemplateRoots()
            ->registerTwigVariables()
            ->registerCpAssets()
            ->registerFields()
            ->registerBehaviors()
            ->registerCacheUtility()
            ->registerGraphQlField()
            ->registerSetUpWizardNavigationItem();
    }

    protected function createSettingsModel()
    {
        return new Settings;
    }

    protected function settingsHtml()
    {
        $customFontsPath = $this->fonts->getPathToCustomFonts();

        return Craft::$app->getView()->renderTemplate('share-previews/settings', [
            'settings' => $this->getSettings(),
            'templateSelectFieldName' => TemplateSelectField::displayName(),
            'urls' => $this->urls,
            'customFonts' => $this->fonts->getCustomFonts(),
            'customFontsFullPath' => $customFontsPath,
            'customFontsNumberOfScannedFiles' => $customFontsPath
                ? $this->fileHandler->getNumberOfFilesAndDirectories($customFontsPath)
                : 0,
        ]);
    }

    private function registerRoutes(): self
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $path = sprintf(
                    '/%s/<data:.*>.png',
                    self::getSettings()->routePrefix,
                );

                $event->rules = array_merge($event->rules, [
                    $path => 'share-previews/preview',
                ]);
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge($event->rules, [
                    'POST share-previews/template-editor' => 'share-previews/template-editor/post',
                    'GET share-previews/template-editor' => 'share-previews/template-editor/edit',
                    'POST share-previews/template-editor/preview' => 'share-previews/template-editor/preview',

                    'GET share-previews/setup' => 'share-previews/set-up/index',
                    'POST share-previews/setup' => 'share-previews/set-up/post',
                    'GET share-previews/setup/instructions' => 'share-previews/set-up/download-instructions',
                ]);
            }
        );

        return $this;
    }

    private function registerTemplateRoots(): self
    {
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $event->roots = array_merge($event->roots, [
                    'share-previews' => __DIR__ . '/templates',
                ]);
            }
        );

        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $event->roots = array_merge($event->roots, [
                    'share-previews' => __DIR__ . '/templates',
                ]);
            }
        );

        return $this;
    }

    private function registerTwigVariables(): self
    {
        $view = Craft::$app->getView();

        $view->registerTwigExtension(new PreviewExtension);
        $view->registerTwigExtension(new TabsExtension);

        return $this;
    }

    private function registerCpAssets(): self
    {
        $isCpRequest = Craft::$app->getRequest()->getIsCpRequest();

        if (! $isCpRequest) {
            return $this;
        }

        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_TEMPLATE,
            function (TemplateEvent $event) {
                $view = Craft::$app->getView();

                $view->registerAssetBundle(ControlPanelAssets::class);
                $view->registerAssetBundle(FontAwesomeAssets::class);
            }
        );

        return $this;
    }

    private function registerFields(): self
    {
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types = array_merge($event->types, [
                    TemplateSelectField::class,
                ]);
            }
        );

        return $this;
    }

    private function registerBehaviors(): self
    {
        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_BEHAVIORS,
            function (DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    PreviewableEntryBehavior::class,
                ]);
            }
        );

        return $this;
    }

    private function registerCacheUtility(): self
    {
        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    'key' => 'sharepreviews-images',
                    'label' => Craft::t('share-previews', 'Share Previews'),
                    'info' => Craft::t('share-previews', 'Contents of `{path}`', [
                        'path' => 'web/' . $this->settings->routePrefix . '/',
                    ]),
                    'action' => function () {
                        FileHelper::clearDirectory($this->fileHandler->getImageDirectory(), [
                            'except' => ['.gitignore'],
                        ]);
                    },
                ];
            },
        );

        return $this;
    }

    private function registerGraphQlField(): self
    {
        Event::on(
            TypeManager::class,
            TypeManager::EVENT_DEFINE_GQL_TYPE_FIELDS,
            function (DefineGqlTypeFieldsEvent $event) {
                if ($event->typeName !== 'EntryInterface') {
                    return;
                }

                $event->fields['sharePreviewUrl'] = [
                    'name' => 'sharePreviewUrl',
                    'type' => Type::string(),
                    'description' => Craft::t('share-previews', 'The URL to the entry\'s generated share preview image.'),
                    'resolve' => function ($source, array $arguments, $context, ResolveInfo $resolveInfo) {
                        return $source->getSharePreviewUrl();
                    },
                ];
            },
        );

        return $this;
    }

    private function registerSetUpWizardNavigationItem(): self
    {
        if (! $this->settings->showSetUpNavigationItemInCp) {
            return $this;
        }

        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function (RegisterCpNavItemsEvent $event) {
                $event->navItems = array_merge($event->navItems, [
                    [
                        'url' => $this->urls->setUp(),
                        'label' => Craft::t('share-previews', 'Share Previews'),
                        'badgeCount' => 'SETUP',
                        'icon' => '@share-previews/resources/imgs/setup-wizard-icon.svg',
                    ],
                ]);
            },
        );

        return $this;
    }
}
