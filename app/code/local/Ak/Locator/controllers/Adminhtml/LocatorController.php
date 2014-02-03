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

class Ak_Locator_Adminhtml_LocatorController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Locator'))
             ->_title($this->__('Manage Locations'));

        $this->loadLayout()->renderLayout();
    }

    public function newAction()
    {
        Mage::register('location_isnew', true);
        $this->_forward('edit');
    }

    public function editAction()
    {

        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('ak_locator/location');
        if ($id) {
            $model->load((int) $id);
            if ($model->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $model->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ak_locator')->__('Example does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('location_data', $model);

        $this->loadLayout();
       
        if(Mage::registry('location_isnew')){
            $this->getLayout()->getBlock('root')->addBodyClass('location-new');
        }else{
            $this->getLayout()->getBlock('root')->addBodyClass('location-edit');    
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);    
        $this->renderLayout();
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('ak_locator/location');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ak_locator')->__('The example has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find the example to delete.'));
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('ak_locator/location');
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {
                if ($id) {
                    $model->setId($id);
                }
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('ak_locator')->__('Error saving location'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ak_locator')->__('Location was successfully saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // The following line decides if it is a "save" or "save and continue"
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ak_locator')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    public function postAction()
    {
        // $post = $this->getRequest()->getPost();
        // try {
        //     if (empty($post)) {
        //         Mage::throwException($this->__('Invalid form data.'));
        //     }

        //     /* here's your form processing */

        //     $message = $this->__('Your form has been submitted successfully.');
        //     Mage::getSingleton('adminhtml/session')->addSuccess($message);
        // } catch (Exception $e) {
        //     Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        // }
        //$this->_redirect('*/*');
    }
}
