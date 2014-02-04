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


$installer->updateAttribute(Ak_Locator_Model_Location::ENTITY, 'latitude', array('attribute_code' => 'latitude_bak'));
$installer->updateAttribute(Ak_Locator_Model_Location::ENTITY, 'longitude', array('attribute_code' => 'longitude_bak'));


$installer->getConnection()
    ->addColumn($installer->getTable('ak_locator/location'), 'latitude', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned'  => true,
        'nullable'  => false,
        'scale'     => 12,
        'precision' => 18,
        'default'   => '0',
        'comment'   => 'location latitude value'
    ));

$installer->getConnection()
    ->addColumn($installer->getTable('ak_locator/location'), 'longitude', array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned'  => false,
        'nullable'  => true,
        'scale'     => 12,
        'precision' => 18,
        'default'   => '0',
        'comment'   => 'location latitude value'
    ));

$installer->getConnection()
    ->addKey(
        $installer->getTable('ak_locator/location'),
        'LOCATION_LATITUDE',
        'latitude'
    );

$installer->getConnection()
    ->addKey(
        $installer->getTable('ak_locator/location'),
        'LOCATION_LONGITUDE',
        'longitude'
    );

$installer->endSetup();
