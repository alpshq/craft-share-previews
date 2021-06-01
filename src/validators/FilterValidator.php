<?php

namespace alps\sharepreviews\validators;

use DateTime;
use yii\base\InvalidConfigException;

class FilterValidator extends \yii\validators\Validator
{
    /** @var string */
    const TYPE_INTEGER = 'int';

    /** @var string */
    const TYPE_FLOAT = 'float';

    /** @var string */
    const TYPE_BOOLEAN = 'bool';

    /** @var string */
    const TYPE_STRING = 'string';

    /** @var string */
    public $type = self::TYPE_STRING;

    /** @var bool */
    public $nullable = true;

    /** @var bool */
    public $trim = true;

    public $skipOnEmpty = false;

    public function init()
    {
        parent::init();

        if (! $this->type) {
            throw new InvalidConfigException('The "type" property must be set.');
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        $value = $this->applyTrim($value);
        $value = $this->applyNullable($value);
        $value = $this->filter($value);

        if ($this->nullable && is_string($value) && empty($value) && $value !== '0') {
            $value = null;
        }

        $model->{$attribute} = $value;
    }

    /**
     * Applies all underlying filters to given $value and returns it.
     *
     * @param $value
     *
     * @return mixed
     */
    private function filter($value)
    {
        $types = $this->type;

        if (! is_array($types)) {
            return $this->applyFilter($types, $value);
        }

        foreach ($types as $type) {
            $value = $this->applyFilter($type, $value);
        }

        return $value;
    }

    /**
     * Apply given the filter corresponding to given $type on given $value and returns the filtered $value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function applyFilter(string $type, $value)
    {
        if ($this->nullable && $value === null) {
            return null;
        }

        if ($type === self::TYPE_INTEGER) {
            return (int) $value;
        }

        if ($type === self::TYPE_FLOAT) {
            return (float) $value;
        }

        if ($type === self::TYPE_BOOLEAN) {
            return (bool) $value;
        }

//        if ($type === self::TYPE_STRING && $value instanceof DateTime) {
//            $value = (string) Carbon::parse($value);
//        }

        if ($type === self::TYPE_STRING) {
            return (string) $value;
        }

        return $value;
    }

    /**
     * Applies the nullable filter to given $value and returns the filtered $value.
     *
     * @param mixed $value
     *
     * @return mixed|null
     */
    private function applyNullable($value)
    {
        if (is_int($value) && $value === 0) {
            return $value;
        }

        if (is_float($value) && $value === 0.0) {
            return $value;
        }

        if (is_bool($value) && $value === false) {
            return $value;
        }

        if (is_string($value) && $value === '') {
            return null;
        }

        return empty($value) ? null : $value;
    }

    /**
     * Applies the trim filter to given $value and returns the filtered $value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function applyTrim($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        return trim($value);
    }
}
