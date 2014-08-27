<?php

/**
 * Location attribute add/edit form block
 *
 * @category   Ak
 * @package    Ak_Locator 
 */

class Ak_Locator_Block_Adminhtml_Location_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
