<?php


namespace BarcodeSearcher\Config;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;

class AhApiConfig implements ApiConfigInterface
{
    public function getBaseUrl(): string
    {
        return "https://ms.ah.nl";
    }
}
