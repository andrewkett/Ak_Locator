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

class MageBrews_Locator_Adminhtml_Location_AttributeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Location Entity Type instance
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Return Location Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    protected function _getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType(MageBrews_Locator_Model_Location::ENTITY);
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('magebrews_locator/attributes')
            ->_addBreadcrumb(
                Mage::helper('magebrews_locator')->__('Locator'),
                Mage::helper('magebrews_locator')->__('Locator'))
            ->_addBreadcrumb(
                Mage::helper('magebrews_locator')->__('Manage Location Attributes'),
                Mage::helper('magebrews_locator')->__('Manage Location Attributes'));
        return $this;
    }

    /**
     * Retrieve locator attribute object
     */
    protected function _initAttribute()
    {
        $attribute = Mage::getModel('magebrews_locator/attribute');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }

    /**
     * Attributes grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Manage Locator Attributes'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new attribute action
     *
     */
    public function newAction()
    {
        $this->addActionLayoutHandles();
        $this->_forward('edit');
    }

    /**
     * Edit attribute action
     *
     */
    public function editAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()
            ->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title($this->__('Manage Location Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->_getSession()
                    ->addError(Mage::helper('magebrews_locator')->__('Attribute is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('magebrews_locator')->__('You cannot edit this attribute.'));
                $this->_redirect('*/*/');
                return;
            }

            $this->_title($attributeObject->getFrontendLabel());
        } else {
            $this->_title($this->__('New Attribute'));
        }

        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        Mage::register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId()
            ? Mage::helper('magebrews_locator')->__('Edit Location Attribute')
            : Mage::helper('magebrews_locator')->__('New Location Attribute');

        $this->_initAction()
            ->_addBreadcrumb($label, $label)
            ->renderLayout();
    }

    /**
     * Validate attribute action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode      = $this->getRequest()->getParam('attribute_code');
            $attributeObject    = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('magebrews_locator')->__('Attribute with the same code already exists')
                );

                $this->_initLayoutMessages('adminhtml/session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        return Mage::helper('magebrews_locator/location')->filterPostData($data);
    }

    /**
     * Save attribute action
     *
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost() && $data) {
        
            $attributeObject = $this->_initAttribute();
            /* @var $helper MageBrews_Location_Helper_Data */
            $helper = Mage::helper('magebrews_locator');

            //filtering
            try {
                $data = $this->_filterPostData($data);
            } catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                    if (isset($data['attribute_id'])) {
                        $this->_redirect('*/*/edit', array('_current' => true));
                    } else {
                        $this->_redirect('*/*/new', array('_current' => true));
                    }
                    return;
            }

            $attributeId = $this->getRequest()->getParam('attribute_id');
            if ($attributeId) {
                $attributeObject->load($attributeId);
                if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                    $this->_getSession()->addError(
                         Mage::helper('magebrews_locator')->__('You cannot edit this attribute.')
                    );
                    $this->_getSession()->addAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code']     = $attributeObject->getAttributeCode();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['frontend_input']     = $attributeObject->getFrontendInput();
                $data['is_user_defined']    = $attributeObject->getIsUserDefined();
                $data['is_system']          = $attributeObject->getIsSystem();
            } else {
                $data['backend_model']      = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                $data['source_model']       = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_type']       = $helper->getAttributeBackendTypeByInputType($data['frontend_input']);
                $data['is_user_defined']    = 1;
                $data['is_system']          = 0;

                // add set and group info
                $data['attribute_set_id']   = $this->_getEntityType()->getDefaultAttributeSetId();
                $data['attribute_group_id'] = Mage::getModel('eav/entity_attribute_set')
                    ->getDefaultGroupId($data['attribute_set_id']);
            }

            if (isset($data['used_in_forms']) && is_array($data['used_in_forms'])) {
                $data['used_in_forms'][] = 'magebrews_locator_location';
            }

            $defaultValueField = $helper->getAttributeDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $scopeKeyPrefix = ($this->getRequest()->getParam('website') ? 'scope_' : '');
                $data[$scopeKeyPrefix . 'default_value'] = $helper->stripTags(
                    $this->getRequest()->getParam($scopeKeyPrefix . $defaultValueField));
            }

            $data['entity_type_id']     = $this->_getEntityType()->getId();
            $data['validate_rules']     = $helper->getAttributeValidateRules($data['frontend_input'], $data);

            $validateRulesErrors = $helper->checkValidateRules($data['frontend_input'], $data['validate_rules']);
            if (count($validateRulesErrors)) {
                foreach ($validateRulesErrors as $message) {
                    $this->_getSession()->addError($message);
                }
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }

            $attributeObject->addData($data);

            /**
             * Check "Use Default Value" checkboxes values
             */
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $key) {
                    $attributeObject->setData('scope_' . $key, null);
                }
            }

            try {
                Mage::dispatchEvent('magebrews_locator_attribute_before_save', array(
                    'attribute' => $attributeObject
                ));
                $attributeObject->save();
                Mage::dispatchEvent('magebrews_locator_attribute_save', array(
                    'attribute' => $attributeObject
                ));

                $this->_getSession()->addSuccess(
                     Mage::helper('magebrews_locator')->__('The location attribute has been saved.')
                );
                $this->_getSession()->setAttributeData(false);
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array(
                        'attribute_id'  => $attributeObject->getId(),
                        '_current'      => true
                    ));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                     Mage::helper('magebrews_locator')->__('An error occurred while saving the location attribute.')
                );
                $this->_getSession()->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Delete attribute action
     *
     */
    public function deleteAction()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()
                || !$attributeObject->getIsUserDefined())
            {
                $this->_getSession()->addError(
                     Mage::helper('magebrews_locator')->__('You cannot delete this attribute.')
                );
                $this->_redirect('*/*/');
                return;
            }
            try {
                $attributeObject->delete();
                Mage::dispatchEvent('magebrews_locator_attribute_delete', array(
                    'attribute' => $attributeObject
                ));

                $this->_getSession()->addSuccess(
                     Mage::helper('magebrews_locator')->__('The location attribute has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                     Mage::helper('magebrews_locator')->__('An error occurred while deleting the location attribute.')
                );
                $this->_redirect('*/*/edit', array('attribute_id' => $attributeId, '_current' => true));
                return;
            }
        }

        $this->_redirect('*/*/');
        return;
    }

    /**
     * Check whether attributes management functionality is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        Mage::log('write alternative to return Mage::getSingleton("admin/session")->isAllowed("admin/customer/attributes/customer_attributes");');
        return true;
        //return Mage::getSingleton('admin/session')->isAllowed('admin/customer/attributes/customer_attributes');
    }
}
