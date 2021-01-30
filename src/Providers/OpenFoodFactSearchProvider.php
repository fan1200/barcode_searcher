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

class OpenFoodFactSearchProvider implements SearchProvider
{

    protected const PRODUCT_ENDPOINT = "/product/%d.json";
    protected $result;
    /**
     * @var ApiConfigInterface
     */
    private ApiConfigInterface $apiConfig;
    /**
     * @var ClientInterface
     */
    private ClientInterface $client;
    /**
     * @var ProductModelFactoryInterface
     */
    private ProductModelFactoryInterface $productModelFactory;

    public function __construct(ApiConfigInterface $apiConfig, ClientInterface $client, ProductModelFactoryInterface $productModelFactory)
    {
        $this->apiConfig = $apiConfig;
        $this->client = $client;
        $this->productModelFactory = $productModelFactory;
    }

    public function search(string $ean): bool
    {
        try {
            $body = $this->client->get($this->_buildUrl($ean))->getBody();
            $this->result = json_decode($body->getContents(), true);

            if ($this->result['status'] === 0 || !isset($data['product']['product_name'])) {
                return false;
            }

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
        return $this->apiConfig->getBaseUrl() . sprintf(self::PRODUCT_ENDPOINT, $ean);
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
