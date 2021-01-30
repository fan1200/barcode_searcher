<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class CodeCheckerProductModelFactory implements ProductModelFactoryInterface
{
    public function run($result): ProductModel
    {
        $productModel = new ProductModel();

        $productModel->setProductName($result['name']);
        $productModel->setEan($result['ean']);

        return $productModel;
    }
}
