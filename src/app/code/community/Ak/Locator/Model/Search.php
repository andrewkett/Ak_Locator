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
 * @category   Ak
 * @package    Ak_Locator
 * @author     Andrew Kett
 */
class Ak_Locator_Model_Search
{
    const XML_SEARCH_OVERRIDES_PATH = "locator_settings/search/overrides_enabled";
    const XML_SEARCH_USEDEFAULT_PATH = "locator_settings/search/use_default_search";
    const XML_SEARCH_USE_CUSTOMER_ADDRESS = "locator_settings/search/use_customer_address";
    const XML_SEARCH_DEFAULT_PARAMS = "locator_settings/search/default_search_params";
    const XML_SEARCH_SHOW_CLOSEST = "locator_settings/search/show_closest_on_noresults";


    protected $handlers = array();
    protected $params = array();
    protected $depth;
    protected $_collection;
    protected $_model;


    public function __construct()
    {
        $this->initHandlers();
    }

    protected function initHandlers()
    {
        //@todo handle weighting handlers
        // Loop through configured handlers and push them into handler stack
        foreach (Mage::app()->getConfig()->getNode('global')->xpath('ak_locator/search_handlers/*') as $handlerConfig) {
            if ($handlerConfig->enabled == '1') {
                /**
                 * @var Ak_Locator_Model_Search_Handler_Abstract $handler
                 */
                $handler = Mage::getModel($handlerConfig->namespace);
                if ($handler) {
                    $this->pushHandler($handler, $handlerConfig->getName());
                }
            }
        }

        return;
    }


    /**
     * Perform search based on params passed
     *
     * @param Array params Array of search params
     *
     * @return Ak_Locator_Model_Resource_Location_Collection
     */
    public function search(array $params = array())
    {
        $params = $this->parseParams($params);

        // Iterate through search handlers and run them if required
        foreach ($this->getHandlers() as $handler) {
            /**
             * @var Ak_Locator_Model_Search_Handler_Abstract $handler
             */
            if ($handler->isValidParams($params)) {
                $params = $handler->parseParams($params);
                $handler->setCollection($this->getCollection())->search($params);
            }
        }

        // If the search is a point search and the site is configured to fallback to the closest match, find it
        if ($this->shouldFindClosest()) {
            $this->getClosest();
        }

        return $this->getCollection();
    }


    /**
     * Do any of the handlers know how to do something with the given params
     *
     * @param array $params
     * @return bool
     */
    public function isValidParams(array $params)
    {
        $params = $this->parseParams($params);

        foreach ($this->getHandlers() as $handler) {
            /**
             * @var Ak_Locator_Model_Search_Handler_Abstract $handler
             */
            if ($handler->isValidParams($params)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Pre pass the search parameters to make any manipulations
     *
     * @param array $params
     * @return mixed
     */
    protected function parseParams(array $params)
    {
        if (empty($params)) {
            $params = $this->getDefaultSearchParams();
        }

        if ($this->shouldParseOverrides()) {
            $params = $this->parseOverrides($params);
        }

        //wrap the parameters in a transport object so they can be manipulated by event listeners
        $transportObject = new Varien_Object();
        $transportObject->setParams($params);

        Mage::dispatchEvent('ak_locator_search_parseparams', array('transport'=>$transportObject));

        return $transportObject->getParams();
    }

    /**
     * Get configured default search parameters
     *
     * @return array
     */
    protected function getDefaultSearchParams()
    {
        //if customer is logged in and they have an address use that
        if ($this->shouldUseCustomerAddress()) {

            $session = Mage::getSingleton('customer/session');

            if ($session->isLoggedIn()
                && $session->getCustomer()->getDefaultBilling()
            ) {
                $addressId = $session->getCustomer()->getDefaultBilling();

                $address = Mage::getModel('customer/address')->load($addressId);
                $street = implode(' ', $address->getStreet());

                $search = $street.', '
                         .$address->getCity().', '
                         .$address->getRegion().', '
                         .$address->getPostcode().', '
                         .$address->getCountry();

                $newParams = array('s'=>$search, 'distance'=>300);
                $searchModel = $this->getSearchClass($newParams);

                //if there are results close to the customer use that
                //otherwise just fallback to default search
                if ($searchModel->search($newParams)->getItems()) {
                    return $newParams;
                }
            }
        }

        //if default params are configured use them
        if ($this->shouldUseDefault()) {
            $params = Mage::getStoreConfig(self::XML_SEARCH_DEFAULT_PARAMS);
            return Mage::helper('ak_locator/search')->parseQueryString($params);
        }
        
        return array();
    }


    /**
     * Find the closest location to the last point search
     */
    protected function getClosest()
    {
        $params = array(
            'point'=> $this->getCollection()->getSearchPoint()
        );
        $this->setCollection($this->getHandler('closest')->search($params));
    }


    /**
     * Should we default to closest location on point searches
     *
     * @return bool
     */
    protected function shouldFindClosest()
    {
        return ($this->getHandler('closest')
            && Mage::getStoreConfig(self::XML_SEARCH_SHOW_CLOSEST)
            && !count($this->getCollection()->getItems())
            && $this->getCollection()->getSearchPoint()
        );
    }


    /**
     * Should we attempt to do a default search
     *
     * @return bool
     */
    protected function shouldUseDefault()
    {
        return (Mage::getStoreConfig(self::XML_SEARCH_USEDEFAULT_PATH));
    }


    /**
     * Should we use the customers shipping address as the default search
     *
     * @return bool
     */
    protected function shouldUseCustomerAddress()
    {
        return (Mage::getStoreConfig(self::XML_SEARCH_USE_CUSTOMER_ADDRESS));
    }


    /**
     * Should parameters be parsed with db overrides
     * @return mixed
     */
    protected function shouldParseOverrides()
    {
        return (Mage::getStoreConfig(self::XML_SEARCH_OVERRIDES_PATH));
    }


    /**
     * parse search params with db overrides
     *
     * @param array $params
     * @return array
     */
    protected function parseOverrides(array $params)
    {
        /**
         * @TODO this needs to be rethought to be more flexible,
         * we need to be able to override a single parameter ignoring the others,
         * e.g replace only the s param in s=melbourne&distance=100
         * also move to parseParams method
         */

        //check db for custom searches matching this one
        $override = Mage::getModel('ak_locator/search_override')->load($params['s']);

        if ($override->getParams()) {
            return Mage::helper('ak_locator/search')->parseQueryString($override->getParams());
        }

        return $params;

    }


    /**
     * Push a handler into the call stack
     *
     * @param Ak_Locator_Model_Search_Handler_Interface $handler
     * @param $code
     * @return $this
     */
    public function pushHandler(Ak_Locator_Model_Search_Handler_Interface $handler, $code)
    {
        $this->handlers[$code] = $handler;

        return $this;
    }


    /**
     * Get all handlers in the call stack
     *
     * @return array
     */
    protected function getHandlers()
    {
        return $this->handlers;
    }


    /**
     * Get as handler from the stack based on its type code
     *
     * @param $code
     * @return Ak_Locator_Model_Search_Handler_Interface
     */
    protected function getHandler($code)
    {
        if (isset($this->handlers[$code])) {
            return $this->handlers[$code];
        }
    }


    /**
     * Get the collection class that will be used to find locations
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->setCollection(
                $this->getModel()
                    ->getCollection()
                    ->addAttributeToFilter('is_enabled', '1')
            );
        }

        return $this->_collection;
    }


    /**
     * Set the collection class that will be used to find locations
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return $this
     */
    public function setCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $this->_collection = $collection;

        return $this;
    }


    /**
     *
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getModel()
    {
        if (!$this->_model) {
            $this->setModel(Mage::getModel('ak_locator/location'));
        }

        return $this->_model;
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $model
     * @return $this
     */
    public function setModel(Mage_Core_Model_Abstract $model)
    {
        $this->_model = $model;

        return $this;
    }
}
