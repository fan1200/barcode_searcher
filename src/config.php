<?php

use BarcodeSearcher\Providers\AhSearchProvider;
use BarcodeSearcher\Providers\CodeCheckerSearchProvider;
use BarcodeSearcher\Providers\CoopSearchProvider;
use BarcodeSearcher\Providers\JumboSearchProvider;
use BarcodeSearcher\Providers\OpenFoodFactSearchProvider;
use BarcodeSearcher\Providers\UpcDatabaseSearchProvider;
use BarcodeSearcher\Providers\UpcItemDbSearchProvider;

return [
    'providers' => [
        CoopSearchProvider::class,
        AhSearchProvider::class,
        JumboSearchProvider::class,
        UpcItemDbSearchProvider::class,
        UpcDatabaseSearchProvider::class,
        OpenFoodFactSearchProvider::class,
        CodeCheckerSearchProvider::class
    ],
    'guzzle' => [
        'timeout' => 2.0,
        'delay' => 1000,
    ],
    'upc_database_key' => env('BARCODE_SEARCHER_UPC_DATABASE_KEY'),
];
