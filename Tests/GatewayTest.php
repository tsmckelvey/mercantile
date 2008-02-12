<?php
require 'Bootstrap.php';

class GatewayTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testGateway_ValidFactoryCall()
    {
        $gateway = Mercantile_Gateway::factory('Gateways_AuthNetAIM',
                                               array(
                                                   'login'    => 'test',
                                                   'tran_key' => 'test'
                                                   ));

        $this->assertType('Mercantile_Gateways_AuthNetAIM', $gateway);
    }
    public function testGateway_InvalidFactoryCall()
    {
        try {
            $gateway = Mercantile_Gateway::factory('BogusAndInvalidGateway',
                                                   array(
                                                       'login'    => 'test',
                                                       'tran_key' => 'test'
                                                       ));
        } catch (Exception $e) {
            return;
        }

        $this->fail('Expected Mercantile_Exception');
    }
}
