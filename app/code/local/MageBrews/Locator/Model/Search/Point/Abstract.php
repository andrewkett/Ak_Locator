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
abstract class MageBrews_Locator_Model_Search_Point_Abstract
    extends MageBrews_Locator_Model_Search_Abstract
{

    /**
     * Find locations near a Lat/Long point
     *
     * @param Point $point
     * @param int $radius
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     * @throws Exception
     */
    protected function pointToLocations(Point $point, $radius = null)
    {
        if (null==$radius) {
            $radius = (int)Mage::getStoreConfig('locator_settings/search/default_search_distance');
        }

        return  $this->getSearchCollection()
                ->addAttributeToSelect('*')
                ->nearPoint($point, $radius)
                ->setOrder('distance');
    }
}
