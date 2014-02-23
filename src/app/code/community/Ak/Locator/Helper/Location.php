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

class Ak_Locator_Helper_Location extends Mage_Core_Helper_Abstract
{

    const XML_PATH_LOCATION_URL_SUFFIX  = 'locator_settings/seo/location_url_suffix';

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'ak_locator_location';
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
                'label' => Mage::helper('ak_locator')->__('Create Location'),
                'value' => 'location_create'
            ),
            array(
                'label' => Mage::helper('ak_locator')->__('Edit Location'),
                'value' => 'location_edit'
            ),
        );
    }


    /**
     * Retrieve location rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getLocationUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_locationUrlSuffix[$storeId])) {
            $this->_locationUrlSuffix[$storeId] = Mage::getStoreConfig(self::XML_PATH_LOCATION_URL_SUFFIX, $storeId);
        }
        return $this->_locationUrlSuffix[$storeId];
    }
}
