<?php

namespace BarcodeSearcher\Config\Contracts;

interface UpcDatabaseApiConfigInterface extends ApiConfigInterface
{
    public function getAuthKey(): ?string;
}
