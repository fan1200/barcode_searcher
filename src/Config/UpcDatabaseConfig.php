<?php


namespace BarcodeSearcher\Config;


use BarcodeSearcher\Config\Contracts\UpcDatabaseApiConfigInterface;
use BarcodeSearcher\Foundation\Contracts\AppAwareInterface;
use BarcodeSearcher\Foundation\Traits\AppAwareTrait;
use Illuminate\Contracts\Config\Repository;

class UpcDatabaseConfig implements UpcDatabaseApiConfigInterface, AppAwareInterface
{
    use AppAwareTrait;

    public function getBaseUrl(): string
    {
        return "https://api.upcdatabase.org";
    }

    public function getAuthKey(): ?string
    {
        return $this->_getIlluminateConfig()->get('barcode_searcher.upc_database_key');
    }

    protected function _getIlluminateConfig(): Repository
    {
        return $this->getApp()->make('config');
    }
}
