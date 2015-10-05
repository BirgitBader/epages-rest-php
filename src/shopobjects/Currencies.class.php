<?php
namespace ep6;
/**
 * This is the static class for the currencies in the shop.
 *
 * Use it like the following code:
 *   Currencies::getDefault();
 *   Currencies::getItems();
 */
class Currencies {
	
	/**
	 * The REST path for currencies.
	 */
	const RESTPATH = "currencies";
	
	/**
	 * Space to save the default currencies.
	 */
	private static $DEFAULT = null;
	
	/**
	 * Space to save the possible currencies.
	 */
	private static $ITEMS = array();
	
	/**
	 * Gets the default and possible currencies of the shop.
	 */
	private static function load() {

		// if request method is blocked
		if (!RESTClient::setRequestMethod("GET")) {
			return;
		}

		$content = RESTClient::send(self::RESTPATH);
		
		// if respond is empty
		if (InputValidator::isEmpty($content)) {
			return;
		}
		
		// if there is no default AND items element
		if (!array_key_exists("default", $content) || !array_key_exists("items", $content)) {
		    Logger::error("Respond for " . self::RESTPATH . " can not be interpreted.");
			return;
		}
		
		// reset values
		self::resetValues();
		
		// save the default currency
		self::$DEFAULT = $content["default"];
		
		// parse the possible currencies
		self::$ITEMS = $content["items"];
	}

	/**
	 * This function resets all curencies values.
	 */
	public static function resetValues() {

		self::$DEFAULT = null;
		self::$ITEMS = array();
	}
	
	/**
	 * Gets the default currency.
	 *
	 * @return The default currencies of the shop.
	 */
	public static function getDefault() {
		
		if (self::$DEFAULT == null) {
			self::load();
		}
		return (self::$DEFAULT == null) ? null : self::$DEFAULT;
	}
	
	/**
	 * Gets the activated currencies.
	 *
	 * @return The possible currencies of the shop.
	 */
	public static function getItems() {
		
		if (empty(self::$ITEMS)) {
			self::load();
		}
		return (empty(self::$ITEMS)) ? null : self::$ITEMS;
	}

}
?>