<?php
class Ak_Locator_Block_Adminhtml_Location_Helper_Boolean extends Varien_Data_Form_Element_Select
{
    /**
     * Prepare default SELECT values
     *
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setValues(array(
            array(
                'label' => Mage::helper('adminhtml')->__('No'),
                'value' => '0',
            ),
            array(
                'label' => Mage::helper('adminhtml')->__('Yes'),
                'value' => 1,
            )
        ));
    }
}
