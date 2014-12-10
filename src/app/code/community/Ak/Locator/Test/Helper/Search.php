<?php

class Ak_Locator_Test_Helper_Search extends EcomDev_PHPUnit_Test_Case
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
        $this->_helper = Mage::helper('ak_locator/search');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Helper_Search', $this->_helper);
    }


    /**
     * @test
     */
    public function testParseQuery()
    {

        $params = $this->_helper->parseQueryString('lat=-37.814207400000&long=144.964045100000');

        $this->assertInternalType('array', $params);

        array('lat'=>'-37.814207400000', 'long'=>'144.964045100000');
    }
}
