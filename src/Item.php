<?php

namespace Drupal\yandex_market_xml;

/**
 * YML item - provide get and set setting for item and common methods for retrieve XML
 * @author Korotkov D.
 */
abstract class Item 
{
	/**
	 * Get all variants for options of select item of settings form
	 * @see https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7
	 * @return array
	 */
	abstract public function all();
	
	/**
	 * Check if variant is correct
	 * @param mixed $pvName item value
	 * @return boolean
	 */
	abstract public function check($pvName);
	
	/**
	 * Get XML
	 * @return \DOMDocument document or boolean false on error
	 */
	abstract public function xml();
	
	/**
	 * Save setting or delete if $pvName is null
	 * @param mixed $pvName value
	 */
	public function set($pvName)
	{
		if(is_null($pvName))
		{
			variable_del($pvName);
		}
		elseif(static::check($pvName) !== false)
		{
			variable_set(get_called_class(), $pvName);
		}
	}
	
	/**
	 * Get setting
	 * @return mixed setting or null if no set
	 */
	public function get()
	{
		return variable_get(get_called_class());
	}
}
