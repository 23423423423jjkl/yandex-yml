<?php

namespace Drupal\yandex_market_xml;

use Drupal\yandex_market_xml\plugins\Main as Plugin;
use Drupal\yandex_market_xml\Vocabulary;
use Drupal\yandex_market_xml\View;

/**
 * Main class - main functions
 * @author Korotkov D.
 */
class Main 
{
	/**
	 * Get form inputs settings for drupal form
	 * @return array key is input name, value is array of format: <br>
	 *	class - object of Drupal\yandex_market_xml\Item type, <br>
	 *	title - input title, <br>
	 *	description - input description.
	 */
	public static function formSettings()
	{
		$aFormSettings = array
		(
			'vocabulary' => array
			(
				'class' => new Vocabulary(), 
				'title' => t('Categories vocabulary'), 
				'description' => t("Categories vocabulary for exporting in YML")
			),
			'view' => array
			(
				'class' => new View(), 
				'title' => t('Categories vocabulary'), 
				'description' => t("Categories vocabulary for exporting in YML")
			),
			'plugin' => array
			(
				'class' => new Plugin(), 
				'title' => t('Currencies plugin'), 
				'description' => t("Currencies plugin for exporting in YML")
			)
		);
		return $aFormSettings;
	}
	
	/**
	 * Get drupal form array for settings
	 * @see https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7
	 * @return array
	 */
	public static function drupalForm()
	{
		$aFormSettings = Main::formSettings();
		$aForm = array();
		foreach($aFormSettings as $sKey => $aSettings)
		{
			$aForm[$sKey] = array
			(
				'#type' => 'select',
				'#title' => $aSettings['title'],
				'#options' => $aSettings['class']->all(),
				'#description' => $aSettings['description'],
				'#required' => TRUE,
			);
			$vDefault = $aSettings['class']->get();
			if(!is_null($vDefault))
			{
				$aForm[$sKey]['#default_value'] = $vDefault;
			}
		}
		$aForm['submit_button'] = array
		(
			'#type' => 'submit',
			'#value' => t('Save'),
		);
		return $aForm;
	}
	
	/**
	 * Validate settings vorm.
	 * @param array $paValues form values
	 */
	public static function drupalFormValidate(array $paValues)
	{
		$aFormSettings = Main::formSettings();
		foreach($aFormSettings as $sKey => $aSetting)
		{
			if(!isset($paValues['values'][$sKey]) || !$aSetting['class']->check($paValues['values'][$sKey]))
			{
				form_set_error($sKey, t('Bad value - check failed'));	
			}
		}
	}
	
	/**
	 * Submit settings
	 * @param array $paValues form values
	 */
	public static function drupalFormSubmit(array $paValues)
	{
		$aFormSettings = Main::formSettings();
		foreach($aFormSettings as $sKey => $aSetting)
		{
			$aSetting['class']->set($paValues['values'][$sKey]);
		}
	}
	
	/**
	 * Get Yandex Market XML
	 * @return string xml or empty string on error
	 */
	public static function yml()
	{
		$sResult = '';
		do
		{
			$oView = new View();
			$oOffers = $oView->xml();
			if($oOffers === false)
			{
				break;
			}
			
			$oCurrency = new Plugin();
			$oCurrencies = $oCurrency->xml();
			if($oCurrencies === false)
			{
				break;
			}
			
			$oVocabulary = new Vocabulary();
			$oCategories = $oVocabulary->xml();
			if($oCategories === false)
			{
				break;
			}
			
			/**
			 * Document
			 * @see http://help.yandex.ru/partnermarket/xml-header.xml
			 */
			$oDim = new \DOMImplementation();
			$oDocumentType = $oDim->createDocumentType('yml_catalog', '', 'shops.dtd');
			$oDocument = $oDim->createDocument(null, null, $oDocumentType);
			$oDocument->encoding = 'UTF-8';
			
			/**
			 * Root element
			 * @see http://help.yandex.ru/partnermarket/yml-catalog.xml
			 */
			$oRootElement = $oDocument->createElement('yml_catalog');
			$oDate = new \DateTime();
			$oRootElement->setAttribute('date', $oDate->format('Y-m-d H:i'));
			$oDocument->appendChild($oRootElement);

			$oShopElement = $oDocument->createElement('shop');

			$oSiteName = $oDocument->createElement('name', variable_get('site_name', 'Drupal'));
			$oShopElement->appendChild($oSiteName);

			$oCompanyName = $oDocument->createElement('company', variable_get('site_name', 'Company'));
			$oShopElement->appendChild($oCompanyName);

			$sURL = rtrim($GLOBALS['base_url'], '/');
			if(!is_null(variable_get('site_frontpage')))
			{
				$sURL .= '/' . trim(variable_get('site_frontpage'), '/');
			}
			$oSiteURL = $oDocument->createElement('url', $sURL);
			$oShopElement->appendChild($oSiteURL);

			$oCMSName = $oDocument->createElement('platform', 'Drupal');
			$oShopElement->appendChild($oCMSName);

			$oCMSVersion = $oDocument->createElement('version', VERSION);
			$oShopElement->appendChild($oCMSVersion);

			$oAgency = $oDocument->createElement('agency', 'Korotkov D. - yandex market module developer');
			$oShopElement->appendChild($oAgency);

			$oEmail = $oDocument->createElement('email', 'dimakorotkov@mail.ru');
			$oShopElement->appendChild($oEmail);

			$oRootElement->appendChild($oShopElement);
			
			foreach($oCurrencies->childNodes as $oFirstChild)
			{
				$oSecondChild = $oDocument->importNode($oFirstChild, true);
				$oShopElement->appendChild($oSecondChild);
			}
			
			foreach($oCategories->childNodes as $oFirstChild)
			{
				$oSecondChild = $oDocument->importNode($oFirstChild, true);
				$oShopElement->appendChild($oSecondChild);
			}
			
			foreach($oOffers->childNodes as $oFirstChild)
			{
				$oSecondChild = $oDocument->importNode($oFirstChild, true);
				$oShopElement->appendChild($oSecondChild);
			}
			
			$sResult = $oDocument->saveXML();
			
		}while(0);
		return $sResult;
	}

}
