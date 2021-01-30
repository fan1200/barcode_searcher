<?php


namespace BarcodeSearcher\Providers\Contracts;


use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Models\ProductModel;

interface SearchProvider
{
    /**
     * @param string $ean
     * @return bool
     * @throws SearchProviderException
     */
    public function search(string $ean): bool;

    /**
     * @return ProductModel
     * @throws SearchProviderException
     */
    public function handleResults(): ProductModel;
}
