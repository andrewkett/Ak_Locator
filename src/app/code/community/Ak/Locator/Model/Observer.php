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


class Ak_Locator_Model_Observer
{
    /**
     * Set admin messages if extension prerequisites haven't been meet
     */
    public function setAdminMessage()
    {
        if ('' == Mage::getStoreConfig('locator_settings/google_maps/api_key')) {
            Mage::getSingleton('core/session')->addError(
                'Warning: You haven\'t yet set a Google API key in locator configuration,
                the locator module requires the Google Maps API to function.
                You can sign up for a key <a href="https://code.google.com/apis/console/" target="_blank">here</a>.'
            );
    
        }

        if (!@class_exists('geoPHP')) {
            Mage::getSingleton('core/session')->addError(
                'Warning: The geoPHP library could not be detected,
                 this is a requirement of the Locator extension.
                 Please <a href=" https://github.com/downloads/phayes/geoPHP/geoPHP.tar.gz">download</a> the library and add it to the lib directory'
            );
        }
    }

    /**
     * Attempt to load geoPHP from lib and vendor directories
     */
    public function loadLibraries()
    {
        $files = array(
            Mage::getBaseDir('lib').'/geoPHP/geoPHP.inc',
            Mage::getBaseDir().'/vendor/phayes/geophp/geoPHP.inc'
        );
        //try to find the geoPHP library
        foreach ($files as $file) {
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
    
    //attrbutes events
    /**
     * Before save observer for location attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Ak_Locator_Model_Observer
     */
    public function locationAttributeBeforeSave(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute instanceof Ak_Locator_Model_Attribute && $attribute->isObjectNew()) {
            /**
             * Check for maximum attribute_code length
             */
            $attributeCodeMaxLength = Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9;
            $validate = Zend_Validate::is($attribute->getAttributeCode(), 'StringLength', array(
                'max' => $attributeCodeMaxLength
            ));
            if (!$validate) {
                throw Mage::exception(
                        'Mage_Eav',
                        Mage::helper('eav')->__('Maximum length of attribute code must be less then %s symbols', $attributeCodeMaxLength)
                );
            }
        }

        return $this;
    }
}