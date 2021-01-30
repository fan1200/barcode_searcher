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

class JumboSearchProvider implements SearchProvider
{
    private const SEARCH_ENDPOINT = "/search?q=%s";

    /**
     * @var ClientInterface
     */
    private ClientInterface $client;

    private $result;
    /**
     * @var ProductModelFactoryInterface
     */
    private ProductModelFactoryInterface $jumboProductModelFactory;
    /**
     * @var ApiConfigInterface
     */
    private ApiConfigInterface $config;

    public function __construct(ClientInterface $client, ProductModelFactoryInterface $jumboProductModelFactory, ApiConfigInterface $config)
    {
        $this->client = $client;
        $this->jumboProductModelFactory = $jumboProductModelFactory;
        $this->config = $config;
    }

    public function search(string $ean): bool
    {
        try {
            $result = $this->client->get($this->_buildUrl($ean), [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:81.0) Gecko/20100101 Firefox/81.0'
                ]
            ]);

            $result = json_decode($result->getBody()->getContents(), true);
            if ($result['products']['total'] > 0) {
                $this->result = $result['products']['data'][0];
            } else {
                return false;
            }

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
        return sprintf($this->config->getBaseUrl() . self::SEARCH_ENDPOINT, $ean);
    }

    public function handleResults(): ProductModel
    {
        try {
            return $this->jumboProductModelFactory->run($this->result);
        } catch (Exception $exception) {
            throw new SearchProviderException($exception->getMessage());
        }
    }
}
