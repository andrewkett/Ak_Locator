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

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'meta_title', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Meta Title',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 300,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'meta_keywords', array(
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Meta Keywords',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 310,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'meta_description', array(
    'input'         => 'textarea',
    'type'          => 'text',
    'label'         => 'Meta Description',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 320,
));


$formAttributes = array(
    'meta_description',
    'meta_keywords',
    'meta_title'
);


$eavConfig = Mage::getSingleton('eav/config');

foreach($formAttributes as $code){
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup();
