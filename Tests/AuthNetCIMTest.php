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
    public function testGateway_validCredentials()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetCIM($credentials);
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
    public function testGateway_createCustomerProfile()
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
    }
    public function testGateway_createCustomerPaymentProfile()
    {
        
    }
}
