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
    /**
     * @var $_point The point that this collections results are based from
     */
    protected $_point;



    protected function _construct()
    {
        $this->_init('magebrews_locator/location');
    }

    /**
     * Override parent method to remove entity_type_id filter
     *
     * @return $this
     */
    protected function _initSelect()
    {
      $this->getSelect()->from(array('e' => $this->getEntity()->getEntityTable()));

      return $this;
    }

    /**
     * Use Haversine formula to find locations within a given radius of a point
     * http://en.wikipedia.org/wiki/Haversine_formula
     *
     * @param Point $point
     * @param int $radius
     * @return $this
     */
    public function nearPoint(Point $point, $radius = 0)
    {
        $this->addExpressionAttributeToSelect('distance', sprintf("(3959 * acos(cos(radians('%s')) * cos(radians(latitude)) * cos(radians(longitude) - radians('%s')) + sin(radians('%s')) * sin( radians(latitude))))", $point->coords[1], $point->coords[0], $point->coords[1], $radius), array('entity_id'));

        if ($radius !== 0) {
            $this->getSelect()->having('distance < ?', $radius);
        }

        $this->setSearchPoint($point);

        return $this;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return mixed
     */
    public function toOptionArray($valueField='entity_id', $labelField='title', $additional=array())
    {
        return $this->_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * Output this collection as a json object to be used by search frontend
     *
     * @return mixed
     */
    public function toJson()
    {
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

        $obj = new Varien_Object();
        $obj->setLocations($locations);

        //dispatch event to allow other modules to add to the json
        Mage::dispatchEvent('magebrews_locator_before_search_json_output', array('collection'=>$this, 'json_data'=>$obj));

        $json = Zend_Json::encode($obj->getLocations());
        //zend_json doesn't encode single quotes but they break in the browser
        $json = str_replace('\'', '&#39;', $json);
        return $json;
    }

    /**
     * @param Point $point
     */
    public function setSearchPoint(Point $point)
    {
        $this->_point = $point;
    }

    /**
     * @return Point
     */
    public function getSearchPoint()
    {
        return $this->_point;
    }

    public function getResponseObject()
    {
        $obj = new Varien_Object();
        $point = $this->getSearchPoint();


        $obj->setLocations($this->toJson());

        if (!is_null($point)) {
            $obj->setSearchPoint($point);
        }

        return $obj;
    }
}
