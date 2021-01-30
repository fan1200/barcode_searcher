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

class AhSearchProvider implements SearchProvider
{
    private const BARCODE_SEARCH_ENDPOINT = "/mobile-services/product/search/v1/gtin/%s";
    private const AUTHORIZE_ENDPOINT = "/create-anonymous-member-token";

    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    private array $basicHeaders = [
        'User-Agent' => 'android/6.29.3 Model/phone Android/7.0-API24',
        'Host' => 'ms.ah.nl',
    ];

    private $result;

    /**
     * @var ProductModelFactoryInterface
     */
    private ProductModelFactoryInterface $productModelFactory;
    /**
     * @var ApiConfigInterface
     */
    private ApiConfigInterface $config;

    /**
     * AhSearchProvider constructor.
     * @param ClientInterface $client
     * @param ProductModelFactoryInterface $productModelFactory
     * @param ApiConfigInterface $config
     */
    public function __construct(ClientInterface $client, ProductModelFactoryInterface $productModelFactory, ApiConfigInterface $config)
    {
        $this->client = $client;
        $this->productModelFactory = $productModelFactory;
        $this->config = $config;
    }

    /**
     * @param string $ean
     * @return bool
     * @throws SearchProviderException
     */
    public function search(string $ean): bool
    {
        if (!$token = $this->_getToken()) {
            return false;
        }

        try {
            $result = $this->client->get($this->_buildUrl($ean), [
                'headers' => array_merge($this->basicHeaders, [
                    'Authorization' => "Bearer {$token}",
                ])
            ]);

            $this->result = json_decode($result->getBody()->getContents(), true);

            return true;
        } catch (ClientException $clientException) {

            if ($clientException->getCode() !== 404) {
                throw new SearchProviderException($clientException->getMessage());
            }

            return false;
        }
    }

    /**
     * @return string|null
     * @throws SearchProviderException
     */
    protected function _getToken(): ?string
    {
        try {
            $tokenResult = $this->client->post($this->config->getBaseUrl() . self::AUTHORIZE_ENDPOINT, [
                'headers' => $this->basicHeaders,
                'json' => ["client" => "appie-anonymous"]
            ]);

            return json_decode($tokenResult->getBody()->getContents(), true)['access_token'];
        } catch (Exception $exception) {
            throw new SearchProviderException($exception->getMessage());
        }
    }

    protected function _buildUrl(string $ean): string
    {
        return sprintf($this->config->getBaseUrl() . self::BARCODE_SEARCH_ENDPOINT, $ean);
    }

    /**
     * @return ProductModel
     * @throws SearchProviderException
     */
    public function handleResults(): ProductModel
    {
        try {
            return $this->productModelFactory->run($this->result);
        } catch (Exception $exception) {
            throw new SearchProviderException($exception->getMessage());
        }
    }
}
