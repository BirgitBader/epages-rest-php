<?php
namespace ep6;
/**
 * This class, used by a static way, checks whether a value is valid.
 *
 * To check whether a value is a host use:
 *   InputValidator::isHost(HOSTNAME);
 */
class InputValidator {

	/**
	 * Checks whether a parameter is a host.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a host, false if not.
	 */
	public static function isHost($parameter) {

		return self::isMatchRegex($parameter, "/^([a-zA-Z0-9]([a-zA-Z0-9\\-]{0,61}[a-zA-Z0-9])?\\.)+[a-zA-Z]{2,6}/", "host")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a shop.
	 * TODO: Finalize this function.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a shop, false if not.
	 */
	public static function isShop($parameter) {
		
		return !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a OAuth authentification token.
	 * TODO: Finalize this function.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a OAuth authentification token, false if not.
	 */
	public static function isAuthToken($parameter) {
		
		return !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a localization string.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a localization string, false if not.
	 */
	public static function isLocale($parameter) {
		
		return self::isMatchRegex($parameter, "/^[a-z]{2,4}_[A-Z]{2,3}$/", "Locale")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a currency string.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a currency string, false if not.
	 */
	public static function isCurrency($parameter) {
		
		return self::isMatchRegex($parameter, "/^[A-Z]{3}$/", "Currency")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a HTTP request method.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a HTTP request method, false if not.
	 */
	public static function isRequestMethod($parameter) {

		return self::isMatchRegex($parameter, "/^(GET|POST|PUT|DELETE|PATCH)/", "HTTP request method")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is an output ressource.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is an output ressource, false if not.
	 */
	public static function isOutputRessource($parameter) {

		return self::isMatchRegex($parameter, "/^(SCREEN)/", "output ressource")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a log level.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a log level, false if not.
	 */
	public static function isLogLevel($parameter) {

		return self::isMatchRegex($parameter, "/^(NOTIFICATION|WARNING|ERROR|NONE)/", "log level")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a REST command.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a REST command, false if not.
	 */
	public static function isRESTCommand($parameter) {

		return !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a JSON string.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the string is a JSON string, false if not.
	 */
	public static function isJSON($parameter) {

		return !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is an int with a range.
	 *
	 * @param int	$parameter	Int to check.
	 * @return boolean	True if the parameter is an int, false if not.
	 */
	public static function isRangedInt($parameter, $minimum = null, $maximum = null) {

		return self::isInt($parameter)
			&& (self::isInt($minimum) ? $parameter >= $minimum : true)
			&& (self::isInt($maximum) ? $parameter <= $maximum : true);
	}

	/**
	 * Checks whether a parameter is a product sort direction.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the parameter is a product sort direction, false if not.
	 */
	public static function isProductDirection($parameter) {

		return self::isMatchRegex($parameter, "/^(asc|desc)$/", "products sort direction")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a product sort parameter.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the parameter is a product sort parameter, false if not.
	 */
	public static function isProductSort($parameter) {

		return self::isMatchRegex($parameter, "/^(name|price)$/", "products sort parameter")
			&& !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is a valid product id.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the parameter is a valid product id, false if not.
	 */
	public static function isProductId($parameter) {

		return !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is an int.
	 *
	 * @param int	$parameter	Int to check.
	 * @return boolean	True if the parameter is an int, false if not.
	 */
	public static function isInt($parameter) {

		return is_int($parameter) && !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a parameter is an array.
	 *
	 * @param array	$parameter	Array to check.
	 * @return boolean	True if the parameter is an array, false if not.
	 */
	public static function isArray($parameter) {

		return is_array($parameter) && !self::isEmpty($parameter);
	}

	/**
	 * Checks whether a paramter is empty or null.
	 *
	 * @param String	$parameter	String to check.
	 * @return boolean	True if the parameter is empty or null, false if not.
	 */
	public static function isEmpty($parameter) {

		return (is_null($parameter) || ($parameter == ""));
	}

	/**
	 * Checks whether a parameter match a regex.
	 *
	 * @param String	$parameter	String to check.
	 * @param String	$regex		The regex to check.
	 * @param String	$type		The type which is validated.
	 * @return boolean	True if the string validates, false if not.
	 */
	private static function isMatchRegex($parameter, $regex, $type) {

		if(!preg_match($regex, $parameter)) {
			Logger::warning("<strong>InputValidator</strong> - This is not a <u>" . $type . "</u>: <i>" . $parameter . "</i>");
		}
		return preg_match($regex, $parameter);
	}

}
?>