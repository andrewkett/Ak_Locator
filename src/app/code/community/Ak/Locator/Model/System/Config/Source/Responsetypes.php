<?php

class Ak_Locator_Model_System_Config_Source_Responsetypes
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'ROOFTOP', 'label'=>Mage::helper('adminhtml')->__('ROOFTOP')),
            array('value' => 'RANGE_INTERPOLATED', 'label'=>Mage::helper('adminhtml')->__('RANGE_INTERPOLATED')),
            array('value' => 'GEOMETRIC_CENTER', 'label'=>Mage::helper('adminhtml')->__('GEOMETRIC_CENTER')),
            array('value' => 'APPROXIMATE', 'label'=>Mage::helper('adminhtml')->__('APPROXIMATE')),
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
            'ROOFTOP' => Mage::helper('adminhtml')->__('ROOFTOP'),
            'RANGE_INTERPOLATED' => Mage::helper('adminhtml')->__('RANGE_INTERPOLATED'),
            'GEOMETRIC_CENTER' => Mage::helper('adminhtml')->__('GEOMETRIC_CENTER'),
            'APPROXIMATE' => Mage::helper('adminhtml')->__('APPROXIMATE'),
        );
    }

}
