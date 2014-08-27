<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

class Ak_Locator_Block_Adminhtml_Location_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('location_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ak_locator/location')->getCollection()
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('geocoded')
            ->addAttributeToSelect('address')
            ->addAttributeToSelect('postal_code')
            ->addAttributeToSelect('country')
            ->addAttributeToSelect('is_enabled');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('ak_locator')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('ak_locator')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));

        $this->addColumn('address', array(
            'header'    => Mage::helper('ak_locator')->__('Address'),
            'align'     =>'left',
            'index'     => 'address',
        ));

        $this->addColumn('postal_code', array(
            'header'    => Mage::helper('ak_locator')->__('Postalcode'),
            'align'     => 'left',
            'index'     => 'postal_code',
        ));

        $this->addColumn('country', array(
            'header'    => Mage::helper('ak_locator')->__('Country'),
            'align'     => 'left',
            'index'     => 'country',
        ));

        $this->addColumn('is_enabled', array(
            'header'    => Mage::helper('ak_locator')->__('Enabled'),
            'align'     => 'left',
            'index'     => 'is_enabled',
            'type'  => 'options',
            'options' => array('1'=>'Yes','0'=>'No'),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
