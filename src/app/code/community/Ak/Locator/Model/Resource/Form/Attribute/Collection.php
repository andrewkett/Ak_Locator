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

class Ak_Locator_Model_Resource_Form_Attribute_Collection extends Mage_Eav_Model_Resource_Form_Attribute_Collection
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = 'ak_locator';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'ak_locator_location';

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('eav/attribute', 'ak_locator/form_attribute');
    }

    /**
     * Get EAV website table
     *
     * Get table, where website-dependent attribute parameters are stored.
     * If realization doesn't demand this functionality, let this function just return null
     *
     * @return string|null
     */
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('ak_locator/eav_attribute_website');
    }
}
