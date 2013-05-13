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
        return $this->getSearchClass($this->params)->search($this->params);
    }


    /**
     * Find appropriate search class based on params passed
     *
     * @param array $params
     * @return MageBrews_Locator_Model_Search_Abstract
     */
    protected function getSearchClass($params = array())
    {
        if(isset($params['s']))
        {
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

            //if customer is logged in and they have an address use that
            $session = Mage::getSingleton('customer/session');
            if($session->isLoggedIn()){
                $addressId = $session->getCustomer()->getDefaultBilling();

                $address = Mage::getModel('customer/address')->load($addressId);
                $street = $address->getStreet();

                $search = @$street[0].' '.@$street[1].', '.$address->getCity().', '.$address->getRegion().', '.$address->getPostcode().', '.$address->getCountry();
                $searchModel = Mage::getModel('magebrews_locator/search_point_string');
                $newParams = array('s'=>$search, 'distance'=>500);

                //if thre are results close to the customer use that
                //otherwise just fallback to default search
                if($searchModel->search($newParams)->getItems()){
                    $this->params = array_merge($newParams, $this->params);
                    return $searchModel;
                }
            }

            return Mage::getModel('magebrews_locator/search_default');
        }
    }
}
