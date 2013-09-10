<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   MageBrews
 * @package    MageBrews_Locator
 * @author     Andrew Kett
 */
class MageBrews_Locator_Model_Search extends MageBrews_Locator_Model_Search_Abstract
{
    const XML_SEARCH_OVERRIDES_PATH = "locator_settings/search/overrides_enabled";
    const XML_SEARCH_USEDEFAULT_PATH = "locator_settings/search/use_default_search";
    const XML_SEARCH_USE_CUSTOMER_ADDRESS = "locator_settings/search/use_customer_address";

    var $params = array();

    /**
     * Perform search based on params passed
     *
     * @param Array params
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params = null)
    {
        $this->params = $params;
        $this->depth = 0;



        if($this->getSearchClass()){
            return $this->getSearchClass()->search($this->params);
        }else{
            //@todo show search form with no results
        }
    }


    /**
     * Find appropriate search class based on params passed
     *
     * @return MageBrews_Locator_Model_Search_Abstract
     */
    protected function getSearchClass()
    {
        $params = $this->params;

        if(isset($params['s']))
        {
            if(Mage::getStoreConfig(self::XML_SEARCH_OVERRIDES_PATH)){
                //check db for custom searches matching this one
                $override = Mage::getModel('magebrews_locator/search_override')->load($params['s']);
                if($override->getParams() && $this->depth < 1){
                    $this->depth++;
                    $this->params = Mage::helper('magebrews_locator/search')->parseQueryString($override->getParams());
                    return $this->getSearchClass($this->params);
                }
            }

            return Mage::getModel('magebrews_locator/search_point_string');
        }
        else if(isset($params['lat']) && isset($params['long']))
        {
            return Mage::getModel('magebrews_locator/search_point_latlong');
        }
        else if(isset($params['a']) || isset($params['country']) || isset($params['postcode']))
        {
            return Mage::getModel('magebrews_locator/search_area');
        }
        else{

            if(Mage::getStoreConfig(self::XML_SEARCH_USE_CUSTOMER_ADDRESS)){
                //if customer is logged in and they have an address use that
                $session = Mage::getSingleton('customer/session');
                if($session->isLoggedIn() && $session->getCustomer()->getDefaultBilling()){
                    $addressId = $session->getCustomer()->getDefaultBilling();

                    $address = Mage::getModel('customer/address')->load($addressId);
                    $street = $address->getStreet();

                    $search = @$street[0].' '.@$street[1].', '.$address->getCity().', '.$address->getRegion().', '.$address->getPostcode().', '.$address->getCountry();
                    $searchModel = Mage::getModel('magebrews_locator/search_point_string');
                    $newParams = array('s'=>$search, 'distance'=>500);

                    //if there are results close to the customer use that
                    //otherwise just fallback to default search
                    if($searchModel->search($newParams)->getItems()){
                        $this->params = array_merge($newParams, $this->params);
                        return $searchModel;
                    }
                }
            }

            if(Mage::getStoreConfig(self::XML_SEARCH_USEDEFAULT_PATH)){
                $params = Mage::getStoreConfig('locator_settings/search/default_search_params');
                $this->params = Mage::helper('magebrews_locator/search')->parseQueryString($params);
                return $this->getSearchClass($params);
            }

            return false;
        }
    }

}
