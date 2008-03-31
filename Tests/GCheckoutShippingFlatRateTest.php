<?php
require_once 'Bootstrap.php';

class GCheckoutShippingFlatRateTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testFlatRate()
    {
        $flatRate = new Mercantile_Gateways_GCheckout_Shipping_FlatRate('UPS Next Day Air', 20);

        $areas = array(
            'excluded-areas' => array(
                'state' => 'AK',
                'zip' => 98006,
                'country-area' => 'CONTINENTAL_48',
                'country-code' => 'US',
                'postal' => '9052*'
                ),
            'allowed-areas' => array(
                'state' => 'AK',
                'zip' => '9502*',
                'country-code' => 'US',
                'postal' => '98201',
                'world' => true
                )
            );

        $flatRate->setShippingRestrictions($areas, true);
    }
    public function testFlatRate_setInvalidShippingRestrictionAndThrowsException()
    {
        $shippingMethod = new Mercantile_Gateways_GCheckout_Shipping_FlatRate('UPS Next-Year Air', 1);

        $areas = array(
            'excluded-areas' => array(
                'state' => 'WA',
                ),
            );

        try {
            $shippingMethod->setShippingRestrictions($areas);
        } catch (Exception $e) {
            return;
        }

        $this->fail('Exception expected');
    }
    // @TODO: make test for invalid constructor params
}
