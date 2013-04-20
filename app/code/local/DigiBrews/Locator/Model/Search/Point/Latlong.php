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
class DigiBrews_Locator_Model_Search_Point_Latlong 
    extends DigiBrews_Locator_Model_Search_Point_Abstract
{

    /**
     * Geocode a search string into a Lat/Long Point
     *
     * @param Array $params
     * @return DigiBrews_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params)
    {
        if(!isset($params['lat']) || !isset($params['long']))
        {
            throw new Exception('Both latitude and longitude values are required to do a lat/long search');
        }

        $point = $this->latLongToPoint($params['lat'],$params['long']);
        return $this->pointToLocations($point, @$params['distance']);
    }


    /**
     * Convert a lat/long value into a point object
     *
     * @param Point $point
     * @param int $distance
     * @return Point
     */
    public function latLongToPoint($lat, $long)
    {
        include_once(Mage::getBaseDir('lib').'/geoPHP/geoPHP.inc');
        $result = new Point($long, $lat);
        return $result;
    }   
}
