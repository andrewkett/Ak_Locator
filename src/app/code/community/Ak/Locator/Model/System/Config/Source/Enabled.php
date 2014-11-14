<?php

class Ak_Locator_Model_System_Config_Source_Enabled
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Yes')),
           // array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Only on non-geocoded')),

        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('Never'),
            1 => Mage::helper('adminhtml')->__('Always'),
            //2 => Mage::helper('adminhtml')->__('Only on non-geocoded'),
        );
    }

}
