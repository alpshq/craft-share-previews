<?php

namespace alps\sharepreviews\twig;

use Craft;
use craft\elements\Entry;
use alps\sharepreviews\Plugin;
use Twig\Environment;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TabsExtension extends \Twig\Extension\AbstractExtension
{
    private array $tabs = [];

    public function getFunctions(): array
    {
        return [
            // Use a closure or define the function outside with [$this, 'nameOfTheFunction'].
            new TwigFunction('tabs_create', [$this, 'create']),
            new TwigFunction('tabs_render', [$this, 'render'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('tabs_section', [$this, 'setSection']),
        ];
    }

    public function create(): int
    {
        $this->tabs[]= [];

        return count($this->tabs) - 1;
    }

    public function setSection(Markup $markup, int $id, string $name)
    {
        $this->tabs[$id][]= [
            'name' => $name,
            'markup' => $markup,
        ];

        return null;
    }

    public function render(Environment $env, int $id, int $selected = null)
    {
        $data = [
            'id' => $id,
            'sections' => $this->tabs[$id],
            'selected' => $this->getSelectedSection($id, $selected),
        ];

        return $env->render('share-previews/_tabs', $data);
    }

    private function getSelectedSection(int $id, ?int $selected): int
    {
        if ($selected !== null) {
            return $selected;
        }

        $request = Craft::$app->request;
        $param = $request->getParam('_tabs', []);

        return (int) ($param[$id] ?? 0);
    }
}
