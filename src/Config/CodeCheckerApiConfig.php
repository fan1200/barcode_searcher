<?php


namespace BarcodeSearcher\Config;

use BarcodeSearcher\Config\Contracts\CodeCheckerApiConfigInterface;

class CodeCheckerApiConfig implements CodeCheckerApiConfigInterface
{
    private ?string $clientNonce = null;

    public function getBaseUrl(): string
    {
        return "https://www.codecheck.info";
    }

    public function getUsername(): string
    {
        return 'androswan4';
    }

    public function getClientNonce(): string
    {
        if (!$this->clientNonce) {
            $this->clientNonce = base64_encode(random_bytes(16));
        }

        return $this->clientNonce;
    }

    public function getSecretBytes(): array
    {
        return [
            23, 89, 196, 82,
            225, 134, 83, 66,
            59, 53, 246, 158,
            162, 108, 153, 129,
            3, 216, 25, 98,
            141, 25, 148, 227,
            251, 123, 2, 175,
            42, 27, 7, 183
        ];
    }
}
