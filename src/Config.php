<?php

namespace alps\sharepreviews;

use BadMethodCallException;
use Craft;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class Config
{
    const POSITION_LEFT = 'left';
    const POSITION_CENTER = 'center';
    const POSITION_RIGHT = 'right';

    public int $width = 1200;
    public int $height = 630;

    public int $contentPaddingLeft = 50;
    public int $contentPaddingTop = 50;
    public int $contentPaddingRight = 50;
    public int $contentPaddingBottom = 50;

    public string $contentText = '';

    public string $backgroundType = 'color';
    public array $backgroundColor = [255, 255, 255];

    public array $backgroundGradientFrom = [255, 255, 255];
    public array $backgroundGradientTo = [0, 0, 0];
    public int $backgroundGradientAngle = 0;

    public ?string $backgroundImagePath = null;

    public ?string $imagePublicPath = null;
    public string $imagePosition = self::POSITION_RIGHT;
    public int $imageMaxWidth = 400;
    public int $imageMaxHeight = 530;
    public int $imageBorder = 0;
    public array $imageBorderColor = [0, 0, 0];

    public int $imagePaddingLeft = 50;
    public int $imagePaddingTop = 50;
    public int $imagePaddingRight = 50;
    public int $imagePaddingBottom = 50;

    public string $fontFamily = 'Roboto';
    public string $fontWeight = '400';
    public array $fontColor = [0, 0, 0];
    public int $fontSize = 50;

    private array $defaultConfig = [];

    /**
     * @var \craft\console\Application|\craft\web\Application
     */
    private $app;
    /**
     * @var models\Settings
     */
    private models\Settings $settings;

    public function __construct()
    {
        $this->settings = SharePreviews::getInstance()->getSettings();

//        $this->setDefaultConfig($this->settings->renderer);

        $this->app = Craft::$app;
    }

    public function __toString(): string
    {
        return $this->getUrl();
    }

    public function __call(string $name, $value): self
    {
        if (substr($name, 0, 4) !== 'with') {
            throw new BadMethodCallException(
                sprintf('Method [%s] not found.', $name)
            );
        }

        $property = substr($name, 4);
        $property = lcfirst($property);

        if (!property_exists($this, $property)) {
            throw new InvalidArgumentException(
                sprintf('Property [%s] does not exists.', $property)
            );
        }

        $this->{$property} = $value[0];

        return $this;
    }

    public function with(array $properties): self
    {
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
                continue;
            }

            throw new InvalidArgumentException(
                sprintf('Property [%s] does not exists.', $property)
            );
        }

        return $this;
    }

    private function setDefaultConfig(array $config): self
    {
        $config = $this->flattenConfigArray([], $config);

        $this->defaultConfig = $config;

        return $this->with($config);
    }

    public function setFromArray(array $config): self
    {
        return $this->with(
            $this->flattenConfigArray([], $config)
        );
    }

    public function applyEncodedData(string $data): self
    {
        $props = $this->decodeDiff($data);

        return $this->with($props);
    }

    private function flattenConfigArray(array $arr, array $input, string $prefix = null): array
    {
        foreach ($input as $key => $value) {
            $key = StringHelper::camelCase($key);
            $path = $prefix ? $prefix . ucfirst($key) : $key;

            if (!is_array($value)) {
                $arr[$path] = $value;
                continue;
            }

            if (array_keys($value) === range(0, count($value) - 1)) {
                $arr[$path] = $value;
                continue;
            }

            $arr = $this->flattenConfigArray($arr, $value, $path);
        }

        return $arr;
    }

    public function getContentCanvasWidth(): int
    {
        return $this->width - $this->contentPaddingLeft - $this->contentPaddingRight;
    }

    public function getContentCanvasHeight(): int
    {
        return $this->height - $this->contentPaddingTop - $this->contentPaddingBottom;
    }

    public function getImageCanvasWidth(): int
    {
        return $this->width - $this->imagePaddingLeft - $this->imagePaddingRight;
    }

    public function getImageCanvasHeight(): int
    {
        return $this->height - $this->imagePaddingTop - $this->imagePaddingBottom;
    }

    public function getUrl(): string
    {
        $diff = $this->getDiffedProperties();

        $data = $this->encodeDiff($diff);

        $url = sprintf(
            '%s/%s/%s.png',
            rtrim(UrlHelper::baseCpUrl(), '/'),
            $this->settings->routePrefix,
            $data
        );

        return $url;
    }

    private function encodeDiff(array $diff): string
    {
        $configHash = hash('crc32', json_encode($this->defaultConfig));

        $diff = json_encode($diff);
        $base64 = base64_encode($diff);

        $base64 = str_replace(['+', '/', '='], ['-', '_', ''], $base64);

        $base64 = str_split($base64, 64);
        $base64 = implode('/', $base64);

        return $configHash . '/' . $base64;
    }

    private function decodeDiff(string $diff): array
    {
        $parts = explode('/', $diff);

        array_shift($parts);

        $base64 = implode('', $parts);

        $base64 = str_replace(['-', '_'], ['+', '/'], $base64);

        $json = base64_decode($base64);

        return json_decode($json, true) ?? [];
    }

    private function getDiffedProperties(): array
    {
        $reflection = new ReflectionClass($this);

        $values = [];

        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $values[] = [
                'name' => $property->getName(),
                'runtime' => $property->getValue($this),
                'default' => $this->defaultConfig[$property->getName()]
                    ?? $property
                        ->getDeclaringClass()
                        ->getDefaultProperties()[$property->getName()],
            ];
        }

        $values = collect($values)
            ->filter(function ($value) {
                return $value['runtime'] !== $value['default'];
            })
            ->pluck('runtime', 'name')
            ->all();

        return $values;
    }

    public function getPublicPath(string $relativeToPublic): string
    {
        $publicPath = rtrim(Craft::getAlias('@webroot'), '/');

        return $publicPath . '/' . ltrim($relativeToPublic, '/');
    }
}
