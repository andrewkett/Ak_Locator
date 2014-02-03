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

class Ak_Locator_Block_Search_Form extends Mage_Core_Block_Template
{

    const XML_PATH_DEFAULT_DISTANCE = "locator_settings/search/default_search_distance";
    const XML_PATH_SHOW_SEARCH = "locator_settings/search/show_search_filter";
    const XML_PATH_SHOW_DISTANCE = "locator_settings/search/show_distance_filter";

    public function __construct(){
        parent::__construct();
        $this->setTemplate('locator/search/simple-form.phtml');
    }


    public function showSearchFilter()
    {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_SEARCH);
    }

    public function showDistanceFilter()
    {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_DISTANCE);
    }

    public function getCurrentDistance($request)
    {
        if($request->getParam('distance')){
            return $request->getParam('distance');
        }else{
            return Mage::getStoreConfig(self::XML_PATH_DEFAULT_DISTANCE);
        }
    }
}
