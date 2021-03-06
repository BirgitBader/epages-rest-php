<?php
/**
 * This file represents the product class.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.0
 */
namespace ep6;
/**
 * This is the product class for a product in the shop.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.0
 * @since 0.1.0 Add price information.
 * @since 0.1.0 Function to delete itself.
 * @since 0.1.0 Delete different Locales.
 * @since 0.1.0 Implement Slideshow functionality.
 * @since 0.1.0 Implement attribute functionality.
 * @since 0.1.0 Implement stock level functionality.
 * @since 0.1.1 This object can be printed with echo.
 * @since 0.1.1 Don't use locale parameter in get functions.
 * @since 0.1.1 Unstatic every attributes.
 * @since 0.1.2 Insert the Setters.
 * @package ep6
 * @subpackage Shopobjects\Product
 */
class Product {

	/** @var String The REST path to the product ressource. */
	const RESTPATH = "products";

	/** @var String The REST path to the product ressource. */
	const RESTPATH_ATTRIBUTES = "custom-attributes";

	/** @var String The REST path to the product ressource. */
	const RESTPATH_STOCKLEVEL = "stock-level";

	/** @var String|null The product ID. */
	private $productID = null;

	/** @var String|null The name of the product. */
	private $name = null;

	/** @var String|null The short description. */
	private $shortDescription = null;

	/** @var String|null The description. */
	private $description = null;

	/** @var boolean Is this product for sale? */
	private $forSale = true;

	/** @var boolean Is this product a special offer? */
	private $specialOffer = false;

	/** @var String|null The text of availibility. */
	private $availibilityText = null;

	/** @var Images[] This are the images in the four different possibilities. */
	private $images = array();

	/** @var ProductPriceWithQuantity|null Here the price is saved. */
	private $price = null;

	/** @var ProductPrice|null Here the deposit price is saved. */
	private $depositPrice = null;

	/** @var ProductPrice|null Here the eco participation price is saved. */
	private $ecoParticipationPrice = null;

	/** @var ProductPrice|null Here the price with deposit is saved. */
	private $withDepositPrice = null;

	/** @var ProductPrice|null Here the manufacturer price is saved. */
	private $manufacturerPrice = null;

	/** @var ProductPrice|null Here the base price is saved. */
	private $basePrice = null;

	/** @var ProductSlideshow|null This object saves the slideshow. */
	private $slideshow = null;

	/** @var ProductAttribute[] This array saves all the attributes. */
	private $attributes = array();

	/** @var float|null Space to save the stocklevel. */
	private $stockLevel = null;

	/** @var int Timestamp in ms when the next request needs to be done. */
	private $NEXT_REQUEST_TIMESTAMP = 0;

	/**
	 * This is the constructor of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Add price information.
	 * @since 0.1.0 Use a default Locale.
	 * @since 0.1.1 Dont use the locale parameter in calling the product price attribute.
	 * @since 0.1.2 Exclude the REST request to the load() function.
	 * @api
	 * @param mixed[] $productParameter The product to create as array.
	 */
	public function __construct($productParameter) {

		if (!InputValidator::isArray($productParameter) ||
			InputValidator::isEmptyArray($productParameter)) {
			return;
		}
		
		self::load($productParameter);
	}

	/**
	 * Loads the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param Array $product The product in an array.
	 */
	private function load($productParameter) {

		// if the product comes from the shop API
		if (InputValidator::isArray($productParameter) &&
			!InputValidator::isEmptyArrayKey($productParameter, "productId")) {

			$this->productID = $productParameter['productId'];

			// load locale depended content
			if (!InputValidator::isEmptyArrayKey($productParameter, "forSale")) {
				$this->forSale = $productParameter['forSale'];
			}
			if (!InputValidator::isEmptyArrayKey($productParameter, "specialOffer")) {
				$this->specialOffer = $productParameter['specialOffer'];
			}
			if (!InputValidator::isEmptyArrayKey($productParameter, "name")) {
				$this->name = $productParameter['name'];
			}
			if (!InputValidator::isEmptyArrayKey($productParameter, "shortDescription")) {
				$this->shortDescription = $productParameter['shortDescription'];
			}
			if (!InputValidator::isEmptyArrayKey($productParameter, "description")) {
				$this->description = $productParameter['description'];
			}
			if (!InputValidator::isEmptyArrayKey($productParameter, "availabilityText")) {
				$this->availabilityText = $productParameter['availabilityText'];
			}

			// parse images
			if (!InputValidator::isEmptyArrayKey($productParameter, "images")) {
				foreach ($productParameter['images'] as $image) {
					if (InputValidator::isArray($image) &&
						!InputValidator::isEmptyArrayKey($image, "classifier") &&
						!InputValidator::isEmptyArrayKey($image, "url")) {
						$this->images[$image['classifier']] = new Image($image['url']);
					}
				}
			}

			// parse price
			if (!InputValidator::isEmptyArrayKey($productParameter, "priceInfo")) {

				$priceInformation = $productParameter['priceInfo'];

				if (!InputValidator::isEmptyArrayKey($priceInformation, "price") &&
					!InputValidator::isEmptyArrayKey($priceInformation, "quantity")) {
					$this->price = new ProductPriceWithQuantity($this->productID, ProductPriceType::PRICE, $priceInformation['price'], $priceInformation['quantity']);
				}
				if (!InputValidator::isEmptyArrayKey($priceInformation, "depositPrice")) {
					$this->depositPrice = new ProductPrice($this->productID, ProductPriceType::DEPOSIT, $priceInformation['depositPrice']);
				}
				if (!InputValidator::isEmptyArrayKey($priceInformation, "ecoParticipationPrice")) {
					$this->ecoParticipationPrice = new ProductPrice($this->productID, ProductPriceType::ECOPARTICIPATION, $priceInformation['ecoParticipationPrice']);
				}
				if (!InputValidator::isEmptyArrayKey($priceInformation, "priceWithDeposits")) {
					$this->withDepositPrice = new ProductPrice($this->productID, ProductPriceType::WITHDEPOSITS, $priceInformation['priceWithDeposits']);
				}
				if (!InputValidator::isEmptyArrayKey($priceInformation, "manufacturerPrice")) {
					$this->manufacturerPrice = new ProductPrice($this->productID, ProductPriceType::MANUFACTURER, $priceInformation['manufacturerPrice']);
				}
				if (!InputValidator::isEmptyArrayKey($priceInformation, "basePrice")) {
					$this->basePrice = new ProductPrice($this->productID, ProductPriceType::BASE, $priceInformation['basePrice']);
				}
			}
		}
	}

	/**
	 * Returns the product id.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return String The product id.
	 */
	public function getID() {

		return $this->productID;
	}

	/**
	 * Returns the name.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale.
	 * @since 0.1.1 Fix to call function without locale parameter.
	 * @api
	 * @return String The name.
	 */
	public function getName() {

		return $this->name;
	}

	/**
	 * Sets the name of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $name The new name.
	 */
	public function setName($name) {

		self::setAttribute("/name", $name);
	}

	/**
	 * Deletes the name value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetName() {

		self::unsetAttribute("/name");
	}

	/**
	 * Sets the product number of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $number The new product number.
	 */
	public function setNumber($number) {

		self::setAttribute("/productNumber", $number);
	}

	/**
	 * Returns the short description.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale.
	 * @since 0.1.1 Fix to call function without locale parameter.
	 * @api
	 * @return String The short description.
	 */
	public function getShortDescription() {

		return $this->shortDescription;
	}

	/**
	 * Returns the description.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale.
	 * @since 0.1.1 Fix to call function without locale parameter.
	 * @api
	 * @return String The description.
	 */
	public function getDescription() {

		return $this->description;
	}

	/**
	 * Sets the description of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $description The new description.
	 */
	public function setDescription($description) {

		self::setAttribute("/description", $dscription);
	}

	/**
	 * Deletes the description value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetDescription() {

		self::unsetAttribute("/description");
	}

	/**
	 * Sets the short description of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $shortDescription The new short description.
	 */
	public function setShortDescription($shortDescription) {

		self::setAttribute("/shortDescription", $shortDescription);
	}

	/**
	 * Deletes the short description value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetShortDescription() {

		self::unsetAttribute("/shortDescription");
	}

	/**
	 * Sets the energy labels string of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $energyLabelsString The new energy labels string.
	 */
	public function setEnergyLabelsString($energyLabelsString) {

		self::setAttribute("/energyLabelsString", $energyLabelsString);
	}

	/**
	 * Deletes the energy labels string value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetEnergyLabelsString() {

		self::unsetAttribute("/energyLabelsString");
	}

	/**
	 * Sets the manufacturer of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $manufacturer The new manufacturer.
	 */
	public function setManufacturer($manufacturer) {

		self::setAttribute("/manufacturer", $manufacturer);
	}

	/**
	 * Deletes the manufacturer value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetManufacturer() {

		self::unsetAttribute("/manufacturer");
	}

	/**
	 * Sets the UPC of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $upc The new UPC.
	 */
	public function setUPC($upc) {

		self::setAttribute("/upc", $upc);
	}

	/**
	 * Deletes the UPC value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetUPC() {

		self::unsetAttribute("/upc");
	}

	/**
	 * Sets the EAN of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $ean The new EAN.
	 */
	public function setEAN($ean) {

		self::setAttribute("/ean", $ean);
	}

	/**
	 * Deletes the EAN value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetEAN() {

		self::unsetAttribute("/ean");
	}

	/**
	 * Sets the essential features of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $essentialFeatures The new essential features.
	 */
	public function setEssentialFeatures($essentialFeatures) {

		self::setAttribute("/essentialFeatures", $essentialFeatures);
	}

	/**
	 * Deletes the essential features value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetEssentialFeatures() {

		self::unsetAttribute("/essentialFeatures");
	}

	/**
	 * Sets the search keywords of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $searchKeywords The new $search keywords.
	 */
	public function setSearchKeywords($searchKeywords) {

		self::setAttribute("/searchKeywords", $searchKeywords);
	}

	/**
	 * Deletes the search keywords of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	public function unsetSearchKeywords() {

		self::unsetAttribute("/searchKeywords");
	}
	
	/**
	 * Sets an attribute of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $path The path to this attribute.
	 * @param String $value The new attribute value.
	 */
	private function setAttribute($path, $value) {

		// if parameter is no string or PATCH does not work
		if (!InputValidator::isString($value) || !RESTClient::setRequestMethod("PATCH")) {
			return;
		}
		
		$parameter = array("op" => "add", "path" => $path, "value" => $value);
		$productParameter = RESTClient::send(self::RESTPATH . "/" . $this->productID, $parameter);
		
		// update the product
		self::load($productParameter);
	}
	
	/**
	 * Unsets an attribute value of the product.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param String $path The path to this attribute.
	 */
	private function unsetAttribute($path) {

		// if PATCH does not work
		if (!RESTClient::setRequestMethod("PATCH")) {
			return;
		}
		
		$parameter = array("op" => "remove", "path" => $path);
		$productParameter = RESTClient::send(self::RESTPATH, $parameter);
		
		// update the product
		self::load($productParameter);		
	}

	/**
	 * Returns true if it is for sale.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return boolean True if it is for sale, false if not.
	 */
	public function isForSale() {

		return $this->forSale;
	}

	/**
	 * Returns true if it is a special offer.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return boolean True if it is a special offer, false if not.
	 */
	public function isSpecialOffer() {

		return $this->specialOffer;
	}

	/**
	 * Returns the availibility text.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @since 0.1.0 Use a default Locale.
	 * @since 0.1.1 Fix to call function without locale parameter.
	 * @api
	 * @return String The availibility text.
	 */
	public function getAvailibilityText() {

		return $this->availibilityText;
	}

	/**
	 * Returns the small image.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return Image The small image.
	 */
	public function getSmallImage() {

		return !InputValidator::isEmptyArrayKey($this->images, "Small") ? $this->images["Small"] : null;
	}

	/**
	 * Returns the medium image.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return Image The medium image.
	 */
	public function getMediumImage() {

		return !InputValidator::isEmptyArrayKey($this->images, "Medium") ? $this->images["Medium"] : null;
	}

	/**
	 * Returns the large image.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return Image The large image.
	 */
	public function getLargeImage() {

		return !InputValidator::isEmptyArrayKey($this->images, "Large") ? $this->images["Large"] : null;
	}

	/**
	 * Returns the hot deal image.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.0.0
	 * @api
	 * @return Image The hot deal image.
	 */
	public function getHotDealImage() {

		return !InputValidator::isEmptyArrayKey($this->images, "HotDeal") ? $this->images["HotDeal"] : null;
	}

	/**
	 * Returns the product price with quantity.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @return ProductPriceWithQuantity Gets the product price with quantity.
	 */
	public function getPrice() {

		return $this->price;
	}

	/**
	 * Returns the deposit price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @return ProductPrice Gets the deposit price.
	 */
	public function getDepositPrice() {

		return $this->depositPrice;
	}

	/**
	 * Returns the eco participation price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @return ProductPrice Gets the eco participation price.
	 */
	public function getEcoParticipationPrice() {

		return $this->ecoParticipationPrice;
	}

	/**
	 * Returns the with deposit price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @return ProductPrice Gets the with deposit price.
	 */
	public function getWithDepositPrice() {

		return $this->withDepositPrice;
	}

	/**
	 * Returns the manufacturer price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @return ProductPrice Gets the manufacturer price.
	 */
	public function getManufacturerPrice() {

		return $this->manufacturerPrice;
	}

	/**
	 * Returns the base price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @return ProductPrice Gets the base price.
	 */
	public function getBasePrice() {

		return $this->basePrice;
	}

	/**
	 * Returns the slideshow.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Slideshows are not reloadable since now.
	 * @api
	 * @return ProductSlideshow Gets the product slideshow.
	 */
	public function getSlideshow() {

		// if the slideshow is not loaded until now
		if (InputValidator::isEmpty($this->slideshow)) {
			$this->slideshow = new ProductSlideshow($this->productID);
		}
		return $this->slideshow;
	}

	/**
	 * Returns the product attributes.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 * @api
	 * @return ProductAttributes[] Gets the product attributes in an array.
	 */
	public function getAttributes() {

		$timestamp = (int) (microtime(true) * 1000);

		// if the attribute is not loaded until now
		if (InputValidator::isEmptyArray($this->attributes) ||
			$this->NEXT_REQUEST_TIMESTAMP < $timestamp) {
			$this->loadAttributes();
		}
		return $this->attributes;
	}

	/**
	 * Returns the product attributes.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @param int $key The number of product attribute to get.
	 * @return ProductAttributes|null Gets the required product attributes.
	 */
	public function getAttribute($key) {

		$timestamp = (int) (microtime(true) * 1000);

		// if the attribute is not loaded until now
		if (InputValidator::isEmptyArrayKey($this->attributes, $key) ||
			$this->NEXT_REQUEST_TIMESTAMP < $timestamp) {
			$this->loadAttributes();
		}

		if (InputValidator::isEmptyArrayKey($this->attributes, $key)) {
			return null;
		}
		return $this->attributes[$key];
	}

	/**
	 * Loads the product attributes.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 */
	private function loadAttributes() {

		// if parameter is wrong or GET is blocked
		if (!RESTClient::setRequestMethod(HTTPRequestMethod::GET)) {
			return;
		}

		$content = RESTClient::send(self::RESTPATH . "/" . $this->productID . "/" .  self::RESTPATH_ATTRIBUTES);

		// if respond is empty
		if (InputValidator::isEmpty($content)) {
			return;
		}

		// if there are items
		if (InputValidator::isEmptyArrayKey($content, "items")) {
		    Logger::error("Respond for " . self::RESTPATH . "/" . $this->productID . "/" .  self::RESTPATH_ATTRIBUTES . "can not be interpreted.");
			return;
		}

		// is there any attribute found: load the attribute.
		foreach ($content['items'] as $number => $attribute) {

			// parse every attribute
			$this->attributes[$number] = new ProductAttribute($attribute);
		}

		// update timestamp when make the next request
		$timestamp = (int) (microtime(true) * 1000);
		$this->NEXT_REQUEST_TIMESTAMP = $timestamp + RESTClient::$NEXT_RESPONSE_WAIT_TIME;
	}

	/**
	 * Returns the stock level.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 * @api
	 * @return float|null The stock level of the product.
	 */
	public function getStockLevel() {

		$timestamp = (int) (microtime(true) * 1000);

		// if the attribute is not loaded until now
		if (InputValidator::isEmpty($this->stockLevel) ||
			$this->NEXT_REQUEST_TIMESTAMP < $timestamp) {
			$this->loadStockLevel();
		}

		return $this->stockLevel;
	}

	/**
	 * Increases the stock level.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @param float $step The value the stock level should be increased, default value is 1.
	 * @return float|null The new stock level of the product.
	 */
	public function increaseStockLevel($step = 1.0) {

		$this->getStockLevel();

		// cast int to float
		if (InputValidator::isInt($step)) {
			$step = (float) $step;
		}

		if (!InputValidator::isRangedFloat($step, 0.0)) {
			return $this->stockLevel;
		}
		$this->changeStockLevel((float) $step);

		return $this->stockLevel;
	}

	/**
	 * Decreases the stock level.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @api
	 * @param float $step The value the stock level should be decreased, default value is 1.
	 * @return float|null The new stock level of the product.
	 */
	public function decreaseStockLevel($step = 1.0) {

		$this->getStockLevel();

		// cast int to float
		if (InputValidator::isInt($step)) {
			$step = (float) $step;
		}

		if (!InputValidator::isRangedFloat($step, 0.0)) {
			return $this->stockLevel;
		}
		$this->changeStockLevel((float) $step * -1);

		return $this->stockLevel;
	}

	/**
	 * Loads the stock level.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 */
	private function loadStockLevel() {

		// if parameter is wrong or GET is blocked
		if (!RESTClient::setRequestMethod(HTTPRequestMethod::GET)) {
			return;
		}

		$content = RESTClient::send(self::RESTPATH . "/" . $this->productID . "/" .  self::RESTPATH_STOCKLEVEL);

		// if respond is empty
		if (InputValidator::isEmpty($content) ||
			InputValidator::isEmptyArrayKey($content, "stocklevel")) {
			return;
		}

		$this->stockLevel = (float) $content["stocklevel"];

		// update timestamp when make the next request
		$timestamp = (int) (microtime(true) * 1000);
		$this->NEXT_REQUEST_TIMESTAMP = $timestamp + RESTClient::$NEXT_RESPONSE_WAIT_TIME;
	}
	/**
	 * Loads the stock level.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 * @param float $step The step to change.
	 */
	private function changeStockLevel($step) {

		// if parameter is wrong or GET is blocked
		if (!RESTClient::setRequestMethod(HTTPRequestMethod::PUT) ||
			!InputValidator::isFloat($step)) {
			return;
		}

		$postfields = array("changeStocklevel" => $step);
		$content = RESTClient::send(self::RESTPATH . "/" . $this->productID . "/" .  self::RESTPATH_STOCKLEVEL, $postfields);

		// if respond is empty
		if (InputValidator::isEmpty($content) ||
			InputValidator::isEmptyArrayKey($content, "stocklevel")) {
			return;
		}

		$this->stockLevel = (float) $content["stocklevel"];

		// update timestamp when make the next request
		$timestamp = (int) (microtime(true) * 1000);
		$this->NEXT_REQUEST_TIMESTAMP = $timestamp + RESTClient::$NEXT_RESPONSE_WAIT_TIME;
	}

	/**
	 * Deletes itself.
	 *
	 * Dont use this function. To delete a product its better to use $shop->deleteProduct($product).
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.0
	 * @since 0.1.1 Unstatic every attributes.
	 * @return boolean True if the deletion was successful, false if not.
	 */
	public function delete() {

		// if request method is blocked
		if (!RESTClient::setRequestMethod(HTTPRequestMethod::DELETE)) {
			return false;
		}

		RESTClient::send(self::RESTPATH . "/" . $this->productID);

		return true;
	}

	/**
	 * Prints the Product object as a string.
	 *
	 * This function returns the setted values of the Product object.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.1
	 * @return String The Product as a string.
	 */
	public function __toString() {

		return "<strong>Product ID:</strong> " . $this->productID . "<br/>" .
				"<strong>Name:</strong> " . $this->name . "<br/>" .
				"<strong>Short description:</strong> " . $this->shortDescription . "<br/>" .
				"<strong>Description:</strong> " . $this->description . "<br/>" .
				"<strong>For sale:</strong> " . $this->forSale . "<br/>" .
				"<strong>Special offer:</strong> " . $this->specialOffer . "<br/>" .
				"<strong>Availibility text:</strong> " . $this->availibilityText . "<br/>" .
				"<strong>Images:</strong> " . print_r($this->images) . "<br/>" .
				"<strong>Price:</strong> " . $this->price . "<br/>" .
				"<strong>Deposit price:</strong> " . $this->depositPrice . "<br/>" .
				"<strong>Ecoparticipation price:</strong> " . $this->ecoParticipationPrice . "<br/>";
	}
}

?>