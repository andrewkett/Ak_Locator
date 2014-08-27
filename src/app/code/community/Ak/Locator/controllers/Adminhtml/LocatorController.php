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
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('ak_locator')->__('Example does not exist')
                );
                $this->_redirect('*/*/');
            }
        }
        Mage::register('location_data', $model);

        $this->loadLayout();
       
        if (Mage::registry('location_isnew')) {
            $this->getLayout()->getBlock('root')->addBodyClass('location-new');
        } else {
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
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ak_locator')->__('The example has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('adminhtml')->__('Unable to find the example to delete.')
        );
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            $model = Mage::getModel('ak_locator/location');
            $id = (int) $this->getRequest()->getParam('id');           
            $formCode = 'location_create';
            if ($id) {
                $model->load($id);
                $formCode = 'location_edit';
            }
            /** @var $locatorForm Ak_Locator_Model_Form */
            $locatorForm = Mage::getModel('ak_locator/form');
            $locatorForm->setEntity($model)
                ->setFormCode($formCode)
                ->ignoreInvisible(false)
            ;
            
            $formData = $locatorForm->extractData($this->getRequest());
            
            $errors = $locatorForm->validateData($formData);
            if ($errors !== true) {
                foreach ($errors as $error) {
                    Mage::getSingleton('adminhtml/session')->addError($error);
                }
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->getResponse()->setRedirect($this->getUrl('*/locator/edit', array('id' => $model->getId())));
                return;
            }
            
            $locatorForm->compactData($formData);
            /*
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
            */
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            
            try {
                /*
                if ($id) {
                    $model->setId($id);
                }                  
                 */
                $model->save();

                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('ak_locator')->__('Error saving location'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ak_locator')->__('Location was successfully saved.')
                );
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
    
    public function viewfileAction()
    {
        $file   = null;
        $plain  = false;
        if ($this->getRequest()->getParam('file')) {
            // download file
            $file   = Mage::helper('core')->urlDecode($this->getRequest()->getParam('file'));
        } else if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file   = Mage::helper('core')->urlDecode($this->getRequest()->getParam('image'));
            $plain  = true;
        } else {
            return $this->norouteAction();
        }

        $path = Mage::getBaseDir('media') . DS . 'ak_locator_location';

        $ioFile = new Varien_Io_File();
        $ioFile->open(array('path' => $path));
        $fileName   = $ioFile->getCleanPath($path . $file);
        $path       = $ioFile->getCleanPath($path);

        if ((!$ioFile->fileExists($fileName) || strpos($fileName, $path) !== 0)
            && !Mage::helper('core/file_storage')->processStorageFile(str_replace('/', DS, $fileName))
        ) {
            return $this->norouteAction();
        }

        if ($plain) {
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }

            $ioFile->streamOpen($fileName, 'r');
            $contentLength = $ioFile->streamStat('size');
            $contentModify = $ioFile->streamStat('mtime');

            $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify))
                ->clearBody();
            $this->getResponse()->sendHeaders();

            while (false !== ($buffer = $ioFile->streamRead())) {
                echo $buffer;
            }
        } else {
            $name = pathinfo($fileName, PATHINFO_BASENAME);
            $this->_prepareDownloadResponse($name, array(
                'type'  => 'filename',
                'value' => $fileName
            ));
        }

        exit();
    }

}
