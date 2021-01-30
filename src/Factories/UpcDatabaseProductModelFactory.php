<?php


namespace BarcodeSearcher\Factories;


use BarcodeSearcher\Factories\Contracts\ProductModelFactoryInterface;
use BarcodeSearcher\Models\ProductModel;

class UpcDatabaseProductModelFactory implements ProductModelFactoryInterface
{
    public function run($result): ProductModel
    {
        $productModel = new ProductModel();

        $productModel->setProductName($result['title']);
        $productModel->setDescription($result['description']);
        $productModel->setManufacturer($this->_getManufacturer($result));
        $productModel->setEan($result['barcode']);

        return $productModel;
    }

    protected function _getManufacturer($result): ?string
    {
        if (!empty($result['manufacturer'])) {
            return $result['manufacturer'];
        }

        if (!empty($result['brand'])) {
            return $result['brand'];
        }

        return null;
    }
}
