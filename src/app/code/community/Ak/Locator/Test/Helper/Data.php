<?php

class Ak_Locator_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ak_Locator_Helper_Data
     */
    protected $_helper;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_helper = Mage::helper('ak_locator');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Helper_Data', $this->_helper);
    }
}
