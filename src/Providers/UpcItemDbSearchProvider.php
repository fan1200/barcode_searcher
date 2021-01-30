<?php


namespace BarcodeSearcher\Providers;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;
use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;
use BarcodeSearcher\Providers\Contracts\SearchProvider;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class UpcItemDbSearchProvider implements SearchProvider
{
    private const LOOKUP_ENDPOINT = "/prod/trial/lookup?upc=%d";

    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    private $result;
    /**
     * @var ProductModelFactoryInterface
     */
    private ProductModelFactoryInterface $modelFactory;
    /**
     * @var ApiConfigInterface
     */
    private ApiConfigInterface $config;

    /**
     * UpcItemDbSearchProvider constructor.
     * @param ClientInterface $client
     * @param ProductModelFactoryInterface $modelFactory
     * @param ApiConfigInterface $config
     */
    public function __construct(ClientInterface $client, ProductModelFactoryInterface $modelFactory, ApiConfigInterface $config)
    {
        $this->client = $client;
        $this->modelFactory = $modelFactory;
        $this->config = $config;
    }

    public function search(string $ean): bool
    {
        try {

            $resultBody = $this->client->get($this->_buildUrl($ean))
                ->getBody()->getContents();

            $jsonBody = json_decode($resultBody, true);

            if ($jsonBody['total'] === 0) {
                return false;
            }

            $this->result = $jsonBody['items'][0];

        } catch (ClientException $clientException) {

            if ($clientException->getCode() !== 404) {
                throw new SearchProviderException($clientException->getMessage());
            }

            return false;
        }

        return true;
    }

    protected function _buildUrl(string $ean): string
    {
        return sprintf($this->config->getBaseUrl() . self::LOOKUP_ENDPOINT, $ean);
    }

    public function handleResults(): ProductModel
    {
        try {
            return $this->modelFactory->run($this->result);
        } catch (Exception $exception) {
            return $this->modelFactory->run($this->result);
        }
    }
}
