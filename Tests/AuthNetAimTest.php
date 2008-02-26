<?php
require 'Bootstrap.php';

class AuthNetAimTest extends PHPUnit_Framework_TestCase
{
    protected $cc = null;

    protected $temp_trans = null;

    public function setUp()
    {
    }
    public function tearDown()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetAim($credentials);

        if (isset($this->temp_trans)) {
            $gateway->void(array('transaction_id' => $this->temp_trans));
        }
    }
    public function testAuthorizeNetGateway()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetAim($credentials);

        $this->assertType('Mercantile_Gateways_AuthNetAim', $gateway);
    }
    /**
     * Test AuthorizeNetGateway AUTH_ONLY 
     *
     * Test authorize of valid CC info
     */
    public function testGateway_AuthorizeValidParams()
    {
        $cc = array(
            'type'      => 'visa',
            'number'    => '4007000000027',
            'month'     => 10,
            'year'      => 2008,
            'card_code' => 388,
            'first_name' => 'Bob',
            'last_name'  => 'Bobsen'
            );

        $cc = new Mercantile_Billing_CreditCard($cc);

        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetAim($credentials);

        $response = $gateway->authorize(1000, $cc, array('x_duplicate_window' => 0));

        $this->assertType('Mercantile_Gateway_Response', $response);
        $this->assertTrue($response->isSuccess());

        $params = $response->getParams();
        $this->temp_trans = $params['transaction_id'];
    }
    public function testGateway_AuthorizeInvalidParams()
    {
        $cc = array(
            'type'      => 'visa',
            'number'    => '4007000000027',
            'month'     => 10,
            'year'      => 2006,
            'card_code' => 388,
            'first_name' => 'Bob',
            'last_name'  => 'Bobsen'
            );

        $cc = new Mercantile_Billing_CreditCard($cc);

        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetAim($credentials);

        $response = $gateway->authorize(1000, $cc, array('x_duplicate_window' => 0));

        $this->assertFalse($response->isSuccess());
    }
    public function testGateway_CaptureValidParams()
    {
        $cc = array(
            'type'      => 'visa',
            'number'    => '4007000000027',
            'month'     => 10,
            'year'      => 2008,
            'card_code' => 388,
            'first_name' => 'Bob',
            'last_name'  => 'Bobsen'
            );

        $cc = new Mercantile_Billing_CreditCard($cc);

        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetAim($credentials);

        $response = $gateway->authorize(1000, $cc, array('x_duplicate_window' => 0));

        $this->assertType('Mercantile_Gateway_Response', $response);
        $this->assertTrue($response->isSuccess());

        $captureOk = $gateway->capture(1000, $response, array('x_duplicate_window' => 0));

        $this->assertTrue($captureOk);

        $params = $response->getParams();
        $this->temp_trans = $params['transaction_id'];
    }
    public function testGateway_CaptureInvalidParams()
    {
        $this->markTestSkipped();
    }
    public function testGateway_Credit()
    {
#@TODO: figure out how to make transactions settled
    }
    public function testGateway_Void()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetAim($credentials);
    }
}
