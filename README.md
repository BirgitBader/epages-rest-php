# epages 6 REST client
This is the REST client to connect to an epages shop. You can use it as a developer for epages app. Register at the [epages developer page](https://developer.epages.com) for a free development account.

## Requirements
To use this package it needs at least **PHP 5.4**.
Download the PHP file archive (.phar) in **build** folder, copy it to your project folder and use it like:
```php
require("libraries/eP6RESTclient.phar");
```

## Version information
This are the information which are provided with the epages 6 REST client since now.

| Feature | GET | PUT | POST | DELETE | PATCH | information |
| --- | :---: | :---: | :---: | :---: | :---: | --- |
| locales | **✔** | **-** | **-** | **-** | **?** |
| currencies | **✔** | **-** | **-** | **-** | **?** |
| legal | **✘** | **-** | **-** | **-** | **?** | won't do, it's not needed |
| legal/contact-information | **✔** | **✘** | **-** | **-** | **?** |
| legal/privacy-policy | **✔** | **✘** | **-** | **-** | **?** |
| legal/terms-and-condition | **✔** | **✘** | **-** | **-** | **?** |
| legal/rights-of-withdrawal | **✔** | **✘** | **-** | **-** | **?** |
| legal/shipping-information | **✔** | **✘** | **-** | **-** | **?** |
| products | **✔** | **-** | **-** | **-** | **?** |
| products/export | **✘** | **-** | **-** | **-** | **?** |
| categories | **✘** | **✘** | **-** | **-** | **?** |
| carts | **✘** | **✘** | **✘** | **✘** | **?** |
| search/product-suggest | **✘** | **-** | **-** | **-** | **?** |
| shpping-methods | **✘** | **-** | **-** | **-** | **?** |

## Code examples

### Example 1

This example get 100 products with a german localization and sort it with **name** attribute:

```php
require_once("libraries/eP6RESTclient.phar");

// set connection constants
$HOST		= "www.meinshop.de";
$SHOP		= "DemoShop";
$AUTHTOKEN	= "xyzxyzxyzxyzxyzxyzxyzxyz";
$ISSSL		= true;

// connect to shop
$shop = new ep6\Shop($HOST, $SHOP, $AUTHTOKEN, $ISSSL);

// use a product filter to search for products
$productFilter = new ep6\ProductFilter();
$productFilter->setLocale("de_DE");
$productFilter->setCurrency("EUR");
$productFilter->setSort("name");
$productFilter->setResultsPerPage(100);
$products = $productFilter->getProducts();

// print the products
foreach ($products as $product) {

	echo "<h2>" . htmlentities($product->getName("de_DE")) . "</h2>";
	echo "<p>";
	echo "<img style=\"float:left\" src=\"" . $product->getSmallImage()->getOriginURL() . "\"/>";
	echo "<strong>ProductID:</strong> " . $product->getID() . "<br/>";
	echo "<strong>Beschreibung:</strong> " . htmlentities($product->getDescription("de_DE")) . "<br/><br/>";
	echo "<strong>Diese Produkt wird ";
	if (!$product->isForSale()) {
		echo "NICHT ";
	}
	echo "verkauft und ist ";
	if ($product->isSpecialOffer()) {
		echo "<u>ein</u> ";
	}
	else {
		echo "KEIN ";
	}
	echo "Spezialangebot</strong>";
	echo "</p><hr style=\"clear:both\"/>";
}
```

### Example 2

This example gets some shop information.

```php
require_once("libraries/eP6RESTclient.phar");

// set connection constants
$HOST		= "www.meinshop.de";
$SHOP		= "DemoShop";
$AUTHTOKEN	= "xyzxyzxyzxyzxyzxyzxyzxyz";
$ISSSL		= true;

// connect to shop
$shop = new ep6\Shop($HOST, $SHOP, $AUTHTOKEN, $ISSSL);

// prints the default currency and localization
echo ep6\Currencies::getDefault();
echo ep6\Locales::getDefault();

// prints the name of the contact information in default language and in german
$contactInformation = $shop->getContactInformation();
echo $contactInformation->getDefaultName();
echo $contactInformation->getName("de_DE");
```

## Utilities

### Logger
The library comes with a huge Logger. It is called ```ep6\Logger```.
To use this (instead of the ```echo``` command) write
```php
ep6\Logger::force("STRNG");
```
The force printer also can print arrays in a simple structure.

By default all notification messages are print. To change this use:
```php
ep6\Logger::setLogLevel("NOTIFICATION");	// shows all messages
ep6\Logger::setLogLevel("WARNING");			// shows warning and error messages
ep6\Logger::setLogLevel("ERROR");			// shows only error messages
```
### InputValidator

To validate data and check the value of an object there is a InputValidator class:
```php
ep6\InputValidator::isHost("www.test.de");
ep6\InputValidator::isJSON("{}");
```
All available functions will be documented soon.

## Licence

The code is available under the terms of the MIT License.