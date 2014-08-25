<?php
/**
 * Location attribute add/edit form main tab
 *
 * @category   Ak
 * @package    Ak_Locator
 */
class Ak_Locator_Block_Adminhtml_Location_Attribute_Edit_Tab_Main 
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Preparing global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $result = parent::_prepareLayout();
        $renderer = $this->getLayout()->getBlock('fieldset_element_renderer');
        if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
            Varien_Data_Form::setFieldsetElementRenderer($renderer);
        }
        return $result;
    }

   /**
     * Adding location form elements for edit form
     *
     * @return Ak_Locator_Block_Adminhtml_Location_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $attribute  = $this->getAttributeObject();
        $form       = $this->getForm();
        $fieldset   = $form->getElement('base_fieldset');
        /* @var $helper Ak_Locator_Helper_Data */
        $helper     = Mage::helper('ak_locator');

        $fieldset->removeField('frontend_class');
        $fieldset->removeField('is_unique');

        // update Input Types
        $element    = $form->getElement('frontend_input');
        $element->setValues($helper->getFrontendInputOptions());
        $element->setLabel(Mage::helper('ak_locator')->__('Input Type'));
        $element->setRequired(true);

        // add limitation to attribute code
        // location attribute code can have prefix "location_" and its length must be max length minus prefix length
        $element      = $form->getElement('attribute_code');
        $oldClassName = sprintf('maximum-length-%d', Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH);
        $newClassName = sprintf('maximum-length-%d', Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9);
        $class        = str_replace($oldClassName, $newClassName, $element->getClass());
        $element->setClass($class);
        $element->setNote(
            Mage::helper('eav')->__('For internal use. Must be unique with no spaces. Maximum length of attribute code must be less then %s symbols', Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9)
        );

        $fieldset->addField('multiline_count', 'text', array(
            'name'      => 'multiline_count',
            'label'     => Mage::helper('ak_locator')->__('Lines Count'),
            'title'     => Mage::helper('ak_locator')->__('Lines Count'),
            'required'  => true,
            'class'     => 'validate-digits-range digits-range-2-20',
            'note'      => Mage::helper('ak_locator')->__('Valid range 2-20')
        ), 'frontend_input');

        $fieldset->addField('input_validation', 'select', array(
            'name'      => 'input_validation',
            'label'     => Mage::helper('ak_locator')->__('Input Validation'),
            'title'     => Mage::helper('ak_locator')->__('Input Validation'),
            'values'    => array('' => Mage::helper('ak_locator')->__('None'))
        ), 'default_value_textarea');

        $fieldset->addField('min_text_length', 'text', array(
            'name'      => 'min_text_length',
            'label'     => Mage::helper('ak_locator')->__('Minimum Text Length'),
            'title'     => Mage::helper('ak_locator')->__('Minimum Text Length'),
            'class'     => 'validate-digits',
        ), 'input_validation');

        $fieldset->addField('max_text_length', 'text', array(
            'name'      => 'max_text_length',
            'label'     => Mage::helper('ak_locator')->__('Maximum Text Length'),
            'title'     => Mage::helper('ak_locator')->__('Maximum Text Length'),
            'class'     => 'validate-digits',
        ), 'min_text_length');

        $fieldset->addField('max_file_size', 'text', array(
            'name'      => 'max_file_size',
            'label'     => Mage::helper('ak_locator')->__('Maximum File Size (bytes)'),
            'title'     => Mage::helper('ak_locator')->__('Maximum File Size (bytes)'),
            'class'     => 'validate-digits',
        ), 'max_text_length');

        $fieldset->addField('file_extensions', 'text', array(
            'name'      => 'file_extensions',
            'label'     => Mage::helper('ak_locator')->__('File Extensions'),
            'title'     => Mage::helper('ak_locator')->__('File Extensions'),
            'note'      => Mage::helper('ak_locator')->__('Comma separated'),
        ), 'max_file_size');

        $fieldset->addField('max_image_width', 'text', array(
            'name'      => 'max_image_width',
            'label'     => Mage::helper('ak_locator')->__('Maximum Image Width (px)'),
            'title'     => Mage::helper('ak_locator')->__('Maximum Image Width (px)'),
            'class'     => 'validate-digits',
        ), 'file_extensions');

        $fieldset->addField('max_image_heght', 'text', array(
            'name'      => 'max_image_heght',
            'label'     => Mage::helper('ak_locator')->__('Maximum Image Height (px)'),
            'title'     => Mage::helper('ak_locator')->__('Maximum Image Height (px)'),
            'class'     => 'validate-digits',
        ), 'max_image_width');

        $fieldset->addField('input_filter', 'select', array(
            'name'      => 'input_filter',
            'label'     => Mage::helper('ak_locator')->__('Input/Output Filter'),
            'title'     => Mage::helper('ak_locator')->__('Input/Output Filter'),
            'values'    => array('' => Mage::helper('ak_locator')->__('None')),
        ));

        $fieldset->addField('date_range_min', 'date', array(
            'name'      => 'date_range_min',
            'label'     => Mage::helper('ak_locator')->__('Minimal value'),
            'title'     => Mage::helper('ak_locator')->__('Minimal value'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $helper->getDateFormat()
        ), 'default_value_date');

        $fieldset->addField('date_range_max', 'date', array(
            'name'      => 'date_range_max',
            'label'     => Mage::helper('ak_locator')->__('Maximum value'),
            'title'     => Mage::helper('ak_locator')->__('Maximum value'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $helper->getDateFormat()
        ), 'date_range_min');

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset('front_fieldset', array(
            'legend'    => Mage::helper('ak_locator')->__('Frontend Properties')
        ));

        $fieldset->addField('is_visible', 'select', array(
            'name'      => 'is_visible',
            'label'     => Mage::helper('ak_locator')->__('Show on Frontend'),
            'title'     => Mage::helper('ak_locator')->__('Show on Frontend'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('ak_locator')->__('Sort Order'),
            'title'     => Mage::helper('ak_locator')->__('Sort Order'),
            'required'  => true,
            'class'     => 'validate-digits'
        ));

        $fieldset->addField('used_in_forms', 'multiselect', array(
            'name'         => 'used_in_forms',
            'label'        => Mage::helper('ak_locator')->__('Forms to Use In'),
            'title'        => Mage::helper('ak_locator')->__('Forms to Use In'),
            'values'       => $helper->getLocationAttributeFormOptions(),
            'value'        => $attribute->getUsedInForms(),
            'can_be_empty' => true,
        ))->setSize(5);

        if ($attribute->getId()) {
            $elements = array();
            if ($attribute->getIsSystem()) {
                $elements = array('sort_order', 'is_visible', 'is_required', 'used_in_forms');
            }
            if (!$attribute->getIsUserDefined() && !$attribute->getIsSystem()) {
                $elements = array('sort_order', 'used_in_forms');
            }
            foreach ($elements as $elementId) {
                $form->getElement($elementId)->setDisabled(true);
            }

            $inputTypeProp = $helper->getAttributeInputTypes($attribute->getFrontendInput());

            // input_filter
            if ($inputTypeProp['filter_types']) {
                $filterTypes = $helper->getAttributeFilterTypes();
                $values = $form->getElement('input_filter')->getValues();
                foreach ($inputTypeProp['filter_types'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_filter')->setValues($values);
            }

            // input_validation getAttributeValidateFilters
            if ($inputTypeProp['validate_filters']) {
                $filterTypes = $helper->getAttributeValidateFilters();
                $values = $form->getElement('input_validation')->getValues();
                foreach ($inputTypeProp['validate_filters'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_validation')->setValues($values);
            }
        }
        

        $this->getForm()->setDataObject($this->getAttributeObject());

        Mage::dispatchEvent('ak_locator_attribute_edit_tab_general_prepare_form', array(
            'form'      => $form,
            'attribute' => $attribute
        ));

        return $this;
    }

    /**
     * Initialize form fileds values
     *
     * @return Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
     */
    protected function _initFormValues()
    {
        $attribute = $this->getAttributeObject();
        if ($attribute->getId() && $attribute->getValidateRules()) {
            $this->getForm()->addValues($attribute->getValidateRules());
        }
        $result = parent::_initFormValues();

        // get data using methods to apply scope
        $formValues = $this->getAttributeObject()->getData();
        foreach (array_keys($formValues) as $idx) {
            $formValues[$idx] = $this->getAttributeObject()->getDataUsingMethod($idx);
        }
        $this->getForm()->addValues($formValues);

        return $result;
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('ak_locator')->__('Properties');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('ak_locator')->__('Properties');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
