<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class UpcItemDbProductModelFactory implements ProductModelFactoryInterface
{
    public function run($result): ProductModel
    {
        $productModel = new ProductModel();

        $productModel->setProductName($result['title']);
        $productModel->setEan($result['ean']);
        $productModel->setDescription($result['description']);
        $productModel->setManufacturer($result['brand']);
        $productModel->setPrice($result['lowest_recorded_price']);
        $productModel->setImages($result['images']);

        return $productModel;
    }
}
