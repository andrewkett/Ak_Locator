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

class DigiBrews_Locator_Helper_Location extends Mage_Core_Helper_Abstract
{
    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'digibrews_locator_location';
    }

    /**
     * Return available location attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return array(
            array(
                'label' => Mage::helper('digibrews_locator')->__('Create Location'),
                'value' => 'location_create'
            ),
            array(
                'label' => Mage::helper('digibrews_locator')->__('Edit Location'),
                'value' => 'location_edit'
            ),
        );
    }
}
