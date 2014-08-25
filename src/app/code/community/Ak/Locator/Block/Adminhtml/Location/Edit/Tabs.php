<?php

class Ak_Locator_Block_Adminhtml_Location_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('location_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ak_locator')->__('Location Information'));

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
                'location_key',
                'is_enabled'
            ),
            'location_seo' => array(
                'url_key',
                'meta_title',
                'meta_description',
                'meta_keywords'
            ),
        ));
    }

    protected function _prepareLayout()
    {

        $this->addTab('location_details', array(
            'label'     => Mage::helper('ak_locator')->__('Location Details'),
            'content'   =>  $this->getLayout()
                            ->createBlock('ak_locator/adminhtml_location_edit_tab_details')
                            ->setTabStructure($this->getTabStructure())
                            ->initForm()
                            ->toHtml()
        ));

        $this->addTab('location_address', array(
            'label'     => Mage::helper('ak_locator')->__('Location Address'),
            'content'   =>  $this->getLayout()
                            ->createBlock('ak_locator/adminhtml_location_edit_tab_address')
                            ->setTabStructure($this->getTabStructure())
                            ->initForm()
                            ->toHtml()
        ));

        $this->addTab('location_seo', array(
            'label'     => Mage::helper('ak_locator')->__('SEO'),
            'content'   =>  $this->getLayout()
                            ->createBlock('ak_locator/adminhtml_location_edit_tab_seo')
                            ->setTabStructure($this->getTabStructure())
                            ->initForm()
                            ->toHtml()
        ));

        return parent::_prepareLayout();
    }
}
