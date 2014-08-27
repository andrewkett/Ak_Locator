<?php

class Ak_Locator_Block_Adminhtml_Location_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{

    protected function getTabStructure()
    {
        $used = array();
        $tabStructure = $this->getData('tab_structure');
        foreach ($tabStructure as $tab) {
            foreach ($tab as $att) {
                $used[$att] = $att;
            }
        }            
        foreach ($this->getLocationForm()->getAttributes() as $att) {            
            if (!isset($used[$att->getAttributeCode()])) {                
                $tabStructure['location_details'][] = $att->getAttributeCode();
            }
        }
        return $tabStructure;
    }


    protected function getLocationForm()
    {
        
        $formCode = 'location_edit';
        if (Mage::registry('location_isnew')) {
            $formCode = 'location_create';
        }
        
        $locationForm = Mage::getModel('ak_locator/form');
        $locationForm->setEntity($this->getLocation())
            ->setFormCode($formCode)
            ->initDefaultValues();

        return $locationForm;
    }


    public function getLocation()
    {
        if (!isset($this->location)) {
            if (Mage::registry('location_data')) {
                $data = Mage::registry('location_data')->getData();
            } else {
                $data = array();
            }

            $location = Mage::getModel('ak_locator/location');

            if (isset($data['entity_id'])) {
                $location->load($data['entity_id']);
            }


        } else {
            $location = $this->location;
        }

        return $location;
    }

    public function initForm()
    {
        $structure = $this->getTabStructure();

        $addressAttributeCodes = @$structure[$this->tabAttrs];


        //get location form
        $locationForm = $this->getLocationForm();

        $form = new Varien_Data_Form();

        $addressAttributes = array();

        $attributes = $locationForm->getAttributes();
        foreach ($attributes as &$attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute->setFrontendLabel(Mage::helper('ak_locator')->__($attribute->getFrontend()->getLabel()));
            $attribute->unsIsVisible();
        }
        if ($addressAttributeCodes) {
            foreach ($addressAttributeCodes as $attributeCode) {
                if (isset($attributes[$attributeCode])) {
                    $addressAttributes[$attributeCode] = $attributes[$attributeCode];
                }
                    
            }
        }

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('ak_locator')->__($this->tabLabel)
        ));

        $this->_setFieldset($addressAttributes, $fieldset, array(@$disableAutoGroupChangeAttributeName));
        $form->setValues($this->getLocation()->getData());
        $this->setForm($form);
        return $this;
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('ak_locator/adminhtml_location_helper_image'),
            'boolean' => Mage::getConfig()->getBlockClassName('ak_locator/adminhtml_location_helper_boolean'),
            'file' => Mage::getConfig()->getBlockClassName('ak_locator/adminhtml_location_helper_file')
        );
    }
}
