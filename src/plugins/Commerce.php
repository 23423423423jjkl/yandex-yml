<?php

namespace Drupal\yandex_market_xml\plugins;

use Drupal\yandex_market_xml\plugins\Currency;

/**
 * Drupall commerce currency plugin, depends on drupal commerce module
 * @author Korotkov D.
 */
class Commerce implements Currency
{
	/**
	 * Plugin title
	 * @return string
	 */
	public static function title()
	{
		return t('Commerce currencies');
	}
	
	/**
	 * Get all active currencies
	 * @return array key is identifier, value is array which contains rate element
	 */
	public static function currencies()
	{
		return commerce_currencies(true);
	}
	
	/**
	 * Get default currency
	 * @return string default currency identifier
	 */
	public static function defaultCurrency()
	{
		return commerce_default_currency();
	}
}
