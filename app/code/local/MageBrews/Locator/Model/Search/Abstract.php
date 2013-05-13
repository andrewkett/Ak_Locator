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
abstract class MageBrews_Locator_Model_Search_Abstract extends Mage_Core_Model_Abstract
{

    protected abstract function search(Array $params);


    protected function getCache()
    {
        if(!$this->_cache){
            $this->_cache = Mage::getSingleton('core/cache');
        }

        return $this->_cache;

    }

    /**
     * Get the location collection used to search
     *
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    protected function getSearchCollection()
    {
        return Mage::getModel('magebrews_locator/location')->getCollection()->addAttributeToFilter('is_enabled','1');
    }

}
