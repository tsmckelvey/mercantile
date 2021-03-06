<?php
require_once 'Bootstrap.php';

class AuthNetCimCustomerProfileTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $options = array('merchantCustomerId' => 'testCustomerId',
                         'description' => 'test description',
                         'email' => 'user@example.com'
                        );

        $this->cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);
    }
    public function tearDown()
    {
        $this->cusProfile = null;
    }
    public function testCustomerProfileConstructor()
    {
        $options = array('merchantCustomerId' => 'testCustomerId',
                         'description' => 'test description',
                         'email' => 'user@example.com'
                        );

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $this->assertType('Mercantile_Gateways_AuthNetCim_CustomerProfile', $cusProfile);
    }
    public function testCustomerProfileConstructor_invalidDescriptionAndThrowsException()
    {
        $options = array('merchantCustomerId' => str_pad('testCustomerId', 50, 'a'),
                         'description' => 'test description',
                         'email' => 'user@example.com'
                        );

        try {
            $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);
        } catch (Mercantile_Exception $e) {
            return;
        }
        
        $this->fail('Mercantile_Exception expected');
    }
    public function testAddPaymentProfile_validProfile()
    {
        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        $this->cusProfile->addPaymentProfile($payProfile);
    }
    public function testAddPaymentProfile_invalidProfileAndThrowsException()
    {
        
    }
    public function testAddPaymentProfile_duplicateProfileAndFails()
    {
        $ccOptions = array('type' => 'visa',
                           'number' => '3234567890123',
                           'month' => 09,
                           'year' => 2009,
                           'card_code' => 388,
                           'first_name' => 'tom',
                           'last_name' => 'yevlekcm');

        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        $payProfile->setPayment(new Mercantile_Billing_CreditCard($ccOptions));
        
        $options = array('description' => (string)randStr());

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);  

        $this->assertTrue($cusProfile->addPaymentProfile($payProfile));

        // this should return false
        $this->assertFalse($cusProfile->addPaymentProfile($payProfile));
    }
}
