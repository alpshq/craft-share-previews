<?php

namespace alps\sharepreviews\services;

use yii\base\Component;

class Helpers extends Component
{
    private function clean($str)
    {
        // Source: http://stackoverflow.com/a/3650743/1402176

        return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1' . chr(255) . '$2', htmlentities($str, ENT_QUOTES, 'UTF-8'));
    }

    public function sortOptions(array $input, callable $closure = null): array
    {
        usort($input, function ($a, $b) use ($closure) {
            $comparison = strcmp($this->clean($a['label']), $this->clean($b['label']));

            if ($closure === null) {
                return $comparison;
            }

            return $closure($a, $b, $comparison);
        });

        return $input;
    }
}
