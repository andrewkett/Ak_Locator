<?php

class MageBrews_Locator_Block_Adminhtml_Location_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{

    protected function getTabStructure(){
        $used = array();
        $tabStructure = $this->getData('tab_structure');
        foreach($tabStructure as $tab){
            foreach($tab as $att){
                $used[$att] = $att;
            }
        }

        foreach($this->getLocationForm()->getAttributes() as $att){
            if(!isset($used[$att->getAttributeCode()])){
                $tabStructure['location_details'][] = $att->getAttributeCode();
            }
        }
        return $tabStructure;
    }



    protected function getLocationForm()
    {
        $locationForm = Mage::getModel('magebrews_locator/form');
        $locationForm->setEntity($this->getLocation())
             ->setFormCode('location_create')
             ->initDefaultValues();

        return $locationForm;
    }


    public function getLocation()
    {
        if(!isset($this->location)){
            if (Mage::registry('location_data'))
            {
                $data = Mage::registry('location_data')->getData();
            }
            else
            {
                $data = array();
            }

            $location = Mage::getModel('magebrews_locator/location');

            if(isset($data['entity_id'])){
                $location->load($data['entity_id']);
            }
            

        }else{
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
     
        if($addressAttributeCodes){
            foreach($addressAttributeCodes as $attributeCode){
                if(isset($attributes[$attributeCode])){
                    $addressAttributes[$attributeCode] = $attributes[$attributeCode];
                }
                
            }
        }

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('magebrews_locator')->__($this->tabLabel)
        ));

        $this->_setFieldset($addressAttributes, $fieldset, array(@$disableAutoGroupChangeAttributeName));
        $form->setValues($this->getLocation()->getData());
        $this->setForm($form);
        return $this;
    }

}
