<?php

namespace Drupal\yandex_market_xml;

use Drupal\yandex_market_xml\Item;

/**
 * Class of view which provides products XML for offers element of YML
 * @author Korotkov D.
 */
class View extends Item
{
	/**
	 * Get all views displays for settings form
	 * @see https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7
	 * @return array
	 */
	public function all()
	{
		$aViews = views_get_all_views();
		$aViewsOptions = array();
		foreach($aViews as $sName => $aView)
		{
			if(!isset($aViewsOptions[$sName]))
			{
				$aViewsOptions[$sName] = array();
			}
			foreach($aView->display as $sDisplay => $aDisplay)
			{
				$aViewsOptions[$sName][$sName . ' ' . $sDisplay] = $sName . ' ' . $sDisplay;
			}
		}
		return $aViewsOptions;
	}
	
	/**
	 * Check if view display is correct
	 * @param string $pvName view and display with ' ' delimiter
	 * @return boolean
	 */
	public function check($pvName)
	{
		return true;
	}
	
	/**
	 * Get offers XML
	 * @return \DOMDocument document or boolean false on error
	 */
	public function xml()
	{
		$vResult = false;
		do
		{
			$sView = $this->get();
			if(is_null($sView))
			{
				break;
			}
			$aViewParams = explode(' ', $sView);
			if(count($aViewParams) <> 2)
			{
				break;
			}
			$sOffers = views_embed_view($aViewParams[0], $aViewParams[1]);
			$oOffersDocument = new \DOMDocument();
			$oOffersDocument->loadXML($sOffers);
			foreach($oOffersDocument->childNodes as $oOffers)
			{
				$aOffers = array();
				foreach($oOffers->childNodes as $oOffer)
				{
					$this->processOffer($oOffer);
				}
			}
			$vResult = $oOffersDocument;
		}while(0);
		return $vResult;
	}
	
	/**
	 * Process offer xml - move id, type, available, bid tags to offer attribute
	 */
	protected function processOffer(&$poOffer)
	{
		$aRemove = array();
		foreach($poOffer->childNodes as $oProperty)
		{
			if(in_array($oProperty->nodeName, array('id', 'type', 'available', 'bid')))
			{
				$poOffer->setAttribute($oProperty->nodeName, $oProperty->nodeValue);
				$aRemove[] = $oProperty;
			}
		}
		foreach($aRemove as $oRemoveNode)
		{
			$poOffer->removeChild($oRemoveNode);
		}
		return $poOffer;
	}
}
