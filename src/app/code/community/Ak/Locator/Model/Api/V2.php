<?php

class Ak_Locator_Model_Api_V2 extends Ak_Locator_Model_Api
{

    /**
     * @param int|string $locationId
     * @return array
     * @throws Exception
     */
    public function retrieve($locationId)
    {

        try {
            if (is_null($locationId)) {
                throw new Exception(Mage::helper('ak_locator/location')->__("Data cannot be null"));
            }
            $result = parent::retrieve($locationId);

            $result = Mage::helper('api')->wsiArrayPacker($result);
            // Mage::log($result);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('transaction_error', $e->getMessage());
        }
        return $result;
    }


    /**
     * @param $dataArray
     * @return array|mixed
     * @throws Exception
     */
    public function add($dataArray)
    {
        try {
            if (is_null($dataArray)) {
                throw new Exception(Mage::helper('ak_locator/location')->__("Data cannot be null"));
            }
            $model = Mage::getModel('ak_locator/location');
            $model->setData((array) $dataArray);
            $model->setIsEnabled(1);
            $errors = $model->validate();
            
            if (is_array($errors)) {
                $strErrors = array();
                foreach ($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                    }
                    $strErrors[] = $error;
                }
                throw new Exception(Mage::helper('ak_locator/location')->__(implode("\n", $strErrors)));
               // $this->_fault('transaction_error', implode("\n", $strErrors));
            } else {
                $model->save();
                return $model->getData();
            }
                       
            
        } catch (Mage_Core_Exception $e) {
            $this->_fault('transaction_error', $e->getMessage());
        }
      
    }


    /**
     * @param int $locationId
     * @param $dataArray
     * @return bool
     * @throws Exception
     */
    public function update($locationId, $dataArray)
    {
        try {

            if (is_null($dataArray) && is_null($locationId)) {
                throw new Exception(Mage::helper('ak_locator/location')->__("Data cannot be null"));
            }

            $model = Mage::getModel('ak_locator/location');
            $updateDate = $model->load($locationId);
            $existingdata = $updateDate->getData();

            if ($updateDate->getId()) {
                $model->setData((array) $dataArray);
                $model->setIsEnabled($existingdata['is_enabled']);
                $model->setId($locationId);
                $errors = $model->validate();
                 
                if (is_array($errors)) {
                     
                    $strErrors = array();
                    foreach ($errors as $code => $error) {
                        if ($error === true) {
                            $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                        }
                        $strErrors[] = $error;
                    }
                    throw new Exception(Mage::helper('ak_locator/location')->__(implode("\n", $strErrors)));
                   // $this->_fault('transaction_error', implode("\n", $strErrors));
                } else {
                    $model->save();
                    return true;
                }
                                
            } else {
                $this->_fault('Location is not found.');
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('transaction_error', $e->getMessage());
        }
    }

}
