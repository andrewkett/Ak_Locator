<?php

class Ak_Locator_Model_Search_Point_Closest
    extends Ak_Locator_Model_Search_Point_Abstract
{
    const TYPE = 'closest';

    /**
     * Find the single closest location to a point
     *
     * @param array $params
     * @return Ak_Locator_Model_Resource_Location_Collection
     */
    public function search(Array $params)
    {
        $collection = $this->pointToLocations($params['point'], 5000);
        $collection->getSelect()->limit(1);
        $collection->setSearch($this);

        return $collection;
    }

}