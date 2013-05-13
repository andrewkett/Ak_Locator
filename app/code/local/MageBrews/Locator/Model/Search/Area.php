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
class MageBrews_Locator_Model_Search_Area
    extends MageBrews_Locator_Model_Search_Abstract
{
    /**
     * Geocode a search string into a Lat/Long Point
     *
     * @param Array $params
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params)
    {
        if(!isset($params['a']) && 
            !isset($params['country']) && 
            !isset($params['administrative_area']) && 
            !isset($params['postcode']))
        {
            throw new Exception('At least one valid search parameter must be passed');
        }
     
        return $this->areaSearch($params);
    }


    /**
     * Find locations based on area attributes
     *
     * @param string name of the administrative area
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function areaSearch($params){
        
        $collection = $this->getSearchCollection();

        if(isset($params['a'])){
            $params['administrative_area'] = $params['a']; 
        } 
        
        if(isset($params['country'])){
            $collection->addAttributeToFilter('country',$params['country']);
        }

        if(isset($params['administrative_area'])){
            $collection->addAttributeToFilter('administrative_area',$params['administrative_area']);
        }

        if(isset($params['postcode'])){
            $collection->addAttributeToFilter('postcode',$params['postcode']);
        }

        //area searches cant be sorted by proximity so sort them by title
        $collection->setOrder('title');
        
        $collection->addAttributeToSelect('*');
        return $collection;
    }  
}
