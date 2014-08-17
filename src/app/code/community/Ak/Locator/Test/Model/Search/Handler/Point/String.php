<?php

class Ak_Locator_Test_Model_Search_Handler_Point_String extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ak_Locator_Model_Search_Handler_Point_String
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('ak_locator/search_handler_point_string');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Search_Handler_Point_String', $this->_model);
    }

    /**
     * @test
     */
    public function testValidParams()
    {
        $params = array('s'=>'123 test street');

        $this->assertTrue($this->_model->isValidParams($params));
    }

    /**
     * @test
     */
    public function testInvalidParams()
    {
        $params = array('country'=>'australia');

        $this->assertFalse($this->_model->isValidParams($params));
    }




//    public function testValidSearch()
//    {
//
//        $params = array('s'=>'123 test street');
//
//        $this->assertFalse($this->_model->search($params));
//
//    }
}