<?php
/**
 * Location attributes grid
 *
 * @category   Ak
 * @package    Ak_Location 
 */
class Ak_Locator_Block_Adminhtml_Location_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Initialize grid, set grid Id
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('locationsAttributeGrid');
        $this->setDefaultSort('sort_order');
    }
    /**
     * Prepare product attributes grid collection object
     *
     * @return Ak_Locator_Block_Adminhtml_Location_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('ak_locator/attribute_collection')
            ->addSystemHiddenFilter()
            ->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return Ak_Locator_Block_Adminhtml_Location_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', array(
            'header'=>Mage::helper('ak_locator')->__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('ak_locator')->__('Yes'),
                '0' => Mage::helper('ak_locator')->__('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('ak_locator')->__('Sort Order'),
            'sortable'  => true,
            'align'     => 'center',
            'index'     => 'sort_order'
        ));
        
        return $this;
    }
}
