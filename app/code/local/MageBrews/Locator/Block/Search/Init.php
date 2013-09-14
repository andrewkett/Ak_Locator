<?php

class MageBrews_Locator_Block_Search_Init
    extends Mage_Core_Block_Template
{
    public function getLocations()
    {
        return Mage::registry('locator_locations');
    }
}