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

class DigiBrews_Locator_Helper_Data extends Enterprise_Eav_Helper_Data
{
    /**
     * Return available location attribute form as select options
     *
     * @throws Mage_Core_Exception
     */
    public function getAttributeFormOptions()
    {
        Mage::throwException(Mage::helper('digibrews_locator')->__('Use helper with defined EAV entity'));
    }

    /**
     * Default attribute entity type code
     *
     * @throws Mage_Core_Exception
     */
    protected function _getEntityTypeCode()
    {
        Mage::throwException(Mage::helper('digibrews_locator')->__('Use helper with defined EAV entity'));
    }

    /**
     * Return available location attribute form as select options
     *
     * @return array
     */
    public function getLocationAttributeFormOptions()
    {
        return Mage::helper('digibrews_locator/location')->getAttributeFormOptions();
    }

    /**
     * Returns array of user defined attribute codes for location entity type
     *
     * @return array
     */
    public function getLocationUserDefinedAttributeCodes()
    {
        return Mage::helper('digibrews_locator/location')->getUserDefinedAttributeCodes();
    }



        /**
     * Return data array of available attribute Input Types
     *
     * @param string|null $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = parent::getAttributeInputTypes($inputType);

        // -- Multiline currently showing on form but not saving
        unset($inputTypes['multiline']);

        // -- Boolean causes whole page to error
        unset($inputTypes['boolean']);

        // file and input both not saving
        unset($inputTypes['file']);
        unset($inputTypes['image']);

        return $inputTypes;
    }

}
