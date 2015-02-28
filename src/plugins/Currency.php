<?php

namespace Drupal\yandex_market_xml\plugins;

/**
 * Interface of currency plugin - provide currencies info for different commerce systems
 * @author Korotkov D.
 */
interface Currency 
{
	/**
	 * Plugin title
	 * @return string
	 */
	public static function title();
	
	/**
	 * Get all active currencies
	 * @return array key is identifier, value is array with rate element
	 */
	public static function currencies();
	
	/**
	 * Get default currency
	 * @return string default currency identifier
	 */
	public static function defaultCurrency();
}
