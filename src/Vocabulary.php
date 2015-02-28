<?php

namespace Drupal\yandex_market_xml;

use Drupal\yandex_market_xml\Item;

/**
 * Class of vocabulary which provides XML for categories element of YML
 * @author Korotkov D.
 */
class Vocabulary extends Item
{
	/**
	 * Get all vocabularies for settings form
	 * @see https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7
	 * @return array
	 */
	public function all()
	{
		$aList = array();
		$aVocabularies = taxonomy_get_vocabularies();
		foreach($aVocabularies as $oVocabulary)
		{
			$aList[$oVocabulary->vid] = $oVocabulary->name;
		}
		return $aList;
	}
	
	/**
	 * Check if vocabulary is correct
	 * @param integer $piVocabulary view and display with ' ' delimiter
	 * @return boolean
	 */
	public function check($piVocabulary)
	{
		return is_numeric($piVocabulary);
	}
	
	/**
	 * Get categories XML
	 * @return \DOMDocument document or boolean false on error
	 */
	public function xml()
	{
		$vResult = false;
		do
		{
			/**
			 * Categories element
			 * @see http://help.yandex.ru/partnermarket/categories.xml
			 */
			$iVocabulary = $this->get();
			if(is_null($iVocabulary))
			{
				break;
			}
			$oDocument = new \DOMDocument();
			$oCategoriesElement = $oDocument->createElement('categories');
			$aCategories = taxonomy_get_tree($iVocabulary);
			foreach($aCategories as $oCategory)
			{
				$oCategoryElement = $oDocument->createElement('category', $oCategory->name);
				$oCategoryElement->setAttribute('id', $oCategory->tid);
				if(!empty($oCategory->parents) && reset($oCategory->parents) != 0)
				{
					$oCategoryElement->setAttribute('parentId', reset($oCategory->parents));
				}
				$oCategoriesElement->appendChild($oCategoryElement);
			}
			$oDocument->appendChild($oCategoriesElement);
			$vResult = $oDocument;
		}while(0);
		return $vResult;
	}
}
