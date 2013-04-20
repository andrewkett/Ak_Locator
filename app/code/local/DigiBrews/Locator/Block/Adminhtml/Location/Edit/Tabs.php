<?php

class Digibrews_Locator_Block_Adminhtml_Location_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    //protected $_attributeTabBlock = 'bi_events/adminhtml_event_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('location_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('digibrews_locator')->__('Location Information'));
    }

    protected function _prepareLayout()
    {

        $this->addTab('location_details', array(
            'label'     => Mage::helper('digibrews_locator')->__('Location Details'),
            'content'   =>  $this->getLayout()->createBlock('digibrews_locator/adminhtml_location_edit_tab_details')->initForm()->toHtml(),
        ));

        $this->addTab('location_address', array(
            'label'     => Mage::helper('digibrews_locator')->__('Location Address'),
            'content'   =>  $this->getLayout()->createBlock('digibrews_locator/adminhtml_location_edit_tab_address')->initForm()->toHtml(),
        ));

        return parent::_prepareLayout();
    }

    // /**
    //  * Getting attribute block name for tabs
    //  *
    //  * @return string
    //  */
    // public function getAttributeTabBlock()
    // {     
    //     return $this->_attributeTabBlock;        
    // }

    // public function setAttributeTabBlock($attributeTabBlock)
    // {
    //     $this->_attributeTabBlock = $attributeTabBlock;
    //     return $this;
    // }

    // /**
    //  * Translate html content
    //  *
    //  * @param string $html
    //  * @return string
    //  */
    // protected function _translateHtml($html)
    // {
    //     Mage::getSingleton('core/translate_inline')->processResponseBody($html);
    //     return $html;
    // }
}
