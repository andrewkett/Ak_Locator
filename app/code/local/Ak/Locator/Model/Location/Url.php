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
 * Location Url model
 */
class Ak_Locator_Model_Location_Url extends Varien_Object
{
    const CACHE_TAG = 'url_rewrite';

    /**
     * URL instance
     *
     * @var Mage_Core_Model_Url
     */
    protected  $_url;

    /**
     * URL Rewrite Instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Initialize Url model
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_store = !empty($args['store']) ? $args['store'] : Mage::app()->getStore();
    }

    /**
     * Retrieve URL Instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (null === $this->_url) {
            $this->_url = Mage::getModel('core/url');
        }
        return $this->_url;
    }

    /**
     * Retrieve URL Rewrite Instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (null === $this->_urlRewrite) {
            $this->_urlRewrite = Mage::getModel('core/url_rewrite');
        }
        return $this->_urlRewrite;
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
     * @param Ak_Locator_Model_Location $location
     * @param array $params the URL route params
     * @return string
     */
    public function getUrlInStore(Ak_Locator_Model_Location $location, $params = array())
    {
        $params['_store_to_url'] = true;
        return $this->getUrl($location, $params);
    }

    /**
     * Retrieve Location URL
     *
     * @param  Ak_Locator_Model_Location $location
     * @param  bool $useSid forced SID mode
     * @return string
     */
    public function getLocationUrl(Ak_Locator_Model_Location $location, $useSid = null)
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
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('ak_locator/location_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * Retrieve Location Url path
     *
     * @param Ak_Locator_Model_Location $location
     *
     * @return string
     */
    public function getUrlPath($location)
    {
        $path = $location->getData('url_path');
        return $path;
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param Ak_Locator_Model_Location $location
     * @param array $params
     * @return string
     */
    public function getUrl(Ak_Locator_Model_Location $location, $params = array())
    {
        $url = $location->getData('url');

        if (!empty($url)) {
            return $url;
        }

        $requestPath = $location->getData('request_path');

        if (empty($requestPath)) {
            $requestPath = $this->_getRequestPath($location);
            $location->setRequestPath($requestPath);
        }



        if (isset($params['_store'])) {
            $storeId = $this->_getStoreId($params['_store']);
        } else {
            $storeId = $location->getStoreId();
        }

        if ($storeId != $this->_getStoreId()) {
            $params['_store_to_url'] = true;
        }

        // reset cached URL instance GET query params
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }

        $this->getUrlInstance()->setStore($storeId);
        $locationUrl = $this->_getLocationUrl($location, $requestPath, $params);
        $location->setData('url', $locationUrl);


        return $location->getData('url');
    }

    /**
     * Returns checked store_id value
     *
     * @param int|null $id
     * @return int
     */
    protected function _getStoreId($id = null)
    {
        return Mage::app()->getStore($id)->getId();
    }


    /**
     * Retrieve product URL based on requestPath param
     *
     * @param Ak_Locator_Model_Location $location
     * @param string $requestPath
     * @param array $routeParams
     *
     * @return string
     */
    protected function _getLocationUrl($location, $requestPath, $routeParams)
    {
        if (!empty($requestPath)) {
            return $this->getUrlInstance()->getDirectUrl($requestPath, $routeParams);
        }
        $routeParams['id'] = $location->getId();
        $routeParams['s'] = $location->getUrlKey();

        return $this->getUrlInstance()->getUrl('locator/location/index', $routeParams);
    }

    /**
     * Retrieve request path
     *
     * @param Ak_Locator_Model_Location $location
     *
     * @return bool|string
     */
    protected function _getRequestPath(Ak_Locator_Model_Location $location)
    {
        $idPath = sprintf('location/%d', $location->getEntityId());

        $rewrite = $this->getUrlRewrite();
        $rewrite->setStoreId($location->getStoreId())
            ->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }

        return false;
    }
}
