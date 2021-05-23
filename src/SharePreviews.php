<?php

namespace alps\sharepreviews;

use alps\sharepreviews\behaviors\PreviewableEntryBehavior;
use alps\sharepreviews\models\Image;
use alps\sharepreviews\services\ImageDiffer;
use alps\sharepreviews\services\Urls;
use alps\sharepreviews\twig\TabsExtension;
use Craft;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\services\Fields;
use craft\web\UrlManager;
use craft\web\View;
use modules\Module;
use alps\sharepreviews\assets\ControlPanelAssets;
use alps\sharepreviews\fields\TemplateSelectorField;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\services\FileHandler;
use alps\sharepreviews\services\FontFetcher;
use alps\sharepreviews\services\Images;
use alps\sharepreviews\services\Renderer;
use alps\sharepreviews\services\Templates;
use alps\sharepreviews\twig\PreviewExtension;
use yii\base\Event;
use yii\console\Application as ConsoleApplication;

/**
 * @property-read Settings    $settings
 * @property-read FileHandler $fileHandler
 * @property-read FontFetcher $fontFetcher
 * @property-read Images      $images
 * @property-read Renderer    $renderer
 * @property-read Templates   $templates
 * @property-read ImageDiffer $imageDiffer
 * @property-read Urls $urls
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
            'fontFetcher' => FontFetcher::class,
            'images' => Images::class,
            'renderer' => Renderer::class,
            'templates' => Templates::class,
            'imageDiffer' => ImageDiffer::class,
            'urls' => Urls::class,
        ]);

        $this
            ->registerRoutes()
            ->registerTemplateRoots()
            ->registerTwigVariables()
            ->registerCpAssets()
            ->registerFields()
            ->registerBehaviors();
    }

    protected function createSettingsModel()
    {
        return new Settings;
    }

    protected function settingsHtml()
    {
//        dd($this->getSettings()->templates[0]->layers[0]);
        return Craft::$app->getView()->renderTemplate('share-previews/settings', [
            'settings' => $this->getSettings(),
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
                    'share-previews/draft' => 'share-previews/preview/draft',
                    'POST share-previews/template-editor' => 'share-previews/template-editor/post',
                    'GET share-previews/template-editor' => 'share-previews/template-editor/edit',
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

        if (!$isCpRequest) {
            return $this;
        }

        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_TEMPLATE,
            function(TemplateEvent $event) {
                $view = Craft::$app->getView();

                $view->registerAssetBundle(ControlPanelAssets::class);
            }
        );

        return $this;
    }

    private function registerFields(): self
    {
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types = array_merge($event->types, [
                    TemplateSelectorField::class,
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
            function(DefineBehaviorsEvent $event) {
                $event->sender->attachBehaviors([
                    PreviewableEntryBehavior::class,
                ]);
            }
        );

        return $this;
    }
}
