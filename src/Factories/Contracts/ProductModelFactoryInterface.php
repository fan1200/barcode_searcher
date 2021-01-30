<?php


namespace BarcodeSearcher\Factories\Contracts;


use BarcodeSearcher\Models\ProductModel;

interface ProductModelFactoryInterface
{
    public function run($data): ProductModel;
}
