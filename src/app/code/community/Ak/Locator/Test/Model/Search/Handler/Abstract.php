<?php

class Ak_Locator_Test_Model_Search_Handler_Abstract extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @var Ak_Locator_Model_Search_Handler_Point_Area
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Ak_Locator_Model_Search_Handler_Abstract');
    }

    /**
     * @test
     */
    public function testDefaultCollectionClass()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Resource_Location_Collection', $this->_model->getCollection());
    }

    /**
     * @test
     */
    public function testDefaultModelClass()
    {
        $this->assertInstanceOf('Mage_Core_Model_Abstract', $this->_model->getModel());
    }

    /**
     * @test
     */
    public function testParseParams()
    {
        $params = array('s'=>'australia');
        $this->assertInternalType('array', $this->_model->parseParams($params));
    }
}