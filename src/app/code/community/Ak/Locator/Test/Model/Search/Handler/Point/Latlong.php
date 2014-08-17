<?php

class Ak_Locator_Test_Model_Search_Handler_Point_Latlong extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ak_Locator_Model_Search_Handler_Point_Latlong
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('ak_locator/search_handler_point_latlong');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Search_Handler_Point_Latlong', $this->_model);
    }

    /**
     * @test
     */
    public function testValidParams()
    {
        $params = array('lat'=>-37.814207400000, 'long'=>144.964045100000);
        $this->assertTrue($this->_model->isValidParams($params));

        $params = array('lat'=>'-37.814207400000', 'long'=>'144.964045100000');
        $this->assertTrue($this->_model->isValidParams($params));
    }

    /**
     * @test
     */
    public function testInvalidParams()
    {
        $params = array('country'=>'australia');
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('lat'=>-37.814207400000);
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('long'=>144.964045100000);
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('lat'=>'-37.81420740.0000', 'long'=>'144.964045100000');
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('lat'=>'lat', 'long'=>'long');
        $this->assertFalse($this->_model->isValidParams($params));
    }


    /**
     * @test
     */
    public function testValidArgumentSearch()
    {
        $params = array('lat'=>-37.814207400000, 'long'=>144.964045100000);
        $result = $this->_model->search($params);
        $this->assertInstanceOf('Ak_Locator_Model_Resource_Location_Collection', $result);
    }


    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentSearch()
    {
        $params = array('s'=>'3141 australia');
        $this->_model->search($params);
    }

}