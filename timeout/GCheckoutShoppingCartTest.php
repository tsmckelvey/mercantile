<?php
require 'Bootstrap.php';

class GCheckoutShoppingCartTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testShoppingCart()
    {
        $cart = new Mercantile_GCheckout_ShoppingCart();

        $xmlCart = $cart->asXml();

        $this->assertType('Mercantile_GCheckout_ShoppingCart', $cart);
        $this->assertType('string', $xmlCart);
    }
    public function testShoppingCart_addItem()
    {
        $cart = new Mercantile_GCheckout_ShoppingCart();

        $newItem = array(
            'name' => 'test product name',
            'description' => 'test description',
            'unit_price' => 99.99,
            'quantity' => 1);

        $item = $cart->addItem($newItem);

        $this->assertType('SimpleXMLElement', $item);
    }
}
