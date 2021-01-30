<?php


namespace BarcodeSearcher\Config;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;

class CoopApiConfig implements ApiConfigInterface
{
    public function getBaseUrl(): string
    {
        return "https://api.coop.nl/INTERSHOP/rest/WFS/COOP-COOPBase-Site/-";
    }
}
