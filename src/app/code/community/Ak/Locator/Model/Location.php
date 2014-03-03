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
 * Class Ak_Locator_Model_Location
 *
 * @method Ak_Locator_Model_Resource_Location getResource()
 *
 */
class Ak_Locator_Model_Location extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */

    const ENTITY                = 'ak_locator_location';
    const CACHE_TAG             = 'ak_locator_location';

    protected $_cacheTag        = 'ak_locator_location';
    //protected $_eventPrefix     = 'ak_locator_location';

    protected $_eventPrefix     = 'ak_locator';
    protected $_eventObject     = 'ak_locator_location';

    protected $_canAffectOptions = false;

    public function _construct()
    {
        $this->_init('ak_locator/location');
    }


    /**
     * If directions_link isn't already set in data first generate it
     *
     * @return string
     */
    public function getDirectionsLink()
    {
        if (null == $this->getData('directions_link')) {
            $this->setDirectionsLink();
        }

        return $this->getData('directions_link');
    }


    /**
     * Create Google maps direction link and set in data
     *
     * @param array $options
     */
    public function setDirectionsLink($options = array())
    {
        $params = array(
            'daddr' => $this->getLatitude().','.$this->getLongitude()
        );

        if (isset($options['start'])) {
            if ($options['start'] instanceof Point) {
                $params['saddr']=$options['start']->coords[1].','.$options['start']->coords[0];
            } else {
                $params['saddr']=$options['start'];
            }
        }

        $this->setData('directions_link', Mage::helper('core/url')->addRequestParam('http://maps.google.com/maps', $params));
    }


    /**
     * Get url to location
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getUrlModel()->getUrl($this, null);
    }


    /**
     * Get location url model
     *
     * @return Ak_Locator_Model_Location_Url
     */
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('ak_locator/location_url');
        }
        return $this->_urlModel;
    }


    /**
     * Formats URL key
     *
     * @param $str URL
     * @return string
     */
    public function formatUrlKey($str)
    {
        return $this->getUrlModel()->formatUrlKey($str);
    }
}
