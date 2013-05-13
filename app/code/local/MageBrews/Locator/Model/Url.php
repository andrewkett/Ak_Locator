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
class MageBrews_Locator_Model_Url
{

    /**
     * Number of characters allowed to be in URL path
     *
     * @var int
     */
    const MAX_REQUEST_PATH_LENGTH = 240;

    /**
     * Number of characters allowed to be in URL path
     * after MAX_REQUEST_PATH_LENGTH number of characters
     *
     * @var int
     */
    const ALLOWED_REQUEST_PATH_OVERFLOW = 10;

    /**
     * Resource model
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    protected $_resourceModel;

    /**
     * Rewrite cache
     *
     * @var array
     */
    protected $_rewrites = array();

    /**
     * Current url rewrite rule
     *
     * @var Varien_Object
     */
    protected $_rewrite;

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_productUrlSuffix = array();

    /**
     * Flag to overwrite config settings for Catalog URL rewrites history maintainance
     *
     * @var bool
     */
    protected $_saveRewritesHistory = null;


    protected $_stores;



    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store|array
     */
    public function getStores($storeId = null)
    {
        return $this->getResource()->getStores($storeId);
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Url
     */
    public function getResource()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = Mage::getResourceModel('magebrews_locator/url');
        }
        return $this->_resourceModel;
    }


    /**
     * Retrieve location model singleton
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getLocationModel()
    {
        return $this->getResource()->getLocationModel();
    }

    /**
     * Setter for $_saveRewritesHistory
     * Force Rewrites History save bypass config settings
     *
     * @param bool $flag
     * @return Mage_Catalog_Model_Url
     */
    public function setShouldSaveRewritesHistory($flag)
    {
        $this->_saveRewritesHistory = (bool)$flag;
        return $this;
    }

    /**
     * Indicate whether to save URL Rewrite History or not (create redirects to old URLs)
     *
     * @param int $storeId Store View
     * @return bool
     */
    public function getShouldSaveRewritesHistory($storeId = null)
    {
        if ($this->_saveRewritesHistory !== null) {
            return $this->_saveRewritesHistory;
        }
        return false;
        //return Mage::helper('catalog')->shouldSaveUrlRewritesHistory($storeId);
    }

    /**
     * Refresh all rewrite urls for some store or for all stores
     * Used to make full reindexing of url rewrites
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshRewrites($store->getId());
            }
            return $this;
        }

        $this->clearStoreInvalidRewrites($storeId);
        $this->refreshLocationRewrites($storeId);

        return $this;
    }


    /**
     * Refresh product rewrite
     *
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @return Mage_Catalog_Model_Url
     */
    protected function _refreshLocationRewrite($location, $storeId)
    {
        //echo 'here';
        if ($location->getUrlKey() == '') {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getTitle());
        }
        else {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getUrlKey());
        }

        $idPath      = $this->generatePath('id', $location, $storeId);
        $targetPath  = $this->generatePath('target', $location, $storeId);
        $requestPath = $this->getLocationRequestPath($location, $storeId);

        $updateKeys = true;


        $rewriteData = array(
            'store_id'      => $storeId,
            'id_path'       => $idPath,
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
            'is_system'     => 1,
            'location_id'   => $location->getId()
        );

        $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);

        // if ($this->getShouldSaveRewritesHistory($storeId)) {
        //     $this->_saveRewriteHistory($rewriteData, $this->_rewrite);
        // }
        // echo '$location->getUrlKey() = '.$location->getUrlKey()."<br />";
        // echo '$urlKey = '.$urlKey."<br />";
        if ($updateKeys && $location->getUrlKey() != $urlKey) {
            $location->setUrlKey($urlKey);
            $this->getResource()->saveLocationAttribute($location, 'url_key');
        }
        // if ($updateKeys && $location->getUrlPath() != $requestPath) {
        //     $location->setUrlPath($requestPath);
        //     $this->getResource()->saveLocationAttribute($location, 'url_path');
        // }

        return $this;
    }


    /**
     * Refresh product rewrite urls for one store or all stores
     * Called as a reaction on product change that affects rewrites
     *
     * @param int $productId
     * @param int|null $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshProductRewrite($locationId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshProductRewrite($productId, $store->getId());
            }
            return $this;
        }

        $location = $this->getResource()->getLocation($locationId, $storeId);

        if ($location) {
            $store = $this->getStores($storeId);
            $storeRootCategoryId = $store->getRootCategoryId();

            // List of categories the product is assigned to, filtered by being within the store's categories root
            $categories = $this->getResource()->getCategories($product->getCategoryIds(), $storeId);
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, $locationId);

            // Add rewrites for all needed categories
            // If product is assigned to any of store's categories -
            // we also should use store root category to create root product url rewrite
            if (!isset($categories[$storeRootCategoryId])) {
                $categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);
            }

            // Create product url rewrites
            foreach ($categories as $category) {
                $this->_refreshProductRewrite($product, $category);
            }

            // Remove all other product rewrites created earlier for this store - they're invalid now
            $excludeCategoryIds = array_keys($categories);
            $this->getResource()->clearProductRewrites($productId, $storeId, $excludeCategoryIds);

            unset($categories);
            unset($product);
        } else {
            // Product doesn't belong to this store - clear all its url rewrites including root one
            $this->getResource()->clearProductRewrites($productId, $storeId, array());
        }

        return $this;
    }

    /**
     * Refresh all product rewrites for designated store
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function refreshLocationRewrites($storeId)
    {
        // $this->_categories      = array();
        // $storeRootCategoryId    = $this->getStores($storeId)->getRootCategoryId();
        // $storeRootCategoryPath  = $this->getStores($storeId)->getRootCategoryPath();
        // $this->_categories[$storeRootCategoryId] = $this->getResource()->getCategory($storeRootCategoryId, $storeId);

        $lastEntityId = 0;
        $process = true;
        $i = 0;

        while ($process == true) {


            $locations = $this->getResource()->getLocationsByStore($storeId, $lastEntityId);
            if (!$locations) {
                $process = false;
                break;
            }
            //temporary to stop infinite loops
            if($i>30){
                $process = false;
                break;
            }
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, array_keys($locations));

            // $loadCategories = array();
            // foreach ($locations as $locations) {
            //     foreach ($product->getCategoryIds() as $categoryId) {
            //         if (!isset($this->_categories[$categoryId])) {
            //             $loadCategories[$categoryId] = $categoryId;
            //         }
            //     }
            // }

            // if ($loadCategories) {
            //     foreach ($this->getResource()->getCategories($loadCategories, $storeId) as $category) {
            //         $this->_categories[$category->getId()] = $category;
            //     }
            // }

            foreach ($locations as $location) {

                $this->_refreshLocationRewrite($location, $storeId);
                // foreach ($product->getCategoryIds() as $categoryId) {
                //     if ($categoryId != $storeRootCategoryId && isset($this->_categories[$categoryId])) {
                //         if (strpos($this->_categories[$categoryId]['path'], $storeRootCategoryPath . '/') !== 0) {
                //             continue;
                //         }
                //         $this->_refreshProductRewrite($product, $this->_categories[$categoryId]);
                //     }
                // }
            }

            unset($locations);
            $this->_rewrites = array();

            $i++;
        }

        return $this;
    }

    /**
     * Deletes old rewrites for store, left from the times when store had some other root category
     *
     * @param int $storeId
     * @return Mage_Catalog_Model_Url
     */
    public function clearStoreInvalidRewrites($storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->clearStoreInvalidRewrites($store->getId());
            }
            return $this;
        }

        $this->getResource()->clearStoreInvalidRewrites($storeId);
        return $this;
    }

    /**
     * Get requestPath that was not used yet.
     *
     * Will try to get unique path by adding -1 -2 etc. between url_key and optional url_suffix
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $idPath
     * @return string
     */
    public function getUnusedPath($storeId, $requestPath, $idPath)
    {
        //skip this for now
        return;
        echo 'here'; die();
        $suffix = $this->getLocationUrlSuffix($storeId);

        if (empty($requestPath)) {
            $requestPath = '-';
        } elseif ($requestPath == $suffix) {
            $requestPath = '-' . $suffix;
        }

        /**
         * Validate maximum length of request path
         */
        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }


        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            if ($this->_rewrites[$idPath]->getRequestPath() == $requestPath) {
                return $requestPath;
            }
        }
        else {
            $this->_rewrite = null;
        }

        echo '<br /><br />rewrite is '.$this->getResource()->getRewriteByRequestPath($requestPath, $storeId).'<br /><br />';
        die();
        $rewrite = $this->getResource()->getRewriteByRequestPath($requestPath, $storeId);
        if ($rewrite && $rewrite->getId()) {
            if ($rewrite->getIdPath() == $idPath) {
                $this->_rewrite = $rewrite;
                return $requestPath;
            }
            // match request_url abcdef1234(-12)(.html) pattern
            $match = array();
            $regularExpression = '#^([0-9a-z/-]+?)(-([0-9]+))?('.preg_quote($suffix).')?$#i';
            // if (!preg_match($regularExpression, $requestPath, $match)) {
            //     return $this->getUnusedPath($storeId, '-', $idPath);
            // }
            $match[1] = $match[1] . '-';
            $match[4] = isset($match[4]) ? $match[4] : '';

            $lastRequestPath = $this->getResource()
                ->getLastUsedRewriteRequestIncrement($match[1], $match[4], $storeId);
            if ($lastRequestPath) {
                $match[3] = $lastRequestPath;
            }
            return $match[1]
                . (isset($match[3]) ? ($match[3]+1) : '1')
                . $match[4];
        }
        else {
            return $requestPath;
        }
    }

    /**
     * Retrieve product rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getLocationUrlSuffix($storeId)
    {
        //return '';
        //return 'locations/';
        return Mage::helper('catalog/product')->getProductUrlSuffix($storeId);
    }


    /**
     * Check if current generated request path is one of the old paths
     *
     * @param string $requestPath
     * @param string $idPath
     * @param int $storeId
     * @return bool
     */
    protected function _deleteOldTargetPath($requestPath, $idPath, $storeId)
    {
        $finalOldTargetPath = $this->getResource()->findFinalTargetPath($requestPath, $storeId);
        if ($finalOldTargetPath && $finalOldTargetPath == $idPath) {
            $this->getResource()->deleteRewriteRecord($requestPath, $storeId, true);
            return true;
        }

        return false;
    }

    /**
     * Get unique product request path
     *
     * @param   Varien_Object $location
     * @return  string
     */
    public function getLocationRequestPath($location, $storeId)
    {

        if ($location->getUrlKey() == '') {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getTitle());
        } else {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getUrlKey());
        }

        $suffix  = $this->getLocationUrlSuffix($storeId);
        $idPath  = $this->generatePath('id', $location, $storeId);

        /**
         * Prepare product base request path
         */
        $requestPath = $urlKey;

        if (strlen($requestPath) > self::MAX_REQUEST_PATH_LENGTH + self::ALLOWED_REQUEST_PATH_OVERFLOW) {
            $requestPath = substr($requestPath, 0, self::MAX_REQUEST_PATH_LENGTH);
        }

        $this->_rewrite = null;

        /**
         * Check $requestPath should be unique
         */
        if (isset($this->_rewrites[$idPath])) {
            $this->_rewrite = $this->_rewrites[$idPath];
            $existingRequestPath = $this->_rewrites[$idPath]->getRequestPath();

            if ($existingRequestPath == $requestPath . $suffix) {
                return $existingRequestPath;
            }

            $existingRequestPath = preg_replace('/' . preg_quote($suffix, '/') . '$/', '', $existingRequestPath);
            /**
             * Check if existing request past can be used
             */
            if ($location->getUrlKey() == '' && !empty($requestPath)
                && strpos($existingRequestPath, $requestPath) === 0
            ) {
                $existingRequestPath = preg_replace(
                    '/^' . preg_quote($requestPath, '/') . '/', '', $existingRequestPath
                );
                if (preg_match('#^-([0-9]+)$#i', $existingRequestPath)) {
                    return $this->_rewrites[$idPath]->getRequestPath();
                }
            }

            $fullPath = $requestPath.$suffix;
            if ($this->_deleteOldTargetPath($fullPath, $idPath, $storeId)) {
                return $fullPath;
            }
        }
        /**
         * Check 2 variants: $requestPath and $requestPath . '-' . $productId
         */
        $validatedPath = $this->getResource()->checkRequestPaths(
            array($requestPath.$suffix, $requestPath.'-'.$location->getId().$suffix),
            $storeId
        );

        if ($validatedPath) {
            return $validatedPath;
        }
        /**
         * Use unique path generator
         */
        return $this->getUnusedPath($storeId, $requestPath.$suffix, $idPath);
    }

    /**
     * Generate either id path, request path or target path for location
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     * $parentPath used only for generating category path
     *
     * @param string $type
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @param string $parentPath
     * @return string
     * @throws Mage_Core_Exception
     */
    public function generatePath($type = 'target', $location = null, $storeId)
    {
        if (!$location) {
            Mage::throwException(Mage::helper('core')->__('Please specify either a category or a product, or both.'));
        }

        // generate id_path
        if ('id' === $type) {
            return 'location/' . $location->getId();
        }

        // generate request_path
        if ('request' === $type) {
            // for category
            // if (!$product) {
            //     if ($category->getUrlKey() == '') {
            //         $urlKey = $this->getCategoryModel()->formatUrlKey($category->getTitle());
            //     }
            //     else {
            //         $urlKey = $this->getCategoryModel()->formatUrlKey($category->getUrlKey());
            //     }

            //     $categoryUrlSuffix = $this->getCategoryUrlSuffix($category->getStoreId());
            //     if (null === $parentPath) {
            //         $parentPath = $this->getResource()->getCategoryParentPath($category);
            //     }
            //     elseif ($parentPath == '/') {
            //         $parentPath = '';
            //     }
            //     $parentPath = Mage::helper('catalog/category')->getCategoryUrlPath($parentPath,
            //         true, $category->getStoreId());

            //     return $this->getUnusedPath($category->getStoreId(), $parentPath . $urlKey . $categoryUrlSuffix,
            //         $this->generatePath('id', null, $category)
            //     );
            // }

            // for product & category
            // if (!$category) {
            //     Mage::throwException(Mage::helper('core')->__('A category object is required for determining the product request path.')); // why?
            // }

            if ($location->getUrlKey() == '') {
                $urlKey = $this->getLocationModel()->formatUrlKey($location->getTitle());
            }
            else {
                $urlKey = $this->getLocationModel()->formatUrlKey($location->getUrlKey());
            }
            $productUrlSuffix  = $this->getLocationUrlSuffix($storeId);

            // if ($category->getLevel() > 1) {
            //     // To ensure, that category has url path either from attribute or generated now
            //     $this->_addCategoryUrlPath($category);
            //     $categoryUrl = Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath(),
            //         false, $category->getStoreId());
            //     return $this->getUnusedPath($category->getStoreId(), $categoryUrl . '/' . $urlKey . $productUrlSuffix,
            //         $this->generatePath('id', $product, $category)
            //     );
            // }


            // echo 'path is '.$this->getUnusedPath($category->getStoreId(), $urlKey . $productUrlSuffix,
            //     $this->generatePath('id', $product, $storeId)
            // );
            // die();

            // for product only
            return $this->getUnusedPath($category->getStoreId(), $urlKey . $productUrlSuffix,
                $this->generatePath('id', $product, $storeId)
            );
        }

        return 'locator/location/index/id/' . $location->getId();
    }

    /**
     * Return unique string based on the time in microseconds.
     *
     * @return string
     */
    public function generateUniqueIdPath()
    {
        return str_replace('0.', '', str_replace(' ', '_', microtime()));
    }

    /**
     * Create Custom URL Rewrite for old product/category URL after url_key changed
     * It will perform permanent redirect from old URL to new URL
     *
     * @param array $rewriteData New rewrite data
     * @param Varien_Object $rewrite Rewrite model
     * @return Mage_Catalog_Model_Url
     */
    protected function _saveRewriteHistory($rewriteData, $rewrite)
    {
        if ($rewrite instanceof Varien_Object && $rewrite->getId()) {
            $rewriteData['target_path'] = $rewriteData['request_path'];
            $rewriteData['request_path'] = $rewrite->getRequestPath();
            $rewriteData['id_path'] = $this->generateUniqueIdPath();
            $rewriteData['is_system'] = 0;
            $rewriteData['options'] = 'RP'; // Redirect = Permanent
            $this->getResource()->saveRewriteHistory($rewriteData);
        }

        return $this;
    }

}
