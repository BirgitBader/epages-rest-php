<?php
/**
 * This file represents a special product price.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.1.2
 */
namespace ep6;
/**
 * This is the class for prices which belongs to a product.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.1.2
 * @package ep6
 * @subpackage Shopobjects\Price
 */
class ProductPrice extends Price {

	/** @var String|null The refered product ID. */
	private $productID = null;

	/** @var ProductPriceType|null The type of price. */
	private $type = null;

	/**
	 * This is the constructor of the product price object.
	 *
	 * @api
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @param String $productID The product ID to which this price belongs.
	 * @param ProductPriceType $type The type of the product price.
	 * @param mixed[] $priceParameter The price parameter.
	 */
	public function __construct($productID, $type, $priceParameter) {

		// if the first parameter is no product ID
		if (!InputValidator::isProductID($productID)) {
			return;
		}

		$this->productID = $productID;
		$this->type = $type;
		parent::__construct($priceParameter);
	}
	
	/**
	 * Sets an the amount of a product price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 * @param float $amount The new amount of price.
	 */
	private function setAmount($amount) {

		$allowedTypes = array(ProductPriceTypes::PRICE, ProductPriceTypes::MANUFACTURER, ProductPriceTypes::ECOPARTICIPATION, ProductPriceTypes::DEPOSIT);

		// if parameter is no float, PATCH does not work or this operation is not allowed for this price type
		if (!InputValidator::isFloat($amount) || !RESTClient::setRequestMethod("PATCH") ||
			InputValidator::isEmptyArrayKey($allowedTypes, $this->type)) {
			return;
		}
		
		$parameter = array("op" => "add", "path" => "/priceInfo/" . $this->type . "/amount", "value" => $amount);
		RESTClient::send("product/" . $this->productID, $parameter);
	}
	
	/**
	 * Unsets the amount of a product price.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @api
	 */
	private function unsetAmount() {
		
		$allowedTypes = array(ProductPriceTypes::PRICE, ProductPriceTypes::MANUFACTURER, ProductPriceTypes::ECOPARTICIPATION, ProductPriceTypes::DEPOSIT);

		// if PATCH does not work or this operation is not allowed for this price type
		if (!RESTClient::setRequestMethod("PATCH") ||
			InputValidator::isEmptyArrayKey($allowedTypes, $this->type)) {
			return;
		}
		
		$parameter = array("op" => "remove", "path" => "/priceInfo/" . $this->type . "/amount");
		RESTClient::send("product/" . $this->productID, $parameter);
	}

	/**
	 * Prints the Product Price object as a string.
	 *
	 * This function returns the setted attributes of the Product Price object.
	 *
	 * @author David Pauli <contact@david-pauli.de>
	 * @since 0.1.2
	 * @return String The Product Price as a string.
	 */
	public function __toString() {

		return "<strong>Product ID:</strong>" . $this->productID . "<br/>" .
				"<strong>Amount:</strong> " . $this->amount . "<br/>" .
				"<strong>Tax type:</strong> " . $this->taxType . "<br/>" .
				"<strong>Currency:</strong> " . $this->currency . "<br/>" .
				"<strong>Formatted:</strong> " . $this->formatted . "<br/>";
	}
}
?>