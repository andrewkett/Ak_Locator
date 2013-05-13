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

class MageBrews_Locator_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup {

    /**
     * Prepare location attribute values to save in additional table
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'is_enabled'                => $this->_getValue($attr, 'enabled', 1),
            'is_visible'                => $this->_getValue($attr, 'visible', 1),
            'is_system'                 => $this->_getValue($attr, 'system', 1),
            'input_filter'              => $this->_getValue($attr, 'input_filter', null),
            'multiline_count'           => $this->_getValue($attr, 'multiline_count', 0),
            'validate_rules'            => $this->_getValue($attr, 'validate_rules', null),
            'data_model'                => $this->_getValue($attr, 'data', null),
            'sort_order'                => $this->_getValue($attr, 'position', 0)
        ));

        return $data;
    }

    /**
     * Add customer attributes to location forms
     *
     * @return void
     */
    public function installLocationForms()
    {
        $location           = (int)$this->getEntityTypeId('magebrews_locator_location');

        $attributeIds       = array();
        $select = $this->getConnection()->select()
            ->from(
                array('ea' => $this->getTable('eav/attribute')),
                array('entity_type_id', 'attribute_code', 'attribute_id'))
            ->where('ea.entity_type_id IN(?)', array($location));

        foreach ($this->getConnection()->fetchAll($select) as $row) {
            $attributeIds[$row['entity_type_id']][$row['attribute_code']] = $row['attribute_id'];
        }

        $data       = array();
        $entities   = $this->getDefaultEntities();

        $attributes = $entities['magebrews_locator_location']['attributes'];
        foreach ($attributes as $attributeCode => $attribute) {
            $attributeId = $attributeIds[$location][$attributeCode];
            $attribute['system'] = isset($attribute['system']) ? $attribute['system'] : true;
            $attribute['visible'] = isset($attribute['visible']) ? $attribute['visible'] : true;
            if ($attribute['system'] != true || $attribute['visible'] != false) {
                $usedInForms = array(
                    'location_create',
                    'location_edit'
                );

                foreach ($usedInForms as $formCode) {
                    $data[] = array(
                        'form_code'     => $formCode,
                        'attribute_id'  => $attributeId
                    );
                }
            }
        }

        if ($data) {
            $this->getConnection()->insertMultiple($this->getTable('magebrews_locator/form_attribute'), $data);
        }
    }



    public function getDefaultEntities() {
        return array(
            MageBrews_Locator_Model_Location::ENTITY => array(
                'entity_model' => 'magebrews_locator/location',
                'table' => 'magebrews_locator/location', /* Maps to the config.xml > global > models > magebrews_locator_resource > entities > location */
                'attribute_model'                => 'magebrews_locator/attribute',
                'increment_model'                => 'eav/entity_increment_numeric',
                'additional_attribute_table'     => 'magebrews_locator/eav_attribute',
                'entity_attribute_collection'    => 'magebrews_locator/attribute_collection',
                'attributes' => array(
                    'title' => array(
                        'type' => 'varchar',
                        'label' => 'Title',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 10,
                        'position' => 10,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),

                    'geocoded' => array(
                        'type'               => 'int',
                        'label'              => 'geocoded',
                        'input'              => 'hidden',
                        'sort_order'         => 20,
                        'position'           => 20,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),

                    'url_key' => array(
                        'type' => 'varchar',
                        'label' => 'Url Key',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 30,
                        'position' => 30,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),

                    'address' => array(
                        'type'               => 'text',
                        'label'              => 'Address',
                        'input'              => 'text',
                        'sort_order'         => 40,
                        'multiline_count'    => 2,
                        'validate_rules'     => 'a:2:{s:15:"max_text_length";i:255;s:15:"min_text_length";i:1;}',
                        'position'           => 40,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),

                    'country' => array(
                      //'description' => 'Two letter ISO country code of this address.',
                      'type' => 'varchar',
                      'label' => 'Country',
                      'input' => 'text',
                      'required' => false,
                      'sort_order' => 20,
                      'position' => 20,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'administrative_area' => array(
                      //'description' => 'The administrative area of this address. (i.e. State/Province)',
                      'label' => 'Administrative Area',
                      'type' => 'varchar',
                      'input' => 'text',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'sub_administrative_area' => array(
                      //'description' => 'The sub administrative area of this address.',
                      'label' => 'Sub Administrative Area',
                      'type' => 'varchar',
                      'input' => 'text',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'locality' => array(
                      //'description' => 'The locality of this address. (i.e. City)',
                      'type' => 'varchar',
                      'input' => 'text',
                      'label' => 'Locality',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'dependent_locality' => array(
                      //'description' => 'The dependent locality of this address.',
                      'label' => 'Dependent Locality',
                      'type' => 'varchar',
                      'input' => 'text',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'postal_code' => array(
                      //'description' => 'The postal code of this address.',
                      'label' => 'Postal Code',
                      'type' => 'varchar',
                      'input' => 'text',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'thoroughfare' => array(
                      //'description' => 'The thoroughfare of this address. (i.e. Street address)',
                      'label' => 'Thoroughfare',
                      'type' => 'varchar',
                      'input' => 'text',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'premise' => array(
                      //'description' => 'The premise of this address. (i.e. Apartment / Suite number)',
                      'label' => 'Premise',
                      'type' => 'varchar',
                      'input' => 'text',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'sub_premise' => array(
                      //'description' => 'The sub_premise of this address.',
                      'label' => 'Sub Premise',
                      'type' => 'varchar',
                      'input' => 'text',
                      'default' => '',
                      'required' => FALSE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'data' => array(
                      //'description' => 'Additional data for this address.',
                      'label' => 'Additional Data',
                      'type' => 'varchar',
                      'input' => 'text',
                      'required' => FALSE,
                      'serialize' => TRUE,
                      'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),

                    'latitude' => array(
                        'type' => 'varchar',
                        'label' => 'Latitude',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 30,
                        'position' => 30,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'longitude' => array(
                        'type' => 'varchar',
                        'label' => 'Longitude',
                        'input' => 'text',
                        'required' => true,
                        'sort_order' => 20,
                        'position' => 20,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                    'is_enabled' => array(
                        'type' => 'int',
                        'label' => 'Is Enabled',
                        'input' => 'select',
                        'required' => true,
                        'sort_order' => 50,
                        'position' => 50,
                        'required' => false,
                        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                    ),
                )
            )
        );



    }
}
