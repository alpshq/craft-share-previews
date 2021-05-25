<?php

namespace alps\sharepreviews\twig;

use Craft;
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

    public function create($id = null)
    {
        if ($id === null) {
            $id = count($this->tabs) - 1;
        }

        if (is_numeric($id)) {
            $id = (int) $id;
        }

        $this->tabs[$id]= [];

        return $id;
    }

    public function setSection(Markup $markup, $id, string $name)
    {
        $this->tabs[$id][]= [
            'name' => $name,
            'markup' => $markup,
        ];

        return null;
    }

    public function render(Environment $env, $id, int $selected = null)
    {
        $data = [
            'id' => $id,
            'sections' => $this->tabs[$id],
            'selected' => $this->getSelectedSection($id, $selected),
        ];

        return $env->render('share-previews/_tabs', $data);
    }

    private function getSelectedSection($id, $selected)
    {
        if ($selected !== null) {
            return $selected;
        }

        $request = Craft::$app->request;
        $param = $request->getParam('_tabs', []);

        $selected = $param[$id] ?? 0;

        return isset($this->tabs[$id][$selected]) ? $selected : 0;
    }
}
