<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class OpenFoodFactProductModelFactory implements ProductModelFactoryInterface
{

    public function run($data): ProductModel
    {
        $productModel = new ProductModel();
        $productModel->setProductName($this->_generateName($data));
        $productModel->setEan($data['product']['code']);
        $productModel->setManufacturer($data['product']['stores']);
        $productModel->setImages($data['product']['images']);

        return $productModel;
    }

    protected function _generateName($data): string
    {
        $brand = isset($data['product']['brands']) ? $data['product']['brands'] : null;
        $name = $data['product']['product_name'];

        return ($brand) ? $brand . " " . $name : $name;
    }
}
