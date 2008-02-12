<?php
require 'Bootstrap.php';

class AuthNetCIMCustomerProfileTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testAnetCIMCusProfile()
    {
        $strFixture = <<<XML
<profile>
    <merchantCustomerId>mcIdTest</merchantCustomerId>
    <description>test description</description>
    <email>test email</email>
    <customerProfileId>test profile</customerProfileId>
</profile>
XML;

        $cusProfile = new Mercantile_Gateways_AuthNetCIM_CustomerProfile($strFixture);

        $this->assertType('Mercantile_Gateways_AuthNetCIM_CustomerProfile', $cusProfile);
        $this->assertType('string', $cusProfile->getProfile());
        $this->assertFalse(strpos('<?xml version="1.0"?>', $cusProfile->asXml()));

        $this->assertEquals('mcIdTest', $cusProfile->getMerchantCustomerId());
        $this->assertEquals('test description', $cusProfile->getDescription());
        $this->assertEquals('test email', $cusProfile->getEmail());
        $this->assertEquals('test profile', $cusProfile->getProfileId());
    }
}
