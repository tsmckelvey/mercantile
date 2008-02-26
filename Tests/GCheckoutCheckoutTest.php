<?php
require 'Bootstrap.php';

class GCheckoutCheckoutTest extends PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $itemInfo = array(
            'name' => 'Apple iPod 20GB',
            'description' => 'Steve Jobs\' tears',
            'price' => 89.99,
            'quantity' => 1
            );

        $this->item = new Mercantile_Gateways_GCheckout_Item($itemInfo);
    
        $this->cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        $this->cart->addItem($this->item->getItem());
    }

    public function tearDown()
    {
        unset($this->cart);
        unset($this->item);
    }

    public function testGCheckoutCheckout()
    {
        $checkoutShoppingCart = new Mercantile_Gateways_GCheckout_Checkout();

        $this->assertType('Mercantile_Gateways_GCheckout_Checkout', $checkoutShoppingCart);

        $cart = $checkoutShoppingCart;

        $this->assertType('Mercantile_Gateways_GCheckout_Checkout', $cart);
        $this->assertType('string', (string)$cart);
    }

    public function testGCheckoutCheckout_setShoppingCart()
    {
        $checkout = new Mercantile_Gateways_GCheckout_Checkout();

        $this->assertTrue($checkout->setShoppingCart($this->cart->getShoppingCart()));
    }

    public function testGCheckoutCheckout_setShoppingCartWrongTag()
    {
        $checkout = new Mercantile_Gateways_GCheckout_Checkout();

        try {
            $checkout->setShoppingCart(new DOMElement('not-a-cart'));
        } catch (Exception $e) {
            return;
        }

        $this->fail('Exception expected!');
    }
    
    public function testGCheckoutCheckout_setShoppingCartDuplicateFail()
    {
        $checkout = new Mercantile_Gateways_GCheckout_Checkout();

        $cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        $this->assertTrue($checkout->setShoppingCart($this->cart->getShoppingCart()));
    }
    
    public function testGCheckoutCheckout_optionalParams()
    {
        $options = array(
            'edit-cart-url' => 'www.something.com',
            'continue-shopping-url' => 'www.something.com',
            'request-buyer-phone-number' => true,
            );

        $checkout = new Mercantile_Gateways_GCheckout_Checkout($options);

        $this->markTestSkipped();
    }
    public function testGCheckoutCheckout_optionalParamsInvalid()
    {
        $this->markTestSkipped();
    }
    public function testGCheckoutCheckout_setShippingMethod()
    {
        $checkout = new Mercantile_Gateways_GCheckout_Checkout();

        $shipMethod = new Mercantile_Gateways_GCheckout_Shipping_FlatRate('UPS Next Day Air', 20);

        $areas = array(
            'excluded-area' => array(
                'state' => 'AK',
                'zip' => 98006,
                'country-area' => 'CONTINENTAL_48',
                'country-code' => 'US'
                // ,postal
                // ,world
                )
            );

        $shipMethod->addShippingRestriction($areas);

        $checkout->setShippingMethod($shipMethod);

        echo $checkout;
        $this->assertTrue($checkout->setShippingMethod($shipMethod));
    }
}
