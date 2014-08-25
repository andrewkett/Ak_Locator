<?php

class Ak_Locator_Model_Api extends Mage_Api_Model_Resource_Abstract {

    /**
     * @param int|string $locationId
     * @return array
     */
    public function retrieve($locationId) {

        if ($locationId != "") {
            $eventCollection = Mage::getModel('ak_locator/location')->getCollection()
                    ->addAttributeToFilter('entity_id', array('eq' => $locationId))
                    ->addAttributeToSelect('*');
            $locations = array();
            foreach ($eventCollection->getItems() as $location) {

                $locations = $this->_unsetUnwanted($location->getData());
            }
            if (!empty($locations)) {
                return $locations;
            } else {
                $this->_fault('location_not_found');
            }
        } else {
            $this->_fault('locationid_not_found');
        }
    }

    /**
     * @param array
     * @return array
     */
    public function add($dataArray) {

        if (isset($dataArray) && !empty($dataArray)) {
            $model = Mage::getModel('ak_locator/location');
            $model->setData($dataArray);
            $model->setIsEnabled(1);
            $errors = $model->validate();
            try {
                 if (is_array($errors)) {
                     
                    $strErrors = array();
                    foreach($errors as $code => $error) {
                        if ($error === true) {
                            $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                        }
                        $strErrors[] = $error;
                    }
                    throw new Exception(Mage::helper('ak_locator/location')->__(implode("\n", $strErrors)));
                   // $this->_fault('transaction_error', implode("\n", $strErrors));
                }else{
                    $model->save();
                }              
             
                
                
            } catch (Exception $ex) {
                $this->_fault('transaction_error', $ex->getMessage());
            }
            return $model->getData();
        } else {
            $this->_fault('dataset_empty');
        }
    }

    /**
     * @param int $locationId
     * @return boolean
     */
    public function remove($locationId) {

        if ($locationId != "") {
            try {
                $model = Mage::getModel('ak_locator/location');

                $updateDate = $model->load($locationId);
                if ($updateDate->getId()) {
                    $model->setId($locationId);
                    $model->delete();
                } else {
                    $this->_fault('Location is not found.');
                }
            } catch (Exception $ex) {
                $this->_fault('transaction_error', $ex->getMessage());
            }
            return true;
        } else {
            $this->_fault('locationid_not_found');
        }
    }

    /**
     * @param int $locationId
     * @param array
     * @return boolean
     */
    public function update($locationId, $dataArray) {

        if ($locationId != "" && isset($dataArray)) {
            try {
                $model = Mage::getModel('ak_locator/location');
                $updateDate = $model->load($locationId);
                
                if ($updateDate->getId()) {
                       $existingdata = $updateDate->getData();
                       Mage::getSingleton('adminhtml/session')->setFormData($dataArray);
                       $model->setData($dataArray);
                       $model->setIsEnabled($existingdata['is_enabled']);
                       $model->setId($locationId);
                       $errors = $model->validate();
                       if (is_array($errors)) {
                     
                            $strErrors = array();
                            foreach($errors as $code => $error) {
                                if ($error === true) {
                                    $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                                }
                                $strErrors[] = $error;
                            }
                            throw new Exception(Mage::helper('ak_locator/location')->__(implode("\n", $strErrors)));
                           // $this->_fault('transaction_error', implode("\n", $strErrors));
                        }else{
                            $model->save();
                        }   

                        
                } else {
                    $this->_fault('Location is not found.');
                }
            } catch (Exception $ex) {
                $this->_fault('transaction_error', $ex->getMessage());
            }
            return true;
        } else {
            $this->_fault('dataset_empty');
        }
    }

    /**
     * @return array
     */
    public function listall() {

        try {

            $eventCollection = Mage::getModel('ak_locator/location')->getCollection()
                    ->addAttributeToSelect('*');
            $locations = array();
            foreach ($eventCollection->getItems() as $location) {

                $locations[] = $this->_unsetUnwanted($location->getData());
            }
            if (empty($locations)) {
                $this->_fault('locations_not_found');
            } else {
                return $locations;
            }
        } catch (Exception $ex) {
            $this->_fault('transaction_error', $ex->getMessage());
        }
    }

    /**
     * @param array
     * @return array
     */
    protected function _unsetUnwanted($data) {

        unset($data['meta_description']);
        unset($data['is_enabled']);
        unset($data['meta_keywords']);
        unset($data['data']);
        unset($data['url_key']);
        unset($data['updated_at']);
        unset($data['created_at']);

        return $data;
    }

}
