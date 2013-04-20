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

class DigiBrews_Locator_Model_Location extends Mage_Core_Model_Abstract
{
   /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */

    const ENTITY                = 'digibrews_locator_location';
    const CACHE_TAG             = 'digibrews_locator_location';

    protected $_cacheTag        = 'digibrews_locator_location';
    //protected $_eventPrefix     = 'digibrews_locator_location';

    protected $_eventPrefix     = 'digibrews_locator';
    protected $_eventObject     = 'digibrews_locator_location';

    protected $_canAffectOptions = false;

    function _construct()
    {
        $this->_init('digibrews_locator/location');
    }



    public function getDirectionsLink()
    {

        if(null == $this->getData('directionsLink')){
            $this->setDirectionsLink();
        }

        return $this->getData('directionsLink');
    }

    public function setDirectionsLink($options = array())
    {
        $params = array();

        $params['daddr'] =$this->getLatitude().','.$this->getLongitude();
        if(isset($options['start'])){

            if($options['start'] instanceof Point){
                $params['saddr']=$options['start']->coords[1].','.$options['start']->coords[0];
            }else{
                $params['saddr']=$options['start'];
            }

        }

        $this->setData('directionsLink', Mage::helper('core/url')->addRequestParam('http://maps.google.com/maps', $params));
    }

    public function getUrl()
    {
        return $this->getUrlModel()->getUrl($this, null);
    }


    /**
     * Get location url model
     *
     * @return DigiBrews_Locator_Model_Location_Url
     */
    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('digibrews_locator/location_url');
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
