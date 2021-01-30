<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class AhOrderProductModelFactory implements ProductModelFactoryInterface
{

    public function run($result): ProductModel
    {
        $productModel = new ProductModel();

        $productModel->setProductName($result['title']);
        $productModel->setImages($result['images']);
        $productModel->setPrice($result['priceBeforeBonus']);
        $productModel->setDescription($result['descriptionFull']);
        $productModel->setManufacturer($result['brand']);

        return $productModel;
    }
}
