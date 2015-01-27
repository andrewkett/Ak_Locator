<?php

class Ak_Locator_Test_Model_Search_Handler_Point_Closest extends EcomDev_PHPUnit_Test_Case
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
        $this->_model = Mage::getModel('ak_locator/search_handler_point_closest');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Search_Handler_Point_Closest', $this->_model);
    }

    /**
     * @test
     */
    public function testValidParams()
    {
        $params = array('point'=> new Point(-37.814207400000, 144.964045100000));
        $this->assertTrue($this->_model->isValidParams($params));
    }

    /**
     * @test
     */
    public function testInvalidParams()
    {
        $params = array('something' => 'australia');
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('point' => 'here');
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('point' => 144.964045100000);
        $this->assertFalse($this->_model->isValidParams($params));
    }


    /**
     * @test
     */
    public function testValidArgumentSearch()
    {
        $params = array('point'=> new Point(-37.814207400000, 144.964045100000));
        $result = $this->_model->search($params);
        $this->assertInstanceOf('Ak_Locator_Model_Resource_Location_Collection', $result);
    }


    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentSearch()
    {
        $params = array('test'=>'test');
        $this->_model->search($params);

        $params = array('point'=>'here');
        $this->_model->search($params);

        $params = array('point'=>1);
        $this->_model->search($params);

        $params = array('point'=> new StdClass());
        $this->_model->search($params);
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
