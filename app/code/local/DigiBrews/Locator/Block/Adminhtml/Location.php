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
 * @copyright   Copyright (c) 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class DigiBrews_Locator_Block_Adminhtml_Location extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_location';
        $this->_headerText = Mage::helper('digibrews_locator')->__('Manage Locations');
        $this->_blockGroup = 'digibrews_locator';
        $this->_addButtonLabel = Mage::helper('digibrews_locator')->__('Add New Location');

        parent::__construct();
    }
}
