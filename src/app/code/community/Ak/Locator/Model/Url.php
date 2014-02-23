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


/**
 * Locator url model
 */
class Ak_Locator_Model_Url
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
    protected $_locationUrlSuffix = array();


    /**
     * Flag to overwrite config settings for Catalog URL rewrites history maintainance
     *
     * @var bool
     */
    protected $_saveRewritesHistory = null;


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
            $this->_resourceModel = Mage::getResourceModel('ak_locator/url');
        }
        return $this->_resourceModel;
    }


    /**
     * Retrieve location model singleton
     *
     * @return Ak_Locator_Model_Location
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
     * @return Ak_Locator_Model_Url
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
        return Mage::helper('catalog')->shouldSaveUrlRewritesHistory($storeId);
    }

    /**
     * Refresh all rewrite urls for some store or for all stores
     * Used to make full reindexing of url rewrites
     *
     * @param int $storeId
     * @return Ak_Locator_Model_Url
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

        //@todo Look into this function - is it needed for locator?
        //$this->getResource()->clearCategoryProduct($storeId);

        return $this;
    }


    /**
     * Refresh location rewrite
     *
     * @param Varien_Object $location
     *
     * @return Ak_Locator_Model_Url
     */
    protected function _refreshLocationRewrite(Varien_Object $location)
    {


        Mage::log($location->getUrlKey(), Zend_Log::DEBUG, 'location_index.log');

        if ($location->getUrlKey() == '') {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getTitle());
        }
        else {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getUrlKey());
        }


        Mage::log($urlKey, Zend_Log::DEBUG, 'location_index.log');

        $idPath      = $this->generatePath('id', $location);
        $targetPath  = $this->generatePath('target', $location);
        $requestPath = $this->getLocationRequestPath($location);


        $updateKeys = true;

        $rewriteData = array(
            'store_id'      => $location->getStoreId(),
            'location_id'   => $location->getId(),
            'id_path'       => $idPath,
            'request_path'  => $requestPath,
            'target_path'   => $targetPath,
            'is_system'     => 1
        );

        $this->getResource()->saveRewrite($rewriteData, $this->_rewrite);

        if ($this->getShouldSaveRewritesHistory($location->getStoreId())) {
            $this->_saveRewriteHistory($rewriteData, $this->_rewrite);
        }

        if ($updateKeys && $location->getUrlKey() != $urlKey) {
            $location->setUrlKey($urlKey);
            $this->getResource()->saveLocationAttribute($location, 'url_key');
        }
//        if ($updateKeys && $location->getUrlPath() != $requestPath) {
//            $location->setUrlPath($requestPath);
//            $this->getResource()->saveLocationAttribute($location, 'url_path');
//        }

        return $this;
    }


    /**
     * Refresh product rewrite urls for one store or all stores
     * Called as a reaction on product change that affects rewrites
     *
     * @param int $locationId
     * @param int|null $storeId
     * @return Ak_Locator_Model_Url
     */
    public function refreshLocationRewrite($locationId, $storeId = null)
    {
        if (is_null($storeId)) {
            foreach ($this->getStores() as $store) {
                $this->refreshLocationRewrite($locationId, $store->getId());
            }
            return $this;
        }

        $location = $this->getResource()->getLocation($locationId, $storeId);
        if ($location) {
            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, '', $locationId);

            // Remove all other product rewrites created earlier for this store - they're invalid now
            $this->getResource()->clearLocationRewrites($locationId, $storeId);
            unset($location);
        } else {
            // Product doesn't belong to this store - clear all its url rewrites including root one
            $this->getResource()->clearLocationRewrites($locationId, $storeId, array());
        }

        return $this;
    }

    /**
     * Refresh all location rewrites for designated store
     *
     * @param int $storeId
     * @return Ak_Locator_Model_Url
     */
    public function refreshLocationRewrites($storeId)
    {
        $lastEntityId = 0;
        $process = true;

        while ($process == true) {
            $locations = $this->getResource()->getLocationsByStore($storeId, $lastEntityId);
            if (!$locations) {
                $process = false;
                break;
            }

            $this->_rewrites = $this->getResource()->prepareRewrites($storeId, false, array_keys($locations));

            foreach ($locations as $location) {
                $this->_refreshLocationRewrite($location);
            }

            unset($locations);
            $this->_rewrites = array();
        }

        return $this;
    }

    /**
     * Deletes old rewrites for store, left from the times when store had some other root category
     *
     * @param int $storeId
     * @return Ak_Locator_Model_Url
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

        $rewrite = $this->getResource()->getRewriteByRequestPath($requestPath, $storeId);
        if ($rewrite && $rewrite->getId()) {
            if ($rewrite->getIdPath() == $idPath) {
                $this->_rewrite = $rewrite;
                return $requestPath;
            }
            // match request_url abcdef1234(-12)(.html) pattern
            $match = array();
            $regularExpression = '#^([0-9a-z/-]+?)(-([0-9]+))?('.preg_quote($suffix).')?$#i';
            if (!preg_match($regularExpression, $requestPath, $match)) {
                return $this->getUnusedPath($storeId, '-', $idPath);
            }
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
     * Retrieve location rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getLocationUrlSuffix($storeId)
    {
        return Mage::helper('ak_locator/location')->getLocationUrlSuffix($storeId);
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
     * Get unique location request path
     *
     * @param   Varien_Object $location
     *
     * @return  string
     */
    public function getLocationRequestPath($location)
    {
        if ($location->getUrlKey() == '') {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getName());
        } else {
            $urlKey = $this->getLocationModel()->formatUrlKey($location->getUrlKey());
        }

        $storeId = $location->getStoreId();
        $suffix  = $this->getLocationUrlSuffix($storeId);
        $idPath  = $this->generatePath('id', $location);

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
     * Generate either id path, request path or target path for product and/or category
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     * $parentPath used only for generating category path
     *
     * @param string $type
     * @param Varien_Object $location
     * @param string $parentPath
     * @return string
     * @throws Mage_Core_Exception
     */
    public function generatePath($type = 'target', $location = null, $parentPath = null)
    {
        if (!$location) {
            Mage::throwException(Mage::helper('core')->__('Please specify a location.'));
        }

        // generate id_path
        if ('id' === $type) {
            return 'location/' . $location->getId();
        }

        // generate request_path
        if ('request' === $type) {

            if ($location->getUrlKey() == '') {
                $urlKey = $this->getLocationModel()->formatUrlKey($location->getName());
            }
            else {
                $urlKey = $this->getLocationModel()->formatUrlKey($location->getUrlKey());
            }
            $locationUrlSuffix  = $this->getLocationUrlSuffix($location->getStoreId());

            // for product only
            return $this->getUnusedPath($location->getStoreId(), $urlKey . $locationUrlSuffix,
                $this->generatePath('id', $location)
            );
        }

        // generate target_path
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
