<?php
namespace ep6;
/**
 * The log level 'enum'.
 *
 * Use this to define which log messages should be printed.
 *
 * @author David Pauli <contact@david-pauli.de>
 * @since 0.0.1
 * @package ep6
 * @subpackage Util\Logger
 */
abstract class LogLevel {
	/** @var String Use this to print all messages. **/
	const NOTIFICATION = "NOTIFICATION";
	/** @var String Use this to print only warnings and errors. **/
	const WARNING = "WARNING";
	/** @var String Use this to print only errors. **/
	const ERROR = "ERROR";
	/** @var String Use this to print no log messages. **/
	const NONE = "NONE";
	/** @var String This is only used for intern reasons. **/
	const FORCE = "FORCE";
}
?>