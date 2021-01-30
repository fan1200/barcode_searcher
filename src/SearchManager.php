<?php


namespace BarcodeSearcher;


use BarcodeSearcher\Contracts\SearchManagerInterface;
use BarcodeSearcher\Exceptions\BarcodeException;
use BarcodeSearcher\Exceptions\SearchProviderException;
use BarcodeSearcher\Models\ProductModel;
use BarcodeSearcher\Providers\Contracts\SearchProvider;

class SearchManager implements SearchManagerInterface
{
    /**
     * @var array | SearchProvider[]
     */
    private array $searchProviders = [];

    /**
     * @param string $barcode
     * @return ProductModel|null
     * @throws BarcodeException
     * @throws SearchProviderException
     */
    public function search(string $barcode): ?ProductModel
    {
        if (!$this->_isValidBarcode($barcode)) {
            throw new BarcodeException("Invalid barcode");
        }

        foreach ($this->getSearchProvider() as $provider) {
            if ($provider->search($barcode)) {
                return $provider->handleResults();
            }
        }

        return null;
    }


    /**
     * @param string $barcode
     * @return bool
     * code obtained from: https://stackoverflow.com/a/41790822
     */
    protected function _isValidBarcode(string $barcode): bool
    {
        //checks validity of: GTIN-8, GTIN-12, GTIN-13, GTIN-14, GSIN, SSCC
        //see: http://www.gs1.org/how-calculate-check-digit-manually
        $barcode = (string)$barcode;
        //we accept only digits
        if (!preg_match("/^[0-9]+$/", $barcode)) {
            return false;
        }
        //check valid lengths:
        $l = strlen($barcode);
        if (!in_array($l, [8, 12, 13, 14, 17, 18]))
            return false;
        //get check digit
        $check = substr($barcode, -1);
        $barcode = substr($barcode, 0, -1);
        $sum_even = $sum_odd = 0;
        $even = true;
        while (strlen($barcode) > 0) {
            $digit = substr($barcode, -1);
            if ($even)
                $sum_even += 3 * $digit;
            else
                $sum_odd += $digit;
            $even = !$even;
            $barcode = substr($barcode, 0, -1);
        }
        $sum = $sum_even + $sum_odd;
        $sum_rounded_up = ceil($sum / 10) * 10;
        return ($check == ($sum_rounded_up - $sum));
    }

    /**
     * @return SearchProvider[]|array
     */
    public function getSearchProvider(): array
    {
        return $this->searchProviders;
    }

    /**
     * @param SearchProvider[]|array $searchProviders
     */
    public function setSearchProviders(array $searchProviders): void
    {
        $this->searchProviders = $searchProviders;
    }
}
