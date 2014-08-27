<?php

/**
 * Location attribute edit page
 *
 * @category   Ak
 * @package    Ak_Locator
 */

class Ak_Locator_Block_Adminhtml_Location_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'ak_locator';
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_location_attribute';

        parent::__construct();
        
            $this->_addButton(
                'save_and_edit_button',
                array(
                    'label'     => Mage::helper('ak_locator')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit()',
                    'class'     => 'save'
                ),
                100
            );
       

        $this->_updateButton('save', 'label', Mage::helper('ak_locator')->__('Save Attribute'));
        $this->_updateButton('save', 'onclick', 'editForm.submit()');

        if (! Mage::registry('entity_attribute')->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('ak_locator')->__('Delete Attribute'));
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('entity_attribute')->getId()) {
            $frontendLabel = Mage::registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return Mage::helper('ak_locator')->__('Edit Location Attribute "%s"', $this->escapeHtml($frontendLabel));
        } else {
            return Mage::helper('ak_locator')->__('New Location Attribute');
        }
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
}
