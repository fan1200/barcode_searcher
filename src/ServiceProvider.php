<?php

namespace BarcodeSearcher;

use BarcodeSearcher\Config\AhApiConfig;
use BarcodeSearcher\Config\CodeCheckerApiConfig;
use BarcodeSearcher\Config\Contracts\ApiConfigInterface;
use BarcodeSearcher\Config\Contracts\CodeCheckerApiConfigInterface;
use BarcodeSearcher\Config\Contracts\UpcDatabaseApiConfigInterface;
use BarcodeSearcher\Config\CoopApiConfig;
use BarcodeSearcher\Config\JumboApiConfig;
use BarcodeSearcher\Config\OpenFoodFactApiConfig;
use BarcodeSearcher\Config\UpcDatabaseConfig;
use BarcodeSearcher\Config\UpcItemDbApiConfig;
use BarcodeSearcher\Contracts\SearchManagerInterface;
use BarcodeSearcher\Factories\AhOrderProductModelFactory;
use BarcodeSearcher\Factories\CodeCheckerProductModelFactory;
use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Factories\CoopProductModelFactory;
use BarcodeSearcher\Factories\JumboProductModelFactory;
use BarcodeSearcher\Factories\OpenFoodFactProductModelFactory;
use BarcodeSearcher\Factories\UpcDatabaseProductModelFactory;
use BarcodeSearcher\Factories\UpcItemDbProductModelFactory;
use BarcodeSearcher\Foundation\Contracts\AppAwareInterface;
use BarcodeSearcher\Providers\AhSearchProvider;
use BarcodeSearcher\Providers\CodeCheckerSearchProvider;
use BarcodeSearcher\Providers\CoopSearchProvider;
use BarcodeSearcher\Providers\JumboSearchProvider;
use BarcodeSearcher\Providers\OpenFoodFactSearchProvider;
use BarcodeSearcher\Providers\UpcDatabaseSearchProvider;
use BarcodeSearcher\Providers\UpcItemDbSearchProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Illuminate\Config\Repository;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => $this->app->configPath('barcode-searcher.php'),
        ], 'barcode_searcher-config');
    }

    public function register()
    {
        parent::register();
        
        $this->app->resolving(AppAwareInterface::class, function (AppAwareInterface $class) {
            $class->setApp($this->app);
            return $class;
        });
        
        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'barcode_searcher'
        );

        $this->_registerConfigs();
        $this->_registerModelFactories();

        $this->app->bind(SearchManagerInterface::class, SearchManager::class);

        $this->app->when($this->_getProviders())
            ->needs(ClientInterface::class)
            ->give(function ($app) {
                return new Client([
                    RequestOptions::TIMEOUT => $this->_createConfig()->get('barcode_searcher.guzzle.timeout'),
                    RequestOptions::DELAY => $this->_createConfig()->get('barcode_searcher.guzzle.delay'),
                ]);
            });

        $this->app->resolving(SearchManagerInterface::class, function (SearchManagerInterface $searchManager) {
            $searchManager->setSearchProviders($this->_makeProviders());
        });
    }

    protected function _registerConfigs()
    {
        $this->app->bind(CodeCheckerApiConfigInterface::class, CodeCheckerApiConfig::class);
        $this->app->bind(UpcDatabaseApiConfigInterface::class, UpcDatabaseConfig::class);

        $this->app->when(OpenFoodFactSearchProvider::class)
            ->needs(ApiConfigInterface::class)
            ->give(OpenFoodFactApiConfig::class);

        $this->app->when(CoopSearchProvider::class)
            ->needs(ApiConfigInterface::class)
            ->give(CoopApiConfig::class);

        $this->app->when(AhSearchProvider::class)
            ->needs(ApiConfigInterface::class)
            ->give(AhApiConfig::class);

        $this->app->when(JumboSearchProvider::class)
            ->needs(ApiConfigInterface::class)
            ->give(JumboApiConfig::class);

        $this->app->when(UpcItemDbSearchProvider::class)
            ->needs(ApiConfigInterface::class)
            ->give(UpcItemDbApiConfig::class);
    }

    protected function _registerModelFactories()
    {
        $this->app->when(OpenFoodFactSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(OpenFoodFactProductModelFactory::class);

        $this->app->when(CoopSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(CoopProductModelFactory::class);

        $this->app->when(AhSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(AhOrderProductModelFactory::class);

        $this->app->when(CodeCheckerSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(CodeCheckerProductModelFactory::class);

        $this->app->when(JumboSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(JumboProductModelFactory::class);

        $this->app->when(UpcItemDbSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(UpcItemDbProductModelFactory::class);

        $this->app->when(UpcDatabaseSearchProvider::class)
            ->needs(ProductModelFactoryInterface::class)
            ->give(UpcDatabaseProductModelFactory::class);
    }

    protected function _getProviders()
    {
        return $this->_createConfig()->get('barcode_searcher.providers');
    }

    protected function _createConfig(): Repository
    {
        return $this->app->make('config');
    }

    protected function _makeProviders(): array
    {
        $providersMade = [];

        foreach ($this->_getProviders() as $provider) {
            $providersMade[] = $this->app->make($provider);
        }

        return $providersMade;
    }
}