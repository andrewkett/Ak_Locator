<?php

class Ak_Locator_Block_Adminhtml_Location_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {

        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media').'locator/location/'. $this->getValue();
        }
        return $url;
    }
}
