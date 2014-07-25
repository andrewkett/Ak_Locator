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
class Ak_Locator_Model_Search_Handler_Area extends Ak_Locator_Model_Search_Handler_Abstract
{
    const TYPE = 'area';

    /**
     * Find locations based on an area attribute
     *
     * @param array $params Array of search params
     *
     * @return Ak_Locator_Model_Resource_Location_Collection
     * @throws Exception
     */
    public function search(array $params)
    {
        if (!$this->isValidParams($params)) {
            throw new Exception('At least one valid search parameter must be passed');
        }
     
        $collection = $this->areaSearch($params);
        $collection->setSearch($this);

        return $collection;
    }

    /**
     * Make any manipulations to parameters required by this handler
     *
     * @param array $params
     * @return array
     */
    public function parseParams(array $params)
    {
        if (isset($params['a']) && !isset($params['administrative_area'])) {
            $params['administrative_area'] = $params['a'];
        }

        if (isset($params['c']) && !isset($params['country'])) {
            $params['country'] = $params['c'];
        }

        return $params;
    }


    /**
     * Validate params
     *
     * @param array $params
     * @return bool
     */
    public function isValidParams(array $params)
    {
        if ((isset($params['a']) && $params['a'] != '')
            || (isset($params['c']) && $params['c'] != '')
            || (isset($params['country']) && $params['country'] != '')
            || (isset($params['administrative_area']) && $params['administrative_area'] != '')
            || (isset($params['postcode']) && $params['postcode'] != '')
        ) {
            return true;
        }

        return false;
    }


    /**
     * Find locations based on area attributes
     *
     * @param string name of the administrative area
     * @return Ak_Locator_Model_Resource_Location_Collection
     */
    public function areaSearch($params)
    {
        $collection = $this->getCollection();

        if (isset($params['country']) && $params['country'] != '') {
            $collection->addAttributeToFilter('country', $params['country']);
        }

        if (isset($params['administrative_area']) && $params['administrative_area'] != '') {
            $collection->addAttributeToFilter('administrative_area', $params['administrative_area']);
        }

        if (isset($params['postcode']) && $params['postcode'] != '') {
            $collection->addAttributeToFilter('postcode', $params['postcode']);
        }

        //area searches cant be sorted by proximity so sort them by title
        $collection->setOrder('title');
        
        $collection->addAttributeToSelect('*');
        return $collection;
    }
}
