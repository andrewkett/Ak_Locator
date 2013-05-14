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

/* @var $installer magebrews_locator_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$lat = $installer->getAttribute('magebrews_locator_location','latitude_bak');
$long = $installer->getAttribute('magebrews_locator_location','longitude_bak');

$latitudeAtt = $lat['attribute_id'];
$longitudeAtt = $long['attribute_id'];

if($latitudeAtt && $longitudeAtt){
    //move lat/long data from old eav attribute to new db column - this should really be done with the ORM
    $query = 'UPDATE `magebrews_locator_location_entity` AS `e`
	INNER JOIN `magebrews_locator_location_entity_varchar` AS `eav1`
		ON e.entity_id = eav1.entity_id and eav1.attribute_id = '.$latitudeAtt.'
	INNER JOIN `magebrews_locator_location_entity_varchar` AS `eav2`
		ON e.entity_id = eav2.entity_id and eav2.attribute_id = '.$longitudeAtt.'
SET `e`.latitude = `eav1`.value, `e`.longitude = `eav2`.value';

    $installer->getConnection()->query($query);

}

$installer->endSetup();
