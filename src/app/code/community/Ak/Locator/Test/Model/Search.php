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


    /**
     * @test
     */
    public function testStringSearchResultInstance()
    {
        $params = array('s' => '3141 australia');
        $this->assertInstanceOf('Ak_Locator_Model_Resource_Location_Collection', $this->_model->search($params));
    }

     /**
      * @test
      */
    public function testLatLongSearchResultInstance()
    {
        $params = array('lat'=>-37.814207400000, 'long'=>144.964045100000);
        $this->assertInstanceOf('Ak_Locator_Model_Resource_Location_Collection', $this->_model->search($params));

    }

    /**
     * @test
     */
    public function testPointSearchResultInstance()
    {
        $params = array('point'=> new Point(-37.814207400000, 144.964045100000));
        $this->assertInstanceOf('Ak_Locator_Model_Resource_Location_Collection', $this->_model->search($params));
    }

    /**
     * @test
     * @loadFixture
     */
    public function testLatLongSearchDistance()
    {
        $params = array(
            'lat'=>-37.814207400000,
            'long'=>144.964045100000,
            'distance' => 5
        );

        $this->assertEquals(2, $this->_model->search($params)->count());
    }

    /**
     * @test
     * @loadFixture testLatLongSearchDistance
     */
    public function testClosest()
    {
        $params = array(
            'point'=> new Point(144.964045100000, -37.814207400000)
        );

        $this->assertEquals(1, $this->_model->search($params)->count());
    }


    /**
     * @test
     * @loadFixture
     */
    public function testLatLongSearchEnabled()
    {
        $params = array(
            'lat'=>-37.814207400000,
            'long'=>144.964045100000,
            'distance' => 20
        );

        $this->assertEquals(2, $this->_model->search($params)->count());
    }
}
