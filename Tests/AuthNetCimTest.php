<?php
require 'Bootstrap.php';

class AuthNetCimTest extends PHPUnit_Framework_TestCase
{
    public function setup() 
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $this->gateway = new Mercantile_Gateways_AuthNetCim($credentials);
    }
    public function tearDown() 
    {
        if (isset($this->customerProfileId)) {
            $response = $this->gateway->deleteCustomerProfile($this->customerProfileId);
            if (!$response->isSuccess())
                throw new Mercantile_Exception('Profile not deleted, check for problem in deleteCustomerProfile');
        }

        $this->gateway = null;
    }
    /**
     * Instantiate an AuthNetCim class with credentials
     */
    public function testConstructor_validCredentials()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetCim($credentials);
    }
    /**
     * Create a customer profile
     */
    public function testCreateCustomerProfile()
    {
        $options = array('description' => (string)randStr());

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $this->customerProfileId = $response->getCustomerProfileId();

        $this->assertTrue($response->isSuccess());
    }
    /**
     * Create a customer profile with multiple payment profiles (creditCard)
     */
    public function testCreateCustomerProfile_multiplePaymentProfilesAndSucceeds()
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

        $cusProfile->addPaymentProfile($payProfile);

        $ccOptions = array('type' => 'visa',
                           'number' => '1234567890123',
                           'month' => 09,
                           'year' => 2009,
                           'card_code' => 388,
                           'first_name' => 'tom',
                           'last_name' => 'yevlekcm');

        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        $payProfile->setPayment(new Mercantile_Billing_CreditCard($ccOptions));

        $cusProfile->addPaymentProfile($payProfile);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $this->customerProfileId = $response->getCustomerProfileId();

        $this->assertTrue($response->isSuccess());
    }
    /**
     * Create a customer profile, then attempt to create another with the same description
     */
    public function testCreateCustomerProfile_duplicateIdAndFails()
    {
        $options = array('description' => (string)randStr(),
                         'merchantCustomerId' => (string)randStr()
                        );

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $this->customerProfileId = $response->getCustomerProfileId();

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $this->assertFalse($response->isSuccess());
    }
    /**
     * Create a customer profile and delete the same profile
     */
    public function testDeleteCustomerProfile_createsAndDeletesCustomerProfile()
    {
        $options = array('description' => (string)randStr());

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $response = $this->gateway->deleteCustomerProfile($response->getCustomerProfileId());

        $this->assertTrue($response->isSuccess());
    }
    /**
     * Get a customer profile
     */
    public function testGetCustomerProfile()
    {
        self::testCreateCustomerProfile();

        $response = $this->gateway->getCustomerProfile($this->customerProfileId);

        $this->assertTrue($response->isSuccess());
    }
    public function testGetCustomerProfile_invalidProfileId()
    {
        $response = $this->gateway->getCustomerProfile('invalidCustomerProfileId');

        $this->assertFalse($response->isSuccess());

        $response = $this->gateway->getCustomerProfile(888192931);

        $this->assertFalse($response->isSuccess());
    }
}
