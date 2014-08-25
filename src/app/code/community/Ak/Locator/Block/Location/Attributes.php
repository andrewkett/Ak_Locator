<?php
class Ak_Locator_Block_Location_Attributes extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function getLocation()
    {
        if (!$this->getData('location')) {
            $this->setData('location', Mage::registry('location'));
        }
        return $this->getData('location');
    }

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalData(array $excludeAttr = array())
    {
        $data = array();
        $location = $this->getLocation();
        $attributes = $location->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisible() && $attribute->getIsUserDefined() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
            //if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($location);
                           
                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode()
                    );
                }
            }
        }
        return $data;
    }
}
