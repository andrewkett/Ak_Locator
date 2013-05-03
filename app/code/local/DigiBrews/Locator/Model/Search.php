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
 * @category   DigiBrews
 * @package    DigiBrews_Locator
 * @author     Andrew Kett
 */
class DigiBrews_Locator_Model_Search extends DigiBrews_Locator_Model_Search_Abstract
{
    /**
     * Perform search based on params passed
     *
     * @param Array params
     * @return DigiBrews_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params = null)
    {
        return $this->getSearchClass($params)->search($params);
    }


    /**
     * Find appropriate search class based on params passed
     *
     * @param array $params
     * @return DigiBrews_Locator_Model_Resource_Location_Collection
     */
    protected function getSearchClass($params = array())
    {
        if(isset($params['s']))
        {
            return Mage::getModel('digibrews_locator/search_point_string');
        }
        else if(isset($params['lat']) && isset($params['long']))
        {
            return Mage::getModel('digibrews_locator/search_point_latlong');
        }
        else if(isset($params['a']) || isset($params['country']) || isset($params['postcode']))
        {
            return Mage::getModel('digibrews_locator/search_area');
        }
        else{
            return Mage::getModel('digibrews_locator/search_default');
        }
    }
}
