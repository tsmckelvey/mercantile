<?php
require_once 'Bootstrap.php';

class GCheckoutIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->merchantId = 647466365709363;
        $this->merchantKey = 'scr_6v-GWoH6joavwNoM7Q';
    }
    public function tearDown()
    {
    }
    public function testGCheckoutIntegration()
    {
        $credentials = array(
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey
            );

        $integration = new Mercantile_GCheckoutIntegration($credentials);

        $integration->test(true);

        $this->assertType('Mercantile_GCheckoutIntegration', $integration);
    }
    public function testGCheckoutIntegration_testCredentials()
    {
        $credentials = array(
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey
            );

        $integration = new Mercantile_GCheckoutIntegration($credentials);

        $integration->test(true);

        $credsOk = $integration->testCredentials($credentials);

        $this->assertTrue($credsOk);
    }
    public function testGCheckoutIntegration_testGetCheckoutButton()
    {
        $credentials = array(
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey
            );

        $integration = new Mercantile_GCheckoutIntegration($credentials);

        $integration->test(true);

        $checkoutButton = $integration->getCheckoutButton(array(
            'merchant_id' => $this->merchantId));

        $this->assertType('string', $checkoutButton);
    }
    public function testGCheckoutIntegration_testGetXmlShoppingCart()
    {
        $credentials = array(
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey
            );

        $integration = new Mercantile_GCheckoutIntegration($credentials);

        $integration->test(true);

        $xml = $integration->getXmlShoppingCart();

        $this->assertType('SimpleXMLElement', $xml);
    }
    public function testGCheckoutIntegration_testGetHmacSha1()
    {

        $credentials = array(
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey
            );

        $integration = new Mercantile_GCheckoutIntegration($credentials);

        $integration->test(true);

        $signature = $integration->getHmacSha1($this->merchantKey, $integration->getXmlShoppingCart());

        $this->assertTrue('string', $signature);
    }
}
