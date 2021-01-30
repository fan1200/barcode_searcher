<?php


namespace BarcodeSearcher\Providers;


use BarcodeSearcher\Config\Contracts\UpcDatabaseApiConfigInterface;
use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;
use BarcodeSearcher\Providers\Contracts\SearchProvider;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class UpcDatabaseSearchProvider implements SearchProvider
{
    private const ENDPOINT = "/product/%s?apikey=%s";

    /**
     * @var ClientInterface
     */
    private ClientInterface $client;
    /**
     * @var ProductModelFactoryInterface
     */
    private ProductModelFactoryInterface $modelFactory;

    private $result;
    /**
     * @var UpcDatabaseApiConfigInterface
     */
    private UpcDatabaseApiConfigInterface $config;

    /**
     * UpcDatabaseSearchProvider constructor.
     * @param ClientInterface $client
     * @param ProductModelFactoryInterface $modelFactory
     * @param UpcDatabaseApiConfigInterface $config
     */
    public function __construct(ClientInterface $client, ProductModelFactoryInterface $modelFactory, UpcDatabaseApiConfigInterface $config)
    {
        $this->client = $client;
        $this->modelFactory = $modelFactory;
        $this->config = $config;
    }

    public function search(string $ean): bool
    {
        if (!$this->config->getAuthKey()) {
           return false;
        }

        try {
            $resultBody = $this->client
                ->get($this->_buildUrl($ean))
                ->getBody()
                ->getContents();

            $bodyContents = json_decode($resultBody, true);

            if (!$bodyContents['success']) {
                return false;
            }

            $this->result = $bodyContents;

        } catch (ClientException $clientException) {

            if ($clientException->getCode() !== 404) {
                throw new SearchProviderException($clientException->getMessage());
            }

            return false;
        }

        return true;
    }

    protected function _buildUrl(string $ean)
    {
        return $this->config->getBaseUrl() . sprintf(self::ENDPOINT, $ean, $this->config->getAuthKey());
    }

    public function handleResults(): ProductModel
    {
        try {
            return $this->modelFactory->run($this->result);
        } catch (Exception $exception) {
            throw new SearchProviderException($exception->getMessage());
        }
    }
}
