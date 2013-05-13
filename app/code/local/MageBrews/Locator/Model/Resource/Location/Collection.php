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

class MageBrews_Locator_Model_Resource_Location_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('magebrews_locator/location');
    }

    protected function _initSelect()
    {
      $this->getSelect()->from(array('e' => $this->getEntity()->getEntityTable()));

      if ($this->getEntity()->getTypeId()) {
          /**
           * We override the Mage_Eav_Model_Entity_Collection_Abstract->_initSelect()
           * because we want to remove the call to addAttributeToFilter for 'entity_type_id'
           * as it is causing invalid SQL select, thus making the User model load failing.
           */
          //$this->addAttributeToFilter('entity_type_id', $this->getEntity()->getTypeId());
      }
      return $this;
    }

    public function getJsonData()
    {
        foreach($this->getItems() as $location) 
        {
          $loc = new StdClass();
          $loc->id = $location->getId();
          $loc->title = $location->getTitle();
          $loc->latitude = $location->getLatitude();
          $loc->longitude = $location->getLongitude();
          $loc->distance = round($location->getDistance(),2);

          $locations[$location->getEntityId()] = $loc;
        }
    }


    public function toJson(){

        $locations = array();

        foreach($this->getItems() as $location) 
        {
          $loc = array();
          $loc['id'] = $location->getId();
          $loc['title'] = $location->getTitle();
          $loc['latitude'] = $location->getLatitude();
          $loc['longitude'] = $location->getLongitude();
          $loc['distance'] = (string)round($location->getDistance(),2);
          $loc['directions'] = $location->getDirectionsLink();

          $locations[(int)$location->getEntityId()] = $loc;
        }
        return Zend_Json::encode($locations);
    }

    public function toOptionArray($valueField='entity_id', $labelField='title', $additional=array())
    {
        return $this->_toOptionArray($valueField, $labelField, $additional);
    }


    public function nearPoint(Point $point, $radius = 0){

        $this->addExpressionAttributeToSelect('distance', sprintf("(3959 * acos(cos(radians('%s')) * cos(radians(latitude)) * cos(radians(longitude) - radians('%s')) + sin(radians('%s')) * sin( radians(latitude))))", $point->coords[1], $point->coords[0], $point->coords[1], $radius), array('entity_id'));

        if ($radius !== 0) {
            $this->getSelect()->having('distance < ?', $radius);
        }

        return $this;

    }
}
