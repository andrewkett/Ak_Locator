<?php

class Ak_Locator_Test_Model_Location extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var Ak_Locator_Model_Location
     */
    protected $_model;

    /**
     * Set up test class
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_model = Mage::getModel('ak_locator/location');
    }

    /**
     * @test
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Ak_Locator_Model_Location', $this->_model);
    }

    public function testDefaultDirectionLink()
    {
        $this->assertInternalType('string', $this->_model->getDirectionsLink());
    }


    /**
     * @test
     * @loadFixture default
     */
    public function testSetDirectionLinkNoParams()
    {
        $this->_model->load(1);
        $this->_model->setDirectionsLink();

        $link = $this->_model->getDirectionsLink();

        $parts = parse_url($link);
        parse_str($parts['query'], $queryParams);

        $this->assertInternalType('string', $link);
        $this->assertStringStartsWith('http://maps.google.com/', $link);
        $this->assertEquals('@-37.814207400000,144.964045100000', $queryParams['daddr']);
    }


    /**
     * @test
     * @loadFixture default
     */
    public function testSetDirectionsLinkStartPoint()
    {
        $params = array(
            'start' => new Point(-37.815207400000, 144.924045100000)
        );

        $this->_model->setDirectionsLink($params);

        $link = $this->_model->getDirectionsLink();
        $parts = parse_url($link);
        parse_str($parts['query'], $queryParams);

        $this->assertInternalType('string', $link);
        $this->assertStringStartsWith('http://maps.google.com/', $link);
        $this->assertEquals('144.9240451,-37.8152074', $queryParams['saddr']);

    }
}
