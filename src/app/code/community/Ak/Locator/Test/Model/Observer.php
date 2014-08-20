<?php

class Ak_Locator_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ak_Locator_Model_Observer
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('ak_locator/observer');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Observer', $this->_model);
    }
}
