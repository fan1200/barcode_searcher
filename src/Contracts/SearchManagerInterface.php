<?php

namespace BarcodeSearcher\Contracts;

use BarcodeSearcher\Exceptions\BarcodeException;
use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Models\ProductModel;
use BarcodeSearcher\Providers\Contracts\SearchProvider;

interface SearchManagerInterface
{
    /**
     * @param string $barcode
     * @return ProductModel|null
     * @throws BarcodeException
     * @throws SearchProviderException
     */
    public function search(string $barcode): ?ProductModel;

    /**
     * @return SearchProvider[]|array
     */
    public function getSearchProvider(): array;

    /**
     * @param SearchProvider[]|array $searchProviders
     */
    public function setSearchProviders(array $searchProviders): void;
}
