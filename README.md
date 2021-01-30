# Barcode Searcher
This package provides an easy possibility to search for products based on barcodes. The package highly relies on
third party API's which may or may not publicly available. So be warned! Use it with common sense and do not over use it
in order to keep the third parties pleased ;)

# Available providers
The following providers can be used:
- Albert Heijn
- CodeChecker
- Coop
- Jumbo
- OpenFoodFact
- UpcDatabase
- UpcItemDb

If you miss a provider and would like to contribute you are more than welcome! :) It should also be easy to hook into this package for own implementations.

# Requirements
- PHP >= 7.4
- illuminate (config, support) for easy Laravel integration
- guzzle and thus curl

# Installation
When using Laravel 5.5 or higher the ServiceProvider should automatically be discovered. If not you have to add `BarcodeSearcher/ServiceProvider`
to your `app.php`.

# Usage
Implement `BarcodeSearcher\SearchManager` into your code and use the `search` function to find results.
When the search was successful you will receive a `BarcodeSearcher\Models\ProductModel` which contains the name of the product with some other information that was found.

# Configuration
All available providers can be found in the `config` file. The array also defines the sort order of providers. If you
wish to change the order or add/disable providers you could publish the config to your own project using: [PLACEHOLDER] 

## UPC Database.org
In order to make use of upcdatabase.org you'll need to create an account and add the API-key to the config.
When the key is obtained you can either choose to add it your environment variable as `BARCODE_SEARCHER_UPC_DATABASE_KEY`
or you can publish the config and overwrite it yourself. By default, it will skip the provider when no key was found.
For more information: https://upcdatabase.org/api

# Todo
- Make some tests
- Add Dirk/Dekamarkt as Provider
- Add `ProductPhotoModel` to provide a single representation of a ProductPhoto.

# Credits
So to save time I took inspiration from the following code snippets and repos:

- Albert Heijn, Jumbo, UpcDatabase, UpcDatabase - Inspired by: https://github.com/Forceu/barcodebuddy
- CodeChecker - Inspired by: https://github.com/marcojodeit/cenavita/blob/63f0d0ce4eec4d42d15580490a32025693e0fe3f/public/php/old_ean_api/rest/1.0/DataProvider_Codecheck.php

