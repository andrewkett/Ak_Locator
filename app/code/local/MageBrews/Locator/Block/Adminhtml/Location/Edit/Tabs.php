<?php

class MageBrews_Locator_Block_Adminhtml_Location_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('location_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('magebrews_locator')->__('Location Information'));

        $this->setTabStructure(array(
            'location_address' => array(
                'address',
                'administrative_area',
                'sub_administrative_area',
                'locality',
                'dependent_locality',
                'postal_code',
                'thoroughfare',
                'premise',
                'sub_premise',
                'country',
                'data',
                'geocoded',
                'latitude',
                'longitude'
            ),
            'location_details' => array(
                'title',
                'is_enabled',
                'url_key'
            )
        ));
    }

    protected function _prepareLayout()
    {

        $this->addTab('location_details', array(
            'label'     => Mage::helper('magebrews_locator')->__('Location Details'),
            'content'   =>  $this->getLayout()
                            ->createBlock('magebrews_locator/adminhtml_location_edit_tab_details')
                            ->setTabStructure($this->getTabStructure())
                            ->initForm()
                            ->toHtml()
        ));

        $this->addTab('location_address', array(
            'label'     => Mage::helper('magebrews_locator')->__('Location Address'),
            'content'   =>  $this->getLayout()
                            ->createBlock('magebrews_locator/adminhtml_location_edit_tab_address')
                            ->setTabStructure($this->getTabStructure())
                            ->initForm()
                            ->toHtml()
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
