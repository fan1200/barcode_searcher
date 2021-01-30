<?php


namespace BarcodeSearcher\Providers;


use BarcodeSearcher\Config\Contracts\CodeCheckerApiConfigInterface;
use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;
use BarcodeSearcher\Providers\Contracts\SearchProvider;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class CodeCheckerSearchProvider implements SearchProvider
{
    protected const SEARCH_ENDPOINT = "/WebService/rest/prodlist/ean/16777216/%s";
    protected const AUTH_ENDPOINT = "/WebService/rest/session/auth";

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
     * @var CodeCheckerApiConfigInterface
     */
    private CodeCheckerApiConfigInterface $config;

    /**
     * UpcDatabaseSearchProvider constructor.
     * @param ClientInterface $client
     * @param ProductModelFactoryInterface $modelFactory
     * @param CodeCheckerApiConfigInterface $config
     */
    public function __construct(ClientInterface $client, ProductModelFactoryInterface $modelFactory, CodeCheckerApiConfigInterface $config)
    {
        $this->client = $client;
        $this->modelFactory = $modelFactory;
        $this->config = $config;
    }

    public function search(string $ean): bool
    {
        try {

            $resultBody = $this->client
                ->get($this->_buildUrl($ean), [
                    'headers' => [
                        'Authorization' => $this->_getAuthorizationHeader()
                    ]
                ])
                ->getBody()
                ->getContents();

            $bodyContents = json_decode($resultBody, true);

            if (isset($bodyContents['error'])) {
                return false;
            }

            $this->result = $bodyContents['result'][0];

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
        return sprintf($this->config->getBaseUrl() . self::SEARCH_ENDPOINT, $ean);
    }

    /**
     * @return string
     * @throws SearchProviderException
     * code obtained from:
     * https://github.com/marcojodeit/cenavita/blob/63f0d0ce4eec4d42d15580490a32025693e0fe3f/public/php/old_ean_api/rest/1.0/DataProvider_Codecheck.php#L131
     */
    private function _getAuthorizationHeader(): string
    {
        $nonce = $this->_getAuthKey();

        $secret = call_user_func_array('pack', array_merge(array('C*'), $this->config->getSecretBytes()));
        $message = $this->config->getUsername() . base64_decode($nonce) . base64_decode($this->config->getClientNonce()); // 42 bytes
        $hmac = base64_encode(hash_hmac('sha256', $message, $secret, true));
        return sprintf('DigestQuick nonce="%s",mac="%s"', $nonce, $hmac);
    }

    /**
     * @return string|null
     * @throws SearchProviderException
     */
    protected function _getAuthKey(): ?string
    {
        $authUrl = $this->config->getBaseUrl() . self::AUTH_ENDPOINT;

        try {
            $resultBody = json_decode($this->client
                ->post($authUrl, [
                    'json' => $this->_buildAuthBody()
                ])
                ->getBody()
                ->getContents(), true);
        } catch (Exception $exception) {
            throw new SearchProviderException($exception->getMessage());
        }

        return $resultBody['result']['nonce'];
    }

    protected function _buildAuthBody(): array
    {
        return [
            'personalization' => [
                'veganWarning' => false,
                'veggieWarning' => false,
                'lactoseWarning' => false,
                'glutenWarning' => false,
                'paidNews' => false,
                'locale' => 'en',
            ],
            'osVersion' => '9',
            'sendTargetingInfo' => false,
            'clientNonce' => $this->config->getClientNonce(),
            'deviceModel' => rand(),
            'authType' => 'DigestQuick',
            'deviceManufacturer' => 'unknown',
            'osName' => 'Android',
            'deviceId' => rand(),
            'apiLevel' => 5,
            'username' => $this->config->getUsername(),
        ];
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
