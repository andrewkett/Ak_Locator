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


//@todo need to copy old digibrews data across to new tables without losing eav attribute associations
$query = '
INSERT INTO magebrews_locator_location_entity 
SELECT *
FROM digibrews_locator_location_entity;

//these inserts copy the attribute id of the old entity which is wrong
INSERT INTO magebrews_locator_location_entity_varchar
SELECT *
FROM digibrews_locator_location_entity_varchar;

INSERT INTO magebrews_locator_location_entity_int
SELECT *
FROM digibrews_locator_location_entity_int;

INSERT INTO magebrews_locator_location_entity_text
SELECT *
FROM digibrews_locator_location_entity_text;
';


$installer->getConnection()->query($query);


$installer->endSetup();
