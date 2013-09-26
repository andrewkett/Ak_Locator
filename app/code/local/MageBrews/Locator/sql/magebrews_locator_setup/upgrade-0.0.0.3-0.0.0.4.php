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
 * @link      http://andrewkett.github.io/MageBrews_Locator/
 */

/* @var $installer magebrews_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$installer->updateAttribute(MageBrews_Locator_Model_Location::ENTITY, 'latitude_bak', array('is_visible' => 0));
$installer->updateAttribute(MageBrews_Locator_Model_Location::ENTITY, 'longitude_bak', array('is_visible' => 0));

//add new enabled attribute
$installer->addAttribute(MageBrews_Locator_Model_Location::ENTITY, 'latitude', array(
    'input'             => 'text',
    'type'              => 'static',
    'label'             => 'Latitude',
    'user_defined'      => false,
    'visible'           => 1,
    'required'          => 1,
    'position'          => 30,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->addAttribute(MageBrews_Locator_Model_Location::ENTITY, 'longitude', array(
    'input'             => 'text',
    'type'              => 'static',
    'label'             => 'Longitude',
    'user_defined'      => false,
    'visible'           => 1,
    'required'          => 1,
    'position'          => 30,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));


//add new attribute to location edit form
$eavConfig = Mage::getSingleton('eav/config');

$formAttributes = array(
    'latitude',
    'longitude'
);

foreach($formAttributes as $code){
    $attribute = $eavConfig->getAttribute(MageBrews_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();
