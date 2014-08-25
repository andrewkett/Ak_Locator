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

class Ak_Locator_Model_Resource_Location extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');

        $this->setType(Ak_Locator_Model_Location::ENTITY);

        $this->setConnection(
            $resource->getConnection('ak_locator_read'),
            $resource->getConnection('ak_locator_write')
        );
    }
    
    
    protected function _beforeSave(Varien_Object $location)
    {
        parent::_beforeSave($location);

        if (!$location->getLocationKey()) {
            throw Mage::throwException(Mage::helper('ak_locator')->__('Location Key is required'));
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = array('location_key' => $location->getLocationKey());

        $select = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('location_key = :location_key');
        
        if ($location->getId()) {
            $bind['entity_id'] = (int)$location->getId();
            $select->where('entity_id != :entity_id');
        }

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            throw Mage::throwException(
                Mage::helper('ak_locator')->__('This Location Key already exists')               
            );
        }
      
        return $this;
    }
}
