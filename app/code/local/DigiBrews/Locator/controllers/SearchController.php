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

/**
*
*/
class DigiBrews_Locator_SearchController extends Mage_Core_Controller_Front_Action
{

 public function indexAction()
  {
      try{

        //if customer is logged in and they have an address use that when there is no search
        if(!$this->getRequest()->getParams()){
          $session = Mage::getSingleton('customer/session');
          if($session->isLoggedIn()){
            $addressId = $session->getCustomer()->getDefaultBilling();

            $address = Mage::getModel('customer/address')->load($addressId);
            $street = $address->getStreet();

            $search = @$street[0].' '.@$street[1].', '.$address->getCity().', '.$address->getRegion().', '.$address->getPostcode().', '.$address->getCountry();
            $this->getRequest()->setQuery('s',$search);
          }
        }

        $this->loadLayout();

        //if there are no locations returned go to the noresults action now
        if(!count($this->getLayout()->getBlock('search')->getLocations()->getItems())){
          $this->_forward('noresults');
          return;
        }

        if($this->getRequest()->isXmlHttpRequest()){
          echo $this->getLayout()->getBlock('search')->asJson();
          die();
        }else{
          $this->renderLayout();
        }

      }catch( Exception $e ) {

          if ($e instanceof DigiBrews_Locator_Model_Exception_Geocode || $e instanceof DigiBrews_Locator_Model_Exception_NoResults) {
            $this->_forward('noresults');
            return;
          }

          throw $e;
      }
  }

  public function noresultsAction(){

    if($this->getRequest()->isXmlHttpRequest()){
      $obj = new Varien_Object();
      $obj->setError(true);
      $obj->setErrorType('noresults');
      $obj->setMessage('No Results Found');
      echo $obj->toJson();
      die();
    }else{
      $this->loadLayout();
      $this->renderLayout();
    }
  }

}
