<?php

namespace alps\sharepreviews\controllers;

use alps\sharepreviews\fields\TemplateSelectField;
use alps\sharepreviews\models\Settings;
use alps\sharepreviews\models\SetUp;
use alps\sharepreviews\models\vendortemplates\AbstractVendorTemplate;
use alps\sharepreviews\SharePreviews;
use Craft;
use craft\web\Controller;
use craft\web\Request;

class SetUpController extends Controller
{
    public function actionIndex()
    {
        return $this->renderSetUp(new SetUp);
    }

    public function actionPost()
    {
        $model = SetUp::fromState(
            $this->request->getBodyParam('modelState', '{}'),
            $this->request->getBodyParam('model', [])
        );

        $op = $this->request->getBodyParam('op', '');

        if ($model->scenario === SetUp::SCENARIO_TEMPLATE_SETTINGS && $op === 'skip') {
            $model->scenario = SetUp::SCENARIO_FINAL;

            $this->hideSetUpNavigationItem();

            return $this->renderSetUp($model);
        }

        if (! $model->validate()) {
            return $this->renderSetUp($model);
        }

        if ($model->scenario === SetUp::SCENARIO_ROUTE) {
            $this->saveRoutePrefix($model);
        }

        if ($model->scenario === SetUp::SCENARIO_TEMPLATES) {
            $this->saveTemplates($model, $this->request);
        }

        $model = $this->setNextScenario($model);

        if ($model->scenario === SetUp::SCENARIO_FINAL) {
            $this->hideSetUpNavigationItem();
        }

        return $this->renderSetUp($model);
    }

    public function actionDownloadInstructions()
    {
        $body = $this->getInstructionsBody();

        return Craft::$app
            ->response
            ->sendContentAsFile($body, 'share-preview-instructions.txt');
    }

    private function renderSetUp(SetUp $model)
    {
        $plugin = SharePreviews::getInstance();

        $thankYouImageUrl = Craft::$app->assetManager->getPublishedUrl(
            '@share-previews/resources/imgs/fakurian-design-E8Ufcyxz514-unsplash.jpg',
            true
        );

        $iconImageUrl = Craft::$app->assetManager->getPublishedUrl(
            '@share-previews/resources/imgs/setup-wizard-icon.svg',
            true
        );

        $template = $model->scenario === SetUp::SCENARIO_START
            ? 'share-previews/setup/index'
            : 'share-previews/setup/step';

        return $this->renderTemplate($template, [
            'title' => Craft::t('share-previews', 'Share Previews: Set Up Wizard'),
            'fullPageForm' => true,
            'model' => $model,
            'settings' => $plugin->settings,
            'urls' => $plugin->urls,
            'instructionsEmailUrl' => $this->getInstructionsEmailUrl(),
            'templateSelectFieldName' => TemplateSelectField::displayName(),
            'thankYouImageUrl' => $thankYouImageUrl,
            'iconImageUrl' => $iconImageUrl,
        ]);
    }

    private function saveSettings(callable $closure): self
    {
        $plugin = SharePreviews::getInstance();

        $settings = $plugin->settings;

        $closure($settings);

        Craft::$app->plugins->savePluginSettings($plugin, $settings->toArray());

        return $this;
    }

    private function saveRoutePrefix(SetUp $model): self
    {
        $this->saveSettings(function (Settings $settings) use ($model) {
            $settings->routePrefix = $model->routePrefix;
        });

        return $this;
    }

    private function saveTemplates(SetUp $model, Request $request): self
    {
        $selected = $request->getBodyParam('import', []);
        $selected = array_map('intval', $selected);

        $templates = array_filter($model->getTemplates(), function (AbstractVendorTemplate $template) use ($selected) {
            return in_array($template->id, $selected);
        });

        $templatesService = SharePreviews::getInstance()->templates;

        foreach ($templates as $template) {
            $template->id = null;
            $templatesService->saveTemplate($template);
        }

        return $this;
    }

    private function hideSetUpNavigationItem()
    {
        $this->saveSettings(function (Settings $settings) {
            $settings->showSetUpNavigationItemInCp = false;
        });

        return $this;
    }

    private function setNextScenario(SetUp $model): SetUp
    {
        $map = [
            SetUp::SCENARIO_START => SetUp::SCENARIO_ROUTE,
            SetUp::SCENARIO_ROUTE => SetUp::SCENARIO_TEMPLATE_SETTINGS,
            SetUp::SCENARIO_TEMPLATE_SETTINGS => SetUp::SCENARIO_TEMPLATES,
            SetUp::SCENARIO_TEMPLATES => SetUp::SCENARIO_FINAL,
        ];

        $next = $map[$model->scenario] ?? SetUp::SCENARIO_DEFAULT;

        $model->scenario = $next;

        return $model;
    }

    private function getInstructionsEmailUrl(): string
    {
        $subject = Craft::t('share-previews', 'Adding Share Previews to our templates');
        $body = $this->getInstructionsBody();

        return SharePreviews::getInstance()->urls->email(null, $subject, $body);
    }

    private function getInstructionsBody(): string
    {
        return Craft::t('share-previews', 'Recently we\'ve added the Share Previews plugin to our Craft site. 

The plugin is meant to generate text based share preview images. 
There are a few steps required we want you to help us with.

# Entry adjustments

Follow these steps to enable the selection of share preview templates in our entries:

- [Create a new field]({newFieldUrl}) with a field type of `{fieldType}` -- there are no special requirements 
- Add the newly created field to all the entry types which have public links

# Twig templates

Please add the following code to the `head` tag of each page:

```
<meta name="twitter:card" content="summary_large_image"> 
<meta name="twitter:image" content="{{ entry.getSharePreviewUrl() }}"> 
<meta property="og:image" content="{{ entry.getSharePreviewUrl() }}">
```

# Are we using GraphQL?

You can fetch the preview URL of an entry using the `sharePreviewUrl` field.
After fetching the URL you need to add it to your `head` tag as you\'d normally do it with Twig. The exact method varies between frontend frameworks.

```
query {
    entries {
        id
        title
        sharePreviewUrl
    }
}
```

If you require additional help feel free to reach out to the plugin creators:

- If you encounter **any issues**, submit them in the plugin\'s repository: {issueUrl}
- If you need **additional support**, contact the creators by email at support@alps.dev

Thank you!', [
            'newFieldUrl' => SharePreviews::getInstance()->urls->createNewField(),
            'issueUrl' => SharePreviews::getInstance()->urls->issueUrl(),
            'fieldType' => TemplateSelectField::displayName(),
        ]);
    }
}
