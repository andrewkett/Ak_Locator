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

class DigiBrews_Locator_Block_Search_Search extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {   
        $this->setTemplate('locator/search/search.phtml');

        $layout = $this->getLayout();
        //$this->loadLayout();

        if ($headBlock = $layout->getBlock('head')) {

          $headBlock->setTitle('Search Results');
          // $headBlock->setDescription('Locations near ');
          // $headBlock->setKeywords('');
        }

        $initLocator = $layout->createBlock('core/template');
        $initLocator->setTemplate('locator/page/html/head/init-locator.phtml');

        $initSearch = $layout->createBlock('core/template');
        $initSearch->setTemplate('locator/page/html/head/init-search.phtml')->setData('locations', $this->getLocations());

        $headBlock->append($initLocator);
        $headBlock->append($initSearch);

        // @todo init these in layout xml so they can be modified easier
        $formBlock = $this->getLayout()->createBlock('digibrews_locator/search_form');
        $listBlock = $this->getListBlock()->setData('locations', $this->getLocations());

        $this->setChild('form', $formBlock);
        $this->setChild('list', $listBlock);

        return parent::_prepareLayout();
    }


    /**
     * Retrieve location collection based on search parameters
     *
     * @return DigiBrews_Locator_Model_Resource_Location_Collection
     */
    public function getLocations()
    {
        if (!Mage::registry('locator_locations')) {
            $results = $this->getSearchClass()
                            ->search($this->getRequest()->getParams());
            Mage::register('locator_locations', $results);
        }
        return Mage::registry('locator_locations');
    }

    public function getLocation()
    {
      $locations = $this->getLocations()->getItems();
      $location = reset($locations);
    }

    public function asJson()
    {
        $obj = new Varien_Object();
        $obj->setLocations($this->getLocations()->toJson());
        $obj->setOutput($this->getListBlock()->setData('locations', $this->getLocations())->toHtml());
        return $obj->toJson();
    }

    /**
     * Find appropriate search class based on params passed 
     * 
     * @return DigiBrews_Locator_Model_Resource_Location_Collection
     */
    protected function getSearchClass()
    {
        $params = $this->getRequest()->getParams();
        
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

    // get the child block which will render the list of locations
    protected function getListBlock()
    {
        if($this->getSearchClass() instanceof DigiBrews_Locator_Model_Search_Area){
            return $this->getLayout()->createBlock('digibrews_locator/search_list_area');
        }else{
            return $this->getLayout()->createBlock('digibrews_locator/search_list_point');
        }
    }

}
