<?php
abstract class Ak_Locator_Helper_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * Array of User Defined attribute codes per entity type code
     *
     * @var array
     */
    protected $_userDefinedAttributeCodes = array();

    /**
     * Default attribute entity type code
     *
     * @throws Mage_Core_Exception
     */
    protected function _getEntityTypeCode()
    {
        Mage::throwException(Mage::helper('ak_locator')->__('Use helper with defined EAV entity'));
    }

    /**
     * Return available EAV entity attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return array(
            array(
                'label' => Mage::helper('ak_locator')->__('Default EAV Form'),
                'value' => 'default'
            )
        );
    }

    /**
     * Check validation rules for specified input type and return possible warnings.
     *
     * @param string $frontendInput
     * @param array $validateRules
     * @return array
     */
    public function checkValidateRules($frontendInput, $validateRules)
    {
        $errors = array();
        switch ($frontendInput) {
            case 'text':
            case 'textarea':
            case 'multiline':
                if (isset($validateRules['min_text_length']) && isset($validateRules['max_text_length'])) {
                    $minTextLength = (int) $validateRules['min_text_length'];
                    $maxTextLength = (int) $validateRules['max_text_length'];
                    if ($minTextLength > $maxTextLength) {
                        $errors[] = Mage::helper('ak_locator')->__('Wrong values for minimum and maximum text length validation rules.');
                    }
                }
                break;
            case 'date':
                if (isset($validateRules['date_range_min']) && isset($validateRules['date_range_max'])) {
                    $minValue = (int) $validateRules['date_range_min'];
                    $maxValue = (int) $validateRules['date_range_max'];
                    if ($minValue > $maxValue) {
                        $errors[] = Mage::helper('ak_locator')->__('Wrong values for minimum and maximum date validation rules.');
                    }
                }
                break;
            default:
                break;
        }

        return $errors;
    }

    /**
     * Return data array of available attribute Input Types
     *
     * @param string|null $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        $inputTypes = array(
            'text'          => array(
                'label'             => Mage::helper('ak_locator')->__('Text Field'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(
                    'alphanumeric',
                    'numeric',
                    'alpha',
                    'url',
                    'email',
                ),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'varchar',
                'default_value'     => 'text',
            ),
            'textarea'      => array(
                'label'             => Mage::helper('ak_locator')->__('Text Area'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'text',
                'default_value'     => 'textarea',
            ),
            'multiline'     => array(
                'label'             => Mage::helper('ak_locator')->__('Multiple Line'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'min_text_length',
                    'max_text_length',
                ),
                'validate_filters'  => array(
                    'alphanumeric',
                    'numeric',
                    'alpha',
                    'url',
                    'email',
                ),
                'filter_types'      => array(
                    'striptags',
                    'escapehtml'
                ),
                'backend_type'      => 'text',
                'default_value'     => 'text',
            ),
            'date'          => array(
                'label'             => Mage::helper('ak_locator')->__('Date'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'date_range_min',
                    'date_range_max'
                ),
                'validate_filters'  => array(
                    'date'
                ),
                'filter_types'      => array(
                    'date'
                ),
                'backend_model'     => 'eav/entity_attribute_backend_datetime',
                'backend_type'      => 'datetime',
                'default_value'     => 'date',
            ),
            'select'        => array(
                'label'             => Mage::helper('ak_locator')->__('Dropdown'),
                'manage_options'    => true,
                'option_default'    => 'radio',
                'validate_types'    => array(),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'source_model'      => 'eav/entity_attribute_source_table',
                'backend_type'      => 'int',
                'default_value'     => false,
            ),
            'multiselect'   => array(
                'label'             => Mage::helper('ak_locator')->__('Multiple Select'),
                'manage_options'    => true,
                'option_default'    => 'checkbox',
                'validate_types'    => array(),
                'filter_types'      => array(),
                'validate_filters'  => array(),
                'backend_model'     => 'eav/entity_attribute_backend_array',
                'source_model'      => 'eav/entity_attribute_source_table',
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
            'boolean'       => array(
                'label'             => Mage::helper('ak_locator')->__('Yes/No'),
                'manage_options'    => false,
                'validate_types'    => array(),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'source_model'      => 'eav/entity_attribute_source_boolean',
                'backend_type'      => 'int',
                'default_value'     => 'yesno',
            ),
            'file'          => array(
                'label'             => Mage::helper('ak_locator')->__('File (attachment)'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'max_file_size',
                    'file_extensions'
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
            'image'         => array(
                'label'             => Mage::helper('ak_locator')->__('Image File'),
                'manage_options'    => false,
                'validate_types'    => array(
                    'max_file_size',
                    'max_image_width',
                    'max_image_heght',
                ),
                'validate_filters'  => array(),
                'filter_types'      => array(),
                'backend_type'      => 'varchar',
                'default_value'     => false,
            ),
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } elseif (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * Return options array of EAV entity attribute Front-end Input types
     *
     * @return array
     */
    public function getFrontendInputOptions()
    {
        $inputTypes = $this->getAttributeInputTypes();
        $options    = array();
        foreach ($inputTypes as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v['label']
            );
        }

        return $options;
    }

    /**
     * Return available attribute validation filters
     *
     * @return array
     */
    public function getAttributeValidateFilters()
    {
        return array(
            'alphanumeric'  => Mage::helper('ak_locator')->__('Alphanumeric'),
            'numeric'       => Mage::helper('ak_locator')->__('Numeric Only'),
            'alpha'         => Mage::helper('ak_locator')->__('Alpha Only'),
            'url'           => Mage::helper('ak_locator')->__('URL'),
            'email'         => Mage::helper('ak_locator')->__('Email'),
            'date'          => Mage::helper('ak_locator')->__('Date'),
        );
    }

    /**
     * Return available attribute filter types
     *
     * @return array
     */
    public function getAttributeFilterTypes()
    {
        return array(
            'striptags'     => Mage::helper('ak_locator')->__('Strip HTML Tags'),
            'escapehtml'    => Mage::helper('ak_locator')->__('Escape HTML Entities'),
            'date'          => Mage::helper('ak_locator')->__('Normalize Date')
        );
    }

    /**
     * Get EAV attribute's elements scope
     *
     * @return array
     */
    public function getAttributeElementScopes()
    {
        return array(
            'is_required'            => 'website',
            'is_visible'             => 'website',
            'multiline_count'        => 'website',
            'default_value_text'     => 'website',
            'default_value_yesno'    => 'website',
            'default_value_date'     => 'website',
            'default_value_textarea' => 'website',
            'date_range_min'         => 'website',
            'date_range_max'         => 'website'
        );
    }

    /**
     * Return default value field name by attribute input type
     *
     * @param string $inputType
     * @return string
     */
    public function getAttributeDefaultValueByInput($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (isset($inputTypes[$inputType])) {
            $value = $inputTypes[$inputType]['default_value'];
            if ($value) {
                return 'default_value_' . $value;
            }
        }
        return false;
    }

    /**
     * Return array of attribute validate rules
     *
     * @param string $inputType
     * @param array $data
     * @return array
     */
    public function getAttributeValidateRules($inputType, array $data)
    {
        $inputTypes = $this->getAttributeInputTypes();
        $rules      = array();
        if (isset($inputTypes[$inputType])) {
            foreach ($inputTypes[$inputType]['validate_types'] as $validateType) {
                if (!empty($data[$validateType])) {
                    $rules[$validateType] = $data[$validateType];
                }
            }
             //transform date validate rules to timestamp
            if ($inputType === 'date') {
                foreach(array('date_range_min', 'date_range_max') as $dateRangeBorder){
                    if (isset($rules[$dateRangeBorder])) {
                        $date = new Zend_Date($rules[$dateRangeBorder], $this->getDateFormat());
                        $rules[$dateRangeBorder] = $date->getTimestamp();
                    }
                }
            }

            if (!empty($inputTypes[$inputType]['validate_filters']) && !empty($data['input_validation'])) {
                if (in_array($data['input_validation'], $inputTypes[$inputType]['validate_filters'])) {
                    $rules['input_validation'] = $data['input_validation'];
                }
            }
        }
        return $rules;
    }

    /**
     * Return default attribute back-end model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    /**
     * Return default attribute backend storage type by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendTypeByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_type'])) {
            return $inputTypes[$inputType]['backend_type'];
        }
        return null;
    }

    /**
     * Returns array of user defined attribute codes
     *
     * @param string $entityTypeCode
     * @return array
     */
    protected function _getUserDefinedAttributeCodes($entityTypeCode)
    {
        if (empty($this->_userDefinedAttributeCodes[$entityTypeCode])) {
            $this->_userDefinedAttributeCodes[$entityTypeCode] = array();
            /* @var $config Mage_Eav_Model_Config */
            $config = Mage::getSingleton('eav/config');
            foreach ($config->getEntityAttributeCodes($entityTypeCode) as $attributeCode) {
                $attribute = $config->getAttribute($entityTypeCode, $attributeCode);
                if ($attribute && $attribute->getIsUserDefined()) {
                    $this->_userDefinedAttributeCodes[$entityTypeCode][] = $attributeCode;
                }
            }
        }
        return $this->_userDefinedAttributeCodes[$entityTypeCode];
    }

    /**
     * Returns array of user defined attribute codes for EAV entity type
     *
     * @return array
     */
    public function getUserDefinedAttributeCodes()
    {
        return $this->_getUserDefinedAttributeCodes($this->_getEntityTypeCode());
    }

    /**
     * return date format
     *
     * @return string
     */
    public function getDateFormat()
    {
        return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @throws Mage_Core_Exception
     * @return array
     */
    public function filterPostData($data)
    {
        if ($data) {
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = $this->stripTags($value);
                }
            }

            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^[a-z_0-9]{1,255}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    Mage::throwException(
                        Mage::helper('ak_locator')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.')
                    );
                }
            }
        }
        return $data;
    }
}
