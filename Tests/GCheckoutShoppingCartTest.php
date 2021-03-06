<?php
require_once 'Bootstrap.php';

class GCheckoutShoppingCartTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // item fixture
        $itemInfo = array(
            'name' => 'Apple iPod 20GB',
            'description' => 'Steve Jobs\' tears',
            'price' => 89.99,
            'quantity' => 1
            );

        $this->item = new Mercantile_Gateways_GCheckout_Item($itemInfo);
    }
    public function tearDown()
    {
        $this->item = null;
    }
    public function testGCheckoutCart()
    {
        $cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        $this->assertType('Mercantile_Gateways_GCheckout_ShoppingCart', $cart);

        $this->assertType('string', (string)$cart);
    }
    public function testGCheckoutShoppingCart_addItem()
    {
        $cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        $this->assertType('DomDocument', $cart);

        $this->assertTrue($cart->addItem($this->item));
    }
    public function testGCheckoutShoppingCart_addItemWrongTag()
    {
        $cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        try {
            $cart->addItem(new DomElement('not-an-item'));
        } catch (Exception $e) {
            return;
        }

        $this->fail('Exception expected!');
    }
}
