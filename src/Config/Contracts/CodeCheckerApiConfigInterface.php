<?php

namespace BarcodeSearcher\Config\Contracts;

interface CodeCheckerApiConfigInterface extends ApiConfigInterface
{
    public function getUsername(): string;

    public function getClientNonce(): string;

    public function getSecretBytes(): array;
}
