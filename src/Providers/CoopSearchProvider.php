<?php


namespace BarcodeSearcher\Providers;


use BarcodeSearcher\Config\Contracts\ApiConfigInterface;
use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class CoopSearchProvider implements Contracts\SearchProvider
{

    protected const PRODUCT_ENDPOINT = "/deprecated-products/%d/";
    protected $result = null;
    /**
     * @var ClientInterface
     */
    private ClientInterface $guzzleClient;
    /**
     * @var ApiConfigInterface
     */
    private ApiConfigInterface $config;
    /**
     * @var ProductModelFactoryInterface
     */
    private ProductModelFactoryInterface $productModelFactory;

    public function __construct(ClientInterface $guzzleClient, ApiConfigInterface $config, ProductModelFactoryInterface $productModelFactory)
    {
        $this->guzzleClient = $guzzleClient;
        $this->config = $config;
        $this->productModelFactory = $productModelFactory;
    }

    public function search(string $ean): bool
    {
        try {
            $body = $this->guzzleClient->get($this->_buildUrl($ean))->getBody();
            $this->result = json_decode($body->getContents(), true);

            return true;
        } catch (ClientException $clientException) {

            if ($clientException->getCode() !== 404) {
                throw new SearchProviderException($clientException->getMessage());
            }

            return false;
        }
    }

    protected function _buildUrl(int $ean)
    {
        return $this->config->getBaseUrl() . sprintf(self::PRODUCT_ENDPOINT, $ean);
    }

    public function handleResults(): ProductModel
    {
        try {
            return $this->productModelFactory->run($this->result);
        } catch (Exception $exception) {
            throw new SearchProviderException($exception->getMessage());
        }
    }
}
