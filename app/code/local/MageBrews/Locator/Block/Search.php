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
 * @link      http://andrewkett.github.io/MageBrews_Locator/
 */

class MageBrews_Locator_Block_Search extends Mage_Core_Block_Template
{
    
    const XML_PATH_SEARCH_META_TITLE = "locator_settings/seo/search_meta_title";
    const XML_PATH_SEARCH_META_DESC = "locator_settings/seo/search_meta_desc";
    const XML_PATH_SEARCH_META_KEY = "locator_settings/seo/search_meta_key";

    protected $_searchModel;

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $layout = $this->getLayout();
        
        $this->getLayout()->createBlock('magebrews_locator/breadcrumbs');

        if ($headBlock = $layout->getBlock('head')) {
            $headBlock->setTitle(Mage::getStoreConfig(self::XML_PATH_SEARCH_META_TITLE));
            $headBlock->setDescription(Mage::getStoreConfig(self::XML_PATH_SEARCH_META_DESC));
            $headBlock->setKeywords(Mage::getStoreConfig(self::XML_PATH_SEARCH_META_KEY));
        }

        return parent::_prepareLayout();
    }


    /**
     * Retrieve location collection based on search parameters
     *
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function getLocations()
    {
        if (!Mage::registry('locator_locations')) {
            $locations = $this->getSearch()->search($this->getRequest()->getParams());
            Mage::register('locator_locations', $locations);
        } else {
            $locations = Mage::registry('locator_locations');
        }

        $listBlock = $this->getListBlock($locations)->setData('locations', $locations);
        $this->setChild('list', $listBlock);

        return $locations;
    }


    /**
     * Represent the current search in a json format
     *
     * @return string
     */
    public function asJson()
    {
        $obj = $this->getLocations()->getResponseObject();
        $obj->setOutput($this->getChild('list')->toHtml());

        return $obj->toJson();
    }


    public function setSearch($model)
    {
        $this->_searchModel = $model;
    }

    /**
     * @return MageBrews_Locator_Model_Search
     */
    public function getSearch()
    {
        if ($this->_searchModel === null) {
            $this->setSearch(Mage::getModel('magebrews_locator/search'));
        }
        return $this->_searchModel;
    }


    /**
     * Get the child block which will render the list of locations
     *
     * @param $locations
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function getListBlock($locations)
    {
        $layout = $this->getLayout();

        //if the collection contains a point of search render accordingly otherwise just list normally
        if (!$locations->getSearchPoint()) {
            return $layout->createBlock('magebrews_locator/search_list_area');
        } else {
            return $layout->createBlock('magebrews_locator/search_list_point');
        }
    }

    /**
     * Check if params are valid
     *
     * @return mixed
     */
    public function hasValidParams()
    {
        return $this->getSearch()->isValidParams($this->getRequest()->getParams());
    }

}
