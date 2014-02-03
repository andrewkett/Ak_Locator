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

class Ak_Locator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const BROWSER_CACHE_CONFIG_PATH = 'locator_settings/search/leverage_browser_caching';

    /**
     * Return available location attribute form as select options
     *
     * @throws Mage_Core_Exception
     */
    public function getAttributeFormOptions()
    {
        Mage::throwException(Mage::helper('ak_locator')->__('Use helper with defined EAV entity'));
    }

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
     * Return available location attribute form as select options
     *
     * @return array
     */
    public function getLocationAttributeFormOptions()
    {
        return Mage::helper('ak_locator/location')->getAttributeFormOptions();
    }

    /**
     * Returns array of user defined attribute codes for location entity type
     *
     * @return array
     */
    public function getLocationUserDefinedAttributeCodes()
    {
        return Mage::helper('ak_locator/location')->getUserDefinedAttributeCodes();
    }



    // /**
    //  * Return data array of available attribute Input Types
    //  *
    //  * @param string|null $inputType
    //  * @return array
    //  */
    // public function getAttributeInputTypes($inputType = null)
    // {
    //     $inputTypes = parent::getAttributeInputTypes($inputType);

    //     // -- Multiline currently showing on form but not saving
    //     unset($inputTypes['multiline']);

    //     // -- Boolean causes whole page to error
    //     unset($inputTypes['boolean']);

    //     // file and input both not saving
    //     unset($inputTypes['file']);
    //     unset($inputTypes['image']);

    //     return $inputTypes;
    // }

    /**
     * Is browser caching enabled for searches
     *
     * @return bool
     */
    public function browserCacheEnabled()
    {
        return (bool)Mage::getStoreConfig(self::BROWSER_CACHE_CONFIG_PATH);
    }
    
    
    public function getBreadcrumbPath()
    {
        $path = array();

        //@note removed this for now as it only works for string searches and cant be used with varnish

        // $backUrl = $_SESSION['core']['last_url'];
        // $paramU = strpos($backUrl,'s=');
        // $paramDistance = strpos($backUrl,'&distance');
        // $modulename = Mage::app()->getRequest()->getModuleName();
        // $controlername = Mage::app()->getRequest()->getControllerName();
        
        // if($modulename=='locator' && $controlername=='location' && isset($paramU) && isset($paramDistance)){
        //     $path['location_search_result'] = array(
        //         'label'=> 'results',
        //         'link' => $backUrl
        //     );
        // }

        // if($s=Mage::app()->getRequest()->getParam('s') && $distance=Mage::app()->getRequest()->getParam('distance') ){
        //     $path['location_search_result'] = array(
        //         'label'=> 'results',
        //         'link' => null
        //     );
        // }

        if($id=Mage::app()->getRequest()->getParam('id')){
            $locations = Mage::getModel('ak_locator/location')->getCollection()
                ->addAttributeToSelect('title')
                ->addAttributeToFilter('entity_id',$id)
                ->load();
            $items = $locations->getItems();
            $location = reset($items);
            $path['location_detail'] = array(
                'label'=> $location->getTitle(),
                'link' => ''
            );
        }
        return $path;
    }



}
