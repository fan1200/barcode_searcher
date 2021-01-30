<?php


namespace BarcodeSearcher\Config;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;

class JumboApiConfig implements ApiConfigInterface
{
    public function getBaseUrl(): string
    {
        return "https://mobileapi.jumbo.com/v9";
    }
}
