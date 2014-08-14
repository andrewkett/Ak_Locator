<?php

class Ak_Locator_Test_Model_Search extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ak_Locator_Model_Search
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('ak_locator/search');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Search', $this->_model);
    }
}