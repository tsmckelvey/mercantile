<?php
require 'Bootstrap.php';

class GCheckoutResponseTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testGCheckoutResponse()
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<checkout-redirect xmlns="http://checkout.google.com/schema/2"/>';

        $response = new Mercantile_GCheckout_Response($xml);

        $this->assertType('array', $response->getMessages());
    }
}
