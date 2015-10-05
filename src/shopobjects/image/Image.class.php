<?php
namespace ep6;
/**
 * This is the image class which is used for images.
 */
class Image {
	
	/**
	 * This is the path to the origin URL.
	 */
	private $URL;
	
	/*
	 * To create a new image object use this constructor with the original URL.
	 *
	 * @param $url	The origin URL of the image. 
	 */
	public function __construct($url) {
		$this->URL = $url;
	}
	
	/**
	 * Gets the original URL of the image.
	 *
	 * @return String	The original URL.
	 */
	public function getOriginURL() {
		return $this->URL;
	} 
}