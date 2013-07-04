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
class MageBrews_Locator_Model_Search_Point_String extends MageBrews_Locator_Model_Search_Point_Abstract
{
    const XML_SEARCH_APIKEY_PATH = "locator_settings/google_maps/api_key";
    const XML_SEARCH_SHOULDAPPEND_PATH = "locator_settings/search/append_string_to_search";
    const XML_SEARCH_APPENDTEXT_PATH = "locator_settings/search/append_string";


    /**
     * Perform search
     *
     * @param array $params
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     * @throws Exception
     */
    public function search(Array $params)
    {
        if(!isset($params['s']))
        {
            throw new Exception('A search string must be passed to perform a string search');
        }
        $point = $this->stringToPoint($params['s']);
        return $this->pointToLocations($point, @$params['distance']);
    }


    /**
     * Geocode a search string into a Lat/Long Point
     *
     * @param $query
     * @return Point
     * @throws MageBrews_Locator_Model_Exception_Geocode
     * @throws Exception
     */
    protected function stringToPoint($query)
    {
        $cache = $this->getCache();

        $appendText = (Mage::getStoreConfig(self::XML_SEARCH_SHOULDAPPEND_PATH))?Mage::getStoreConfig(self::XML_SEARCH_APPENDTEXT_PATH):'';

        $query = $query.' '.$appendText;

        if(!$result = unserialize($cache->load('locator_string_to_point_'.$query))){

            $key = Mage::getStoreConfig(self::XML_SEARCH_APIKEY_PATH);

            try{
                $geocoder = new GoogleGeocode($key);
                $result = $geocoder->read($query);
                $cache->save(serialize($result), 'locator_string_to_point_'.$query);

            }catch(Exception $e){

                if(strpos($e->getMessage(), 'ZERO_RESULTS')){
                    throw new MageBrews_Locator_Model_Exception_Geocode($e->getMessage());
                }

                throw $e;
            }
        }
        
        return $result;
    }       
}
