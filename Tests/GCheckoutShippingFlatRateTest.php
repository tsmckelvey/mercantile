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
            'excluded-area' => array(
                'state' => 'AK',
                'zip' => 98006,
                'country-area' => 'CONTINENTAL_48',
                'country-code' => 'US',
                'postal' => '9052*'
                // ,world
                )
            );

        $flatRate->addShippingRestriction($areas, true);

        $areas = array(
            'allowed-area' => array(
                'state' => 'AK',
                'zip' => '9502*',
                'country-code' => 'US',
                'postal' => '98201',
                'world' => true
                )
            );

        $flatRate->addShippingRestriction($areas, false);

        echo $flatRate;
    }
    // @TODO: make test for invalid constructor params
}
