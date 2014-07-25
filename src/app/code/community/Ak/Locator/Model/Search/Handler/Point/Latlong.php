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
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

/**
 * @category   Ak
 * @package    Ak_Locator
 * @author     Andrew Kett
 */
class Ak_Locator_Model_Search_Handler_Point_Latlong extends Ak_Locator_Model_Search_Handler_Point_Abstract
{
    const TYPE = 'latlong';

    /**
     * Find locations near a Lat/Long Point
     *
     * @param Array $params Array of search params
     *
     * @return Ak_Locator_Model_Resource_Location_Collection
     * @throws Exception
     */
    public function search(Array $params)
    {
        if (!$this->isValidParams($params)) {
            throw new Exception('Both latitude and longitude values are required to do a lat/long search');
        }

        $point = new Point($params['long'], $params['lat']);

        $collection = $this->pointToLocations($point, @$params['distance']);
        $collection->setSearch($this);

        return $collection;
    }


    /**
     * Validate params
     *
     * @param array $params
     * @return bool
     */
    public function isValidParams(array $params)
    {
        if (isset($params['lat']) && $params['lat'] != '' && isset($params['long']) && $params['long'] != '') {
            return true;
        }

        return false;
    }
}
