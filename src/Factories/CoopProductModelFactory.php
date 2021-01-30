<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class CoopProductModelFactory implements ProductModelFactoryInterface
{

    public function run($data): ProductModel
    {
        $productModel = new ProductModel();

        $productModel->setProductName($data['productName']);
        $productModel->setPrice($data['salePrice']['value']);
        $productModel->setEan($data['sku']);
        $productModel->setManufacturer(isset($data['manufacturer']) ?? null);
        $productModel->setDescription(isset($data['longDescription']) ?? null);
        $productModel->setImages($data['images']);

        return $productModel;
    }
}
