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


//    /**
//     * @test
//     */
//    public function testSetDirectionLinkNoParams()
//    {
//        $this->_model->setDirectionsLink();
//
//        $link = $this->_model->getDirectionsLink();
//
//        $this->assertInternalType('string', $link);
//        $this->assertStringStartsWith('http://maps.google.com/', $link);
//    }
//
//    /**
//     * @test
//     */
//    public function testSetDirectionsLinkStartPoint()
//    {
//        $params = array(
//            'start' => new Point(-37.814207400000, 144.964045100000)
//        );
//
//        $this->_model->setDirectionsLink($params);
//
//        $link = $this->_model->getDirectionsLink();
////
////        $parts = parse_url($link);
////        echo $parts['query'];
////
////        print_r(explode('&', $parts['query']));
////        print(parse_str($parts['query']));
//
//        //@todo check that saddr is set
//
//        $this->assertInternalType('string', $link);
//        $this->assertStringStartsWith('http://maps.google.com/', $link);
//    }
}
