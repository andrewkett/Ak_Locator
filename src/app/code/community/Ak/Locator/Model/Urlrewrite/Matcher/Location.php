<?php

class Ak_Locator_Model_Urlrewrite_Matcher_Location
{

    public function __construct()
    {
        $this->_seoSuffix =  Mage::getStoreConfig(Ak_Locator_Helper_Location::XML_PATH_LOCATION_URL_SUFFIX);
    }

    /**
     * Match product rewrite
     *
     * @param array $rewriteRow
     * @param string $requestPath
     * @return bool
     */
    public function match(array $rewriteRow, $requestPath)
    {
        $entityType = Mage::getModel('eav/config')->getEntityType('ak_locator_location');
        $entityTypeId = $entityType->getEntityTypeId();

        if ($entityTypeId != $rewriteRow['entity_type']) {
            return false;
        }

        $rewriteParts = explode('/', $rewriteRow['request_path']);
        $rewriteTail = array_pop($rewriteParts);

        if (!empty($this->_seoSuffix)) {
            $rewriteTail .= $this->_seoSuffix;
        }

        $requestParts = explode('/', $requestPath);
        $requestTail = array_pop($requestParts);

        if (strcmp($rewriteTail, $requestTail) === 0) {

            $locationId = substr($rewriteRow['target_path'], strrpos($rewriteRow['target_path'], '/')+1);

            $isMatched = !empty($locationId);
               // && $this->_isRewriteRedefinedInStore($locationId, $rewriteRow['request_path']);

            if ($isMatched) {
                //$this->_checkStoreRedirect($productId, $categoryPath);
                return true;
            }
        }
        return false;
    }


//    /**
//     * Is rewrite redefined on store level
//     *
//     * @param $productId
//     * @param $requestPath
//     * @return bool
//     */
//    protected function _isRewriteRedefinedInStore($locationId, $requestPath)
//    {
//        // Check that url key isn't redefined on store level
//        $storeRewriteRow = $this->_productResource->getRewriteByStoreId($this->_prevStoreId, $locationId);
//        if (!empty($storeRewriteRow) && $storeRewriteRow['request_path'] != $requestPath) {
//            return false;
//        }
//        return true;
//    }
//
//    /**
//     * Redirect to product from another store if custom url key defined
//     *
//     * @param int $productId
//     * @param string $categoryPath
//     */
//    protected function _checkStoreRedirect($productId, $categoryPath)
//    {
//        if ($this->_prevStoreId != $this->_storeId) {
//            $rewrite = $this->_productResource->getRewriteByProductId($productId, $this->_storeId);
//            if (!empty($rewrite)) {
//                $requestPath = $rewrite['request_path'];
//                if (!empty($this->_newStoreSeoSuffix)) {
//                    $requestPath .= '.' . $this->_newStoreSeoSuffix;
//                }
//                if (!empty($categoryPath)) {
//                    $requestPath = $this->_getNewStoreCategoryPath($categoryPath) . '/' . $requestPath;
//                }
//
//                $requestPath = $this->_getBaseUrl() . $requestPath;
//                $this->_response->setRedirect($requestPath, 301);
//                $this->_request->setDispatched(true);
//            }
//        }
//    }
}
