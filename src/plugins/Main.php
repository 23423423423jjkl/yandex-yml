<?php

namespace Drupal\yandex_market_xml\plugins;

use Drupal\yandex_market_xml\Item;

/**
 * Class of currencies plugins which provide XML for currencies element of YML.
 * Each plugin - file in current directory, which is instance of Currency class
 * @author Korotkov D.
 */
class Main extends Item
{
	/**
	 * Get all currencies plugins for settings form
	 * @see https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7
	 * @return array
	 */
	public function all()
	{
		$aPlugins = scandir(__DIR__);
		$aPluginsOptions = array();
		foreach($aPlugins as $sPlugin)
		{
			$sClass = static::load($sPlugin);
			if($sClass !== false)
			{
				$aPluginsOptions[$sPlugin] = $sClass::title();
			}
		}
		return $aPluginsOptions;
	}
	
	/**
	 * Check if plugin is correct
	 * @param string $psFile plugin file
	 * @return boolean
	 */
	public function check($psFile)
	{
		return (static::load($psFile) !== false);
	}
	
	/**
	 * Load plugin by file or default plugin from settings if $psFile is null
	 * @param string $psFile plugin file
	 * @return string checked class of false if check failed
	 */
	public function load($psFile = null)
	{
		$vResult = false;
		do
		{
			if(is_null($psFile))
			{
				$sFile = static::get();
			}
			else
			{
				$sFile = $psFile;
			}
			if(is_null($sFile))
			{
				break;
			}
			$sClass = basename($sFile, '.php');
			$sNamespace = 'Drupal\\yandex_market_xml\\plugins\\' . $sClass;
			if
			(
				!class_exists($sNamespace) 
				|| 
				!in_array('Drupal\\yandex_market_xml\\plugins\\Currency', class_implements($sNamespace))
			)
			{
				break;
			}
			$vResult = $sNamespace;
		}while(0);
		return $vResult;
	}
	
	/**
	 * Get currencies XML
	 * @return \DOMDocument document or boolean false on error
	 */
	public function xml()
	{
		$vResult = false;
		do
		{
			
			/**
				* Currencies element
				* @see http://help.yandex.ru/partnermarket/currencies.xml
				*/
			 $sClass = $this->load();
			 if($sClass === false)
			 {
				 break;
			 }
			 $oDocument = new \DOMDocument();
			 $aCurrencies = $sClass::currencies();
			 $sDefaultCurrency = $sClass::defaultCurrency();
			 $oCurrenciesElement = $oDocument->createElement('currencies');
			 foreach($aCurrencies as $i => $aCurrency)
			 {
				 $oCurrency = $oDocument->createElement('currency');
				 $oCurrency->setAttribute('id', $i);
				 if(is_numeric($aCurrency['conversion_rate']))
				 {
					 $sRate = $aCurrency['conversion_rate'];
				 }
				 elseif($i == $sDefaultCurrency)
				 {
					 $sRate = '1';
				 }
				 else
				 {
					 $sRate = 'CB';
				 }
				 $oCurrency->setAttribute('rate', $sRate);
				 $oCurrenciesElement->appendChild($oCurrency);
			 }
			 $oDocument->appendChild($oCurrenciesElement);
			 $vResult = $oDocument;
		}while(0);
		return $vResult;
	}
}
