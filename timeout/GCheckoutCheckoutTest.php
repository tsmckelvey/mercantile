<?php
require_once 'Bootstrap.php';

class GCheckoutCheckoutTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testGCheckoutCheckout()
    {
        $checkout = new Mercantile_Integrations_GCheckout_Checkout();

        $this->assertType('Mercantile_Integrations_GCheckout', $checkout);
    }
}
