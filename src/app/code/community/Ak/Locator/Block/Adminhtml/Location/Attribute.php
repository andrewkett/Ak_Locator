<?php
/**
 * Adminhtml catalog product attributes block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Ak_Locator_Block_Adminhtml_Location_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'ak_locator';
        $this->_controller = 'adminhtml_location_attribute';
        $this->_headerText = Mage::helper('ak_locator')->__('Manage Attributes');
        $this->_addButtonLabel = Mage::helper('ak_locator')->__('Add New Attribute');
        parent::__construct();
    }

}
