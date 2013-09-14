<?php

class MageBrews_Locator_Model_Search_Point_Closest
    extends MageBrews_Locator_Model_Search_Point_Abstract
{
    /**
     * Find the single closest location to a point
     *
     * @param array $params
     * @return MageBrews_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params)
    {
        $collection = $this->pointToLocations($params['point'], 5000);
        $collection->getSelect()->limit(1);
        return $collection;
    }

}