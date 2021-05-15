<?php


namespace alps\sharepreviews\services;


use craft\helpers\StringHelper;
use GuzzleHttp\Client;
use RuntimeException;
use yii\base\Component;

class FontFetcher extends Component
{
    private Client $client;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->client = new Client([
            'http_errors' => true,
        ]);
    }

    private function getFontFamilyUrl(string $family): string
    {
        return sprintf(
            'http://google-webfonts-helper.herokuapp.com/api/fonts/%s',
            StringHelper::slugify($family)
        );
    }

    public function fetch(string $family, string $variantName): string
    {
        $url = $this->getFontFamilyUrl($family);

        $response = $this->client->get($url);

        $data = json_decode($response->getBody(), true);

        if (!isset($data['variants'])) {
            throw new RuntimeException(
                sprintf(
                    'Response did not contain any variant data. Response was: %s',
                    json_encode($data),
                )
            );
        }

        $variant = $this->findVariant($data['variants'], $variantName);

        if (isset($variant['ttf'])) {
            return file_get_contents($variant['ttf']);
        }

        throw new RuntimeException(
            sprintf('Path to TTF file not found in font variant data. Data was: %s.', json_encode($variant))
        );
    }

    private function findVariant(array $data, string $variantName)
    {
        $data = collect($data);

        return $data
            ->filter(function ($variantData) use ($variantName) {
                if ($variantData['id'] === $variantName) {
                    return true;
                }

                if ($variantData['fontWeight'] === (string) $variantName) {
                    return true;
                }

                if ($variantData['fontStyle'] === $variantName) {
                    return true;
                }

                return false;
            })
            ->first(null, function() use ($data, $variantName) {
                $possibleVariants = $data->map(function($variantData) {
                    return $variantData['id'];
                })->all();

                throw new RuntimeException(
                    sprintf(
                        'Variant "%s" not found in Google Web Fonts. Choose one of the following: %s',
                        $variantName,
                        json_encode($possibleVariants)
                    )
                );
            });
    }
}
