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

class MageBrews_Locator_Block_Location_View extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('locator/location/view.phtml');
    }

    /**
     * Prepare the layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $location = $this->getLocation();

        if(!$location){
            return;
        }

        $layout = $this->getLayout();

        if ($headBlock = $layout->getBlock('head')) {
          $headBlock->setTitle($location->getTitle());
        }

        $initLocator = $layout->createBlock('core/template');
        $initLocator->setTemplate('locator/page/html/head/init-locator.phtml');

        $initSearch = $layout->createBlock('core/template');
        $initSearch->setTemplate('locator/page/html/head/init-store.phtml')->setData('locations', $this->getLocations());

        $headBlock->append($initLocator);
        $headBlock->append($initSearch);

        $listBlock = $this->getLayout()->createBlock('magebrews_locator/location_info')->setData('locations', $this->getLocations());
        $mapBlock = $this->getLayout()->createBlock('magebrews_locator/location_map')->setData('locations', $this->getLocations());


        $nearbyBlock = $this->getLayout()->createBlock('magebrews_locator/search_list_point')->setData('locations', $this->getNearbyLocations())->setTemplate('locator/location/others.phtml');


        $this->setChild('others', $nearbyBlock);
        $this->setChild('info', $listBlock);
        $this->setChild('map', $mapBlock);

        return parent::_prepareLayout();
    }


    /**
     * Retrieve a location collection containing the current location
     *
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function getLocations()
    {
        if (!Mage::registry('locator_locations')) {

          $id = $this->getRequest()->getParam('id');

          $locations = Mage::getModel('magebrews_locator/location')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id',$id);

            Mage::register('locator_locations', $locations);
        }
        return Mage::registry('locator_locations');
    }


    /**
     * Get the location currently being viewed
     *
     * @return MageBrews_Locator_Model_Location
     */
    public function getLocation()
    {
      return $this->getLocations()->getFirstItem();
    }


    /**
     * Get locations near current location
     *
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function getNearbyLocations()
    {
        $location = $this->getLocation();
        $params = array(
            'lat'=>$location->getLatitude(),
            'long'=>$location->getLongitude(),
            'limit' => 8
        );
        
        $results = Mage::getModel('magebrews_locator/search')
                        ->search($params)
                        ->addAttributeToFilter('entity_id', array('neq'=>$location->getId()));

        return $results;
    }
}
