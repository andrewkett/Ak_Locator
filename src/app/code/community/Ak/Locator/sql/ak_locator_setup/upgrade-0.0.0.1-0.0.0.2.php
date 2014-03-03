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

/* @var $installer ak_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

//remove old is_active attribute
$installer->removeAttribute(Ak_Locator_Model_Location::ENTITY, 'is_active');

//add new enabled attribute
$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'is_enabled', array(
    'input'             => 'select',
    'type'              => 'int',
    'label'             => 'Enabled',
    'backend_label'     => 'Enabled',
    'source'      => 'eav/entity_attribute_source_boolean',
    'user_defined'      => false,
    'visible'           => 1,
    'required'          => 1,
    'position'          => 11,
    'default'           =>1,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


//add new attribute to location edit form
$eavConfig = Mage::getSingleton('eav/config');

$formAttributes = array(
    'is_enabled'
);

foreach ($formAttributes as $code) {
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();
