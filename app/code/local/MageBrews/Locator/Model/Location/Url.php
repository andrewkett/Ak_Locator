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


class MageBrews_Locator_Model_Location_Url extends Varien_Object
{
    const CACHE_TAG = 'url_rewrite';

    /**
     * Static URL instance
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_url;

    /**
     * Static URL Rewrite Instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected static $_urlRewrite;

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (!self::$_urlRewrite) {
            self::$_urlRewrite = Mage::getModel('core/url_rewrite');
        }
        return self::$_urlRewrite;
    }

    /**
     * 'no_selection' shouldn't be a valid image attribute value
     *
     * @param string $image
     * @return string
     */
    protected function _validImage($image)
    {
        if($image == 'no_selection') {
            $image = null;
        }
        return $image;
    }

    /**
     * Retrieve URL in current store
     *
     * @param MageBrews_Locator_Model_Location $location
     * @param array $params the URL route params
     * @return string
     */
    public function getUrlInStore(MageBrews_Locator_Model_Location $location, $params = array())
    {
        $params['_store_to_url'] = true;
        return $this->getUrl($location, $params);
    }

    /**
     * Retrieve Product URL
     *
     * @param  MageBrews_Locator_Model_Location $location
     * @param  bool $useSid forced SID mode
     * @return string
     */
    public function getLocationUrl($location, $useSid = null)
    {
        if ($useSid === null) {
            $useSid = Mage::app()->getUseSessionInUrl();
        }

        $params = array();
        if (!$useSid) {
            $params['_nosid'] = true;
        }

        return $this->getUrl($location, $params);
    }

    /**
     * Format Key for URL
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    // *
    //  * Retrieve Product Url path (with category if exists)
    //  *
    //  * @param Mage_Catalog_Model_Product $product
    //  * @param Mage_Catalog_Model_Category $category
    //  *
    //  * @return string
     
    // public function getUrlPath($location, $category=null)
    // {
    //     $path = $location->getData('url_path');
    //     return $path;

    // }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $params
     * @return string
     */
    public function getUrl(MageBrews_Locator_Model_Location $location, $params = array())
    {
        $routePath      = '';
        $routeParams    = $params;

        $storeId    = $location->getStoreId();
        // if (isset($params['_ignore_category'])) {
        //     unset($params['_ignore_category']);
        //     $categoryId = null;
        // } else {
        //     $categoryId = $product->getCategoryId() && !$product->getDoNotUseCategoryId()
        //         ? $product->getCategoryId() : null;
        // }

        if ($location->hasUrlDataObject()) {
            $requestPath = $location->getUrlDataObject()->getUrlRewrite();
            $routeParams['_store'] = $location->getUrlDataObject()->getStoreId();
        } else {
            $requestPath = $location->getRequestPath();
            if (empty($requestPath) && $requestPath !== false) {
                $idPath = sprintf('location/%d', $location->getEntityId());
                // if ($categoryId) {
                //     $idPath = sprintf('%s/%d', $idPath, $categoryId);
                // }
                $rewrite = $this->getUrlRewrite();
                $rewrite->setStoreId($storeId)
                    ->loadByIdPath($idPath);
                if ($rewrite->getId()) {
                    $requestPath = $rewrite->getRequestPath();
                    $location->setRequestPath($requestPath);
                } else {
                    $location->setRequestPath(false);
                }
            }
        }

        if (isset($routeParams['_store'])) {
            $storeId = Mage::app()->getStore($routeParams['_store'])->getId();
        }

        if ($storeId != Mage::app()->getStore()->getId()) {
            $routeParams['_store_to_url'] = true;
        }

        if (!empty($requestPath)) {
            $routeParams['_direct'] = $requestPath;
        } else {
            $routePath = 'locator/location/index';
            $routeParams['id']  = $location->getId();
            $routeParams['s']   = $location->getUrlKey();
        }

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

        return $this->getUrlInstance()->setStore($storeId)
            ->getUrl($routePath, $routeParams);
    }
}
