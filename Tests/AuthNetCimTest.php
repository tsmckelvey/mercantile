<?php
require 'Bootstrap.php';

class AuthNetCimTest extends PHPUnit_Framework_TestCase
{
    protected function _randStr()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $i = 0;
        $str = '';

        while ($i <= 19) {
            $num = rand(1, strlen($chars));
            $tmp = substr($chars, $num, 1);
            $str .= $tmp;
            $i++;
        }

        return $str;
    }
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
        $options = array('description' => (string)$this->_randStr());

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $this->customerProfileId = $response->getCustomerProfileId();

        $this->assertTrue($response->isSuccess());
    }
    /**
     * Create a customer profile, then attempt to create another with the same description
     */
    public function testCreateCustomerProfile_duplicateIdAndFails()
    {
        $options = array('description' => (string)$this->_randStr(),
                         'merchantCustomerId' => (string)$this->_randStr()
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
        $options = array('description' => (string)$this->_randStr());

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
}
