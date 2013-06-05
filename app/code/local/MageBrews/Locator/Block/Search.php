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

class MageBrews_Locator_Block_Search extends Mage_Core_Block_Template
{

    protected $_searchModel;

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $layout = $this->getLayout();
        
        $this->getLayout()->createBlock('magebrews_locator/breadcrumbs');

        if ($headBlock = $layout->getBlock('head')) {
            $headBlock->setTitle('Search Results');
            $headBlock->getChild('init-search')->setData('locations', $this->getLocations());
        }

        // @todo init these in layout xml so they can be modified easier
        $listBlock = $this->getListBlock()->setData('locations', $this->getLocations());
        $this->setChild('list', $listBlock);

        return parent::_prepareLayout();
    }


    /**
     * Retrieve location collection based on search parameters
     *
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function getLocations()
    {
        if (!Mage::registry('locator_locations')) {
            $locations = $this->getSearch()->search($this->getRequest()->getParams());
            Mage::register('locator_locations', $locations);
        }else{
            $locations = Mage::registry('locator_locations');
        }

        return $locations;
    }

    /**
     *
     * @return MageBrews_Locator_Model_Location
     */
//    public function getLocation()
//    {
//      $locations = $this->getLocations()->getFirstItem();
//    }


    /**
     * @return string
     */
    public function asJson()
    {
        $obj = new Varien_Object();
        $obj->setLocations($this->getLocations()->toJson());
        $obj->setOutput($this->getListBlock()->setData('locations', $this->getLocations())->toHtml());
        return $obj->toJson();
    }


    public function setSearch($model)
    {
        $this->_searchModel = $model;
    }

    public function getSearch()
    {
        if($this->_searchModel === null){
            $this->setSearch(Mage::getModel('magebrews_locator/search'));
        }
        return $this->_searchModel;
    }

    /**
     * get the child block which will render the list of locations
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function getListBlock()
    {
        //if the collection contains distance data render accordingly otherwise just list normally
        if(!$this->getLocations()->getFirstItem()->getDistance()){
            return $this->getLayout()->createBlock('magebrews_locator/search_list_area');
        }else{
            return $this->getLayout()->createBlock('magebrews_locator/search_list_point');
        }
    }

}
