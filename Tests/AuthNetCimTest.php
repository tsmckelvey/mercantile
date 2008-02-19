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
    public function testConstructor_validCredentials()
    {
        $credentials = array(
            'login'     => '8954jM4pCcWZ',
            'tran_key'  => '7828uzaA6j83MHQr'
            );

        $gateway = new Mercantile_Gateways_AuthNetCim($credentials);
    }
    public function testCreateCustomerProfile()
    {
        $options = array('description' => (string)$this->_randStr());

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $params = $response->getParams();
        
        $this->customerProfileId = $params['customerProfileId'];

        $this->assertTrue($response->isSuccess());
    }
    public function testCreateCustomerProfile_duplicateIdAndFails()
    {
        $options = array('description' => (string)$this->_randStr(),
                         'merchantCustomerId' => (string)$this->_randStr()
                        );

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $params = $response->getParams();

        $this->customerProfileId = $params['customerProfileId'];

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $this->assertFalse($response->isSuccess());
    }
    public function testDeleteCustomerProfile_createsAndDeletesCustomerProfile()
    {
        $options = array('description' => (string)$this->_randStr());

        $cusProfile = new Mercantile_Gateways_AuthNetCim_CustomerProfile($options);

        $response = $this->gateway->createCustomerProfile($cusProfile);

        $params = $response->getParams();
        
        $response = $this->gateway->deleteCustomerProfile($params['customerProfileId']);

        $this->assertTrue($response->isSuccess());
    }
    public function testGateway_getCustomerProfile()
    {
    }
}
