<?php

/**
 * Location attribute edit page tabs
 *
 * @category   Ak
 * @package    Ak_Locator
 */
class Ak_Locator_Block_Adminhtml_Location_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('location_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ak_locator')->__('Attribute Information'));
    }    
}
