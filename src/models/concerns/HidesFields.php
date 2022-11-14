<?php

namespace alps\sharepreviews\models\concerns;

trait HidesFields
{
    protected function getHiddenFields(): array
    {
        return [];
    }

    public function fields(): array
    {
        $fields = $this->attributes();
        $fields = $this->removeHiddenFields($fields);

        return array_combine($fields, $fields);
    }

    protected function removeHiddenFields(array $fields): array
    {
        $hidden = $this->getHiddenFields();

        $filtered = [];

        foreach ($fields as $fieldName) {
            if (! in_array($fieldName, $hidden)) {
                $filtered[] = $fieldName;
            }
        }

        return $filtered;
    }
}
