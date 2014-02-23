<?php

class Ak_Locator_Block_Search_Infowindows extends Mage_Core_Block_Template
{
    protected $_location;


    public function getLocations(){

        if(!isset($this->_location)){
            $ids = explode(',', Mage::app()->getRequest()->getParam('ids'));
            $this->setLocations(Mage::getModel('ak_locator/location')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('entity_id', array('in' => $ids)));
        }

        return $this->_locations;
    }

    public function setLocations($locations){
        $this->_locations = $locations;
    }

    public function _toHtml(){

        $locations = array();
        foreach($this->getLocations() as $location){
            $block = $this->getChild('infowindow');
            $locations[$location->getId()] = $block->setLocation($location)->toHtml();
        }

        $json = Zend_Json::encode($locations);
        //zend_json doesn't encode single quotes but they break in the browser
        $json = str_replace('\'', '&#39;', $json);

        return $json;
    }

}