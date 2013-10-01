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

/**
 * @category   MageBrews
 * @package    MageBrews_Locator
 * @author     Andrew Kett
 */
class MageBrews_Locator_Model_Search
    extends MageBrews_Locator_Model_Search_Abstract
{
    const XML_SEARCH_OVERRIDES_PATH = "locator_settings/search/overrides_enabled";
    const XML_SEARCH_USEDEFAULT_PATH = "locator_settings/search/use_default_search";
    const XML_SEARCH_USE_CUSTOMER_ADDRESS = "locator_settings/search/use_customer_address";
    const XML_SEARCH_DEFAULT_PARAMS = "locator_settings/search/default_search_params";
    const XML_SEARCH_SHOW_CLOSEST = "locator_settings/search/show_closest_on_noresults";

    protected $params = array();
    protected $depth;

    /**
     * Perform search based on params passed
     *
     * @param Array params Array of search params
     *
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params = array())
    {
        $this->params = $this->parseParams($params);
        $this->depth = 0;

        /** @var MageBrews_Locator_Model_Resource_Location_Collection $collection */
        $collection = $this->getSearchClass()->search($this->params);

        if (Mage::getStoreConfig(self::XML_SEARCH_SHOW_CLOSEST)
            && !count($collection->getItems())
            && $collection->getSearchPoint()
        ) {

            $params = array(
                'point'=> $collection->getSearchPoint()
            );

            $collection = Mage::getModel('magebrews_locator/search_point_closest')->search($params);
        }

        return $collection;
    }


    /**
     * Find appropriate search class based on params passed
     *
     * @return MageBrews_Locator_Model_Search_Abstract
     */
    protected function getSearchClass(Array $params = null)
    {
        if (is_null($params)) {
            $params = $this->params;
        }

        if ($this->isStringSearch($params)) {

            /**
             * @TODO this needs to be rethought to be more flexible,
             * we need to be able to override a single parameter ignoring the others,
             * e.g replace only the s param in s=melbourne&distance=100
             * also move to parseParams method
             */
            if (Mage::getStoreConfig(self::XML_SEARCH_OVERRIDES_PATH)) {

                //check db for custom searches matching this one
                $override = Mage::getModel('magebrews_locator/search_override')->load($params['s']);

                if ($override->getParams() && $this->depth < 1) {
                    $this->depth++;
                    $this->params = Mage::helper('magebrews_locator/search')->parseQueryString($override->getParams());
                    return $this->getSearchClass($this->params);
                }
            }

            return Mage::getModel('magebrews_locator/search_point_string');

        } else if ($this->isLatLongSearch($params)) {

            return Mage::getModel('magebrews_locator/search_point_latlong');

        } else if ($this->isAreaSearch($params)) {

            return Mage::getModel('magebrews_locator/search_area');

        }
    }


    /**
     * Parse the current parameters and do any necessary manipulations
     *
     * @param array $params
     * @return array
     */
    protected function parseParams(Array $params)
    {
        if (empty($params)) {
            $params = $this->getDefaultSearchParams();
        }

        //wrap the parameters in a transport object so they can be manipulated by event listeners
        $_transportObject = new Varien_Object();
        $_transportObject->setParams($params);

        Mage::dispatchEvent('magebrews_locator_search_parseparams', array('transport'=>$_transportObject));

        return $_transportObject->getParams();
    }

    /**
     * Get configured default search parameters
     *
     * @return array
     */
    protected function getDefaultSearchParams()
    {
        //if customer is logged in and they have an address use that
        if (Mage::getStoreConfig(self::XML_SEARCH_USE_CUSTOMER_ADDRESS)) {

            $session = Mage::getSingleton('customer/session');

            if ($session->isLoggedIn()
                && $session->getCustomer()->getDefaultBilling()
            ) {
                $addressId = $session->getCustomer()->getDefaultBilling();

                $address = Mage::getModel('customer/address')->load($addressId);
                $street = $address->getStreet();

                $search = @$street[0].' '.@$street[1].', '.$address->getCity().', '.$address->getRegion().', '.$address->getPostcode().', '.$address->getCountry();

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
        if (Mage::getStoreConfig(self::XML_SEARCH_USEDEFAULT_PATH)) {
            $params = Mage::getStoreConfig(self::XML_SEARCH_DEFAULT_PARAMS);
            return Mage::helper('magebrews_locator/search')->parseQueryString($params);
        }
    }


    /**
     * Is the current search a string search?
     *
     * @param array $params
     * @return bool
     */
    protected function isStringSearch(Array $params)
    {
        return isset($params['s']);
    }

    /**
     * Is the current search a lat/long search?
     *
     * @param array $params
     * @return bool
     */
    protected function isLatLongSearch(Array $params)
    {
        return isset($params['lat']) && isset($params['long']);
    }

    /**
     * Is the current search an area search?
     *
     * @param array $params
     * @return bool
     */
    protected function isAreaSearch(Array $params)
    {
        return isset($params['a'])
        || isset($params['country'])
        || isset($params['postcode']);
    }

    /**
     * Check if search params will return a valid search class
     *
     * @param array $params
     * @return bool
     */
    public function isValidParams(array $params)
    {
        $params = $this->parseParams($params);
        return !is_null($this->getSearchClass($params));
    }
}