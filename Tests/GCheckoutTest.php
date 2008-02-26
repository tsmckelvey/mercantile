<?php
require_once 'Bootstrap.php';

class GCheckoutTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->id = 647466365709363;
        $this->key = 'scr_6v-GWoH6joavwNoM7Q';

        $this->credentials = array(
            'merchant_id' => $this->id,
            'merchant_key'=> $this->key
            );

        $cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        $item = new Mercantile_Gateways_GCheckout_Item(array(
            'name' => 'iPod',
            'description' => 'Passion of the Jobs',
            'price' => 149.99,
            'quantity' => 1
            ));

        $cart->addItem($item->getItem());

        $this->loadedCart = $cart;
    }
    public function tearDown()
    {
        $this->id = null;
        $this->key = null;
        $this->credentials = null;

        $this->loadedCart = null;
    }
    public function testGCheckout()
    {
        $credentials = array(
            'merchant_id' => $this->id,
            'merchant_key'=> $this->key
            );

        $checkout = new Mercantile_Gateways_GCheckout($credentials);

        $this->assertType('Mercantile_Gateways_GCheckout', $checkout);
    }
    public function testGCheckout_invalidCredentialsAndFails()
    {
        $credentials = array();

        try {
            $checkout = new Mercantile_Gateways_GCheckout($credentials);
        } catch (Mercantile_Exception $e) {
            return;
        }

        $this->fail('Exception was not raised');
    }
    public function testTestCredentials_validCredentialsAndTestCredentials()
    {
        $credentials = array(
            'merchant_id' => $this->id,
            'merchant_key' => $this->key
            );

        $response = Mercantile_Gateways_GCheckout::testCredentials($credentials);

        $this->assertType('Mercantile_Gateways_GCheckout_Response', $response);
        $this->assertTrue($response->isSuccess());

        $params = $response->getParams();

        $this->assertType('string', $params['serial-number']);
    }
    public function testGenerateCheckoutButton_validParamsAndSucceeds()
    {
        $params =  array('merchant_id' => $this->id);

        $buttonUrl = Mercantile_Gateways_GCheckout::generateCheckoutButton($params);

        $this->assertType('string', $buttonUrl);
    }
    public function testGenerateCheckoutButton_merchantIdInvalidAndFails()
    {
        $params = array();

        try {
            $buttonUrl = Mercantile_Gateways_GCheckout::generateCheckoutButton($params);
        } catch (Mercantile_Exception $e) {
            return;
        }

        $this->fail('Exception was not raised');
    }
    public function testSendCheckoutRequest_setShoppingCartAndRequestSucceeds()
    {
        $gcheckout = new Mercantile_Gateways_GCheckout($this->credentials);

        $cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

        $item = new Mercantile_Gateways_GCheckout_Item(array(
            'name' => 'iPod',
            'description' => 'Passion of the Jobs',
            'price' => 149.99,
            'quantity' => 1
            ));

        $cart->addItem($item->getItem());

        $checkout = new Mercantile_Gateways_GCheckout_Checkout();

        $checkout->setShoppingCart($cart->getShoppingCart());

        $response = $gcheckout->sendCheckoutRequest($checkout);

        $this->assertTrue($response->isSuccess());

        $this->assertType('string', $response->getParam('redirect-url'));
    }
    public function testSendCheckoutRequest_setShoppingCartNoCheckoutFlowSupportAndSucceeds()
    {
        $gcheckout = new Mercantile_Gateways_GCheckout($this->credentials);

        $checkout = new Mercantile_Gateways_GCheckout_Checkout();

        $checkout->setShoppingCart($this->loadedCart);

        $response = $gcheckout->sendCheckoutRequest($checkout);

        $this->assertTrue($response->isSuccess());
    }
    public function testSetShoppingCart_sendRequestWithFlowSupportAndShippingParams()
    {
        $gcheckout = new Mercantile_Gateways_GCheckout($this->credentials);

        $options = array('edit-cart-url' => 'http://www.something.com');
            
        $checkout = new Mercantile_Gateways_GCheckout_Checkout($options);

        $checkout->setShoppingCart($this->loadedCart);

        $shipMethod = new Mercantile_Gateways_GCheckout_Shipping_FlatRate('UPS Next Day Air', 20);

        $areas = array(
            'allowed-area' => array(
                'state' => 'AK',
                'zip' => 98006,
                'country-area' => 'CONTINENTAL_48',
                'country-code' => 'US'
                )
            );

        $shipMethod->addShippingRestriction($areas);

        $checkout->setShippingMethod($shipMethod);

        $response = $gcheckout->sendCheckoutRequest($checkout);
    }
}
