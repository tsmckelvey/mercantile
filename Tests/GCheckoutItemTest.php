<?php
require_once 'Bootstrap.php';

class GCheckoutItemTest extends PHPUnit_Framework_TestCase
{
    public $itemInfo = array(
        'name' => 'Apple iPod 20GB',
        'description' => 'Steve Jobs\' tears',
        'price' => 89.99,
        'quantity' => 1
        );

    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testGCheckoutItem()
    {
        $item = new Mercantile_Gateways_GCheckout_Item($this->itemInfo);

        $this->assertType('Mercantile_Gateways_GCheckout_Item', $item);
    }
    public function testGCheckoutItem_getItem()
    {
        $item = new Mercantile_Gateways_GCheckout_Item($this->itemInfo);

        $newItem = $item->getItem();

        $this->assertType('DOMNode', $newItem);
    }
}
