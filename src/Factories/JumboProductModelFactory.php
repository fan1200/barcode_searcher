<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class JumboProductModelFactory implements ProductModelFactoryInterface
{
    public function run($result): ProductModel
    {
        $productModel = new ProductModel();

        $productModel->setProductName($result['title']);
        $productModel->setPrice($result['prices']['price']['amount'] / 100);
        $productModel->setImages($result['imageInfo']);

        return $productModel;
    }
}
