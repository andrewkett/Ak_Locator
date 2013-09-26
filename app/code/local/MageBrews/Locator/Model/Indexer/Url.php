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

class MageBrews_Locator_Model_Indexer_Url extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_url_match_result';

    /**
     * Index math: product save, category save, store save
     * store group save, config save
     *
     * @var array
     */
    protected $_matchedEntities = array(
        MageBrews_Locator_Model_Location::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    protected $_relatedConfigSettings = array(
        Mage_Catalog_Helper_Category::XML_PATH_CATEGORY_URL_SUFFIX,
        Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX,
        Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
    );

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('magebrews_locator')->__('Locator URL Rewrites');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('magebrews_locator')->__('Index location URL rewrites');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store && ($store->isObjectNew() || $store->dataHasChangedFor('group_id'))) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Config_Data::ENTITY) {
            $configData = $event->getDataObject();
            if ($configData && in_array($configData->getPath(), $this->_relatedConfigSettings)) {
                $result = $configData->isValueChanged();
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();
        switch ($entity) {
            case Mage_Catalog_Model_Product::ENTITY:
               $this->_registerProductEvent($event);
                break;

            // case Mage_Catalog_Model_Category::ENTITY:
            //     $this->_registerCategoryEvent($event);
            //     break;

            // case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
            //     $event->addNewData('catalog_url_reindex_all', true);
            //     break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
            case Mage_Core_Model_Config_Data::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerLocationEvent(Mage_Index_Model_Event $event)
    {
        //echo 'herwrew';
        $location = $event->getDataObject();
        $dataChange = $location->dataHasChangedFor('url_key')
            || $location->getIsChangedWebsites();

        if (!$location->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_location_ids', array($location->getId()));
        }
    }

    // /**
    //  * Register event data during category save process
    //  *
    //  * @param Mage_Index_Model_Event $event
    //  */
    // protected function _registerCategoryEvent(Mage_Index_Model_Event $event)
    // {
    //     $category = $event->getDataObject();
    //     if (!$category->getInitialSetupFlag() && $category->getLevel() > 1) {
    //         if ($category->dataHasChangedFor('url_key') || $category->getIsChangedProductList()) {
    //             $event->addNewData('rewrite_category_ids', array($category->getId()));
    //         }
    //         /**
    //          * Check if category has another affected category ids (category move result)
    //          */
    //         if ($category->getAffectedCategoryIds()) {
    //             $event->addNewData('rewrite_category_ids', $category->getAffectedCategoryIds());
    //         }
    //     }
    // }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_url_reindex_all'])) {
            $this->reindexAll();
        }

        /* @var $urlModel Mage_Catalog_Model_Url */
        $urlModel = Mage::getSingleton('catalog/url');

        // Force rewrites history saving
        $dataObject = $event->getDataObject();
        if ($dataObject instanceof Varien_Object && $dataObject->hasData('save_rewrites_history')) {
            $urlModel->setShouldSaveRewritesHistory($dataObject->getData('save_rewrites_history'));
        }

        if(isset($data['rewrite_location_ids'])) {
            $urlModel->clearStoreInvalidRewrites(); // Maybe some products were moved or removed from website
            foreach ($data['rewrite_location_ids'] as $productId) {
                 $urlModel->refreshProductRewrite($productId);
            }
        }
        // if (isset($data['rewrite_category_ids'])) {
        //     $urlModel->clearStoreInvalidRewrites(); // Maybe some categories were moved
        //     foreach ($data['rewrite_category_ids'] as $categoryId) {
        //         $urlModel->refreshCategoryRewrite($categoryId);
        //     }
        // }
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        /** @var $resourceModel Mage_Catalog_Model_Resource_Url */
        $resourceModel = Mage::getResourceSingleton('magebrews_locator/url');
        $resourceModel->beginTransaction();
        try {
            Mage::getSingleton('magebrews_locator/url')->refreshRewrites();
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
    }
}
