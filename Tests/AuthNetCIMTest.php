<?php
require 'Bootstrap.php';

class AuthNetCIMTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testGateway_validParams()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetCIM($credentials);

        $this->assertType('Mercantile_Gateways_AuthNetCIM', $gateway);
    }
    public function testGateway_parseResponse()
    {

    }
    public function xtestGateway_createCustomerProfile()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetCIM($credentials);

        $gateway->setTest(true);

        $this->assertType('Mercantile_Gateways_AuthNetCIM', $gateway);

        $cusProfile = array(
            'refId' => 8293,
            'merchantCustomerId' => 'testId'
            );

        $response = $gateway->createCustomerProfile(null, $cusProfile);
        
        $this->assertType('Mercantile_Gateway_Response', $response);

        print_r($response->getMessages());
        print_r($response->getParams());

        echo $gateway->getLastRequest();
    }
    public function testGateway_getCustomerProfile()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetCIM($credentials);

        $gateway->setTest(true);

        $response = $gateway->getCustomerProfile(1686);

        $this->assertType('Mercantile_Gateway_Response', $response);
        $this->assertTrue($response->isSuccess());

        print_r($response->getParams());
        print_r($response->getMessages());
    }
    public function testGateway_createCustomerPaymentProfile()
    {
        
    }
}
