<?php


namespace BarcodeSearcher\Config;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;

class UpcItemDbApiConfig implements ApiConfigInterface
{
    public function getBaseUrl(): string
    {
        return "https://api.upcitemdb.com";
    }
}
