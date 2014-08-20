<?php

class Ak_Locator_Test_Model_Search_Handler_Area extends EcomDev_PHPUnit_Test_Case
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
        parent::setUp();
        $this->_model = Mage::getModel('ak_locator/search_handler_area');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Search_Handler_Area', $this->_model);
    }

    /**
     * @test
     */
    public function testValidParams()
    {
        $params = array('a'=>'victoria');
        $this->assertTrue($this->_model->isValidParams($params));


        $params = array('country'=>'australia');
        $this->assertTrue($this->_model->isValidParams($params));

        $params = array('a'=>'victoria', 'country'=>'australia');
        $this->assertTrue($this->_model->isValidParams($params));

        $params = array('s'=>'123 test street', 'country'=>'australia');
        $this->assertTrue($this->_model->isValidParams($params));
    }

    /**
     * @test
     */
    public function testInvalidParams()
    {
        $params = array();
        $this->assertFalse($this->_model->isValidParams($params));

        $params = array('s'=>'australia');
        $this->assertFalse($this->_model->isValidParams($params));
    }

}