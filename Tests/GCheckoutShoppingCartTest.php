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

        $this->item = new Mercantile_Integrations_GCheckout_Item($itemInfo);
    }
    public function tearDown()
    {
        $this->item = null;
    }
    public function testGCheckoutCart()
    {
        $cart = new Mercantile_Integrations_GCheckout_ShoppingCart();

        $this->assertType('Mercantile_Integrations_GCheckout_ShoppingCart', $cart);

        $this->assertType('string', (string)$cart);
    }
    public function testGCheckoutShoppingCart_addItem()
    {
        $cart = new Mercantile_Integrations_GCheckout_ShoppingCart();

        $shoppingCart = $cart->getShoppingCart();

        $this->assertType('DOMElement', $shoppingCart);

        $this->assertTrue( $cart->addItem($this->item->getItem()) );
    }
    public function testGCheckoutShoppingCart_addItemWrongTag()
    {
        $cart = new Mercantile_Integrations_GCheckout_ShoppingCart();

        try {
            $cart->addItem(new DOMElement('not-an-item'));
        } catch (Exception $e) {
            return;
        }

        $this->fail('Exception expected!');
    }
}
