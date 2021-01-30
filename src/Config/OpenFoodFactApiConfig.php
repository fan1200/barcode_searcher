<?php


namespace BarcodeSearcher\Config;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;

class OpenFoodFactApiConfig implements ApiConfigInterface
{
    public function getBaseUrl(): string
    {
        return "https://world.openfoodfacts.org/api/v0";
    }
}
