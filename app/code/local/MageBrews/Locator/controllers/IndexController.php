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
class MageBrews_Locator_IndexController extends Mage_Core_Controller_Front_Action
{

  public function indexAction()
  {
    $this->_forward('index','search');

  }


  function jsonAction(){
    echo Mage::getModel('magebrews_locator/search')->pointToLocations($position,20)->toJson();
  }


  public function writeLocationUrlAction()
  {
      Mage::getModel('magebrews_locator/indexer_url')->reindexAll(1);
  }

}
