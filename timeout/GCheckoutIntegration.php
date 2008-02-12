<?php
/**
 * Mercantile_GCheckoutIntegration is an integration for Google's "Google Checkout"
 * webservice.  As a utility class and client for GCheckout, the class prepares
 * XML strings, authenticates with GCheckout, stores merchant id/key configurations,
 * and guides the Http client through the checkout procedure.
 *
 * @version   $ $
 * @author    Thomas McKelvey <tom.mckelvey@milksites.com>
 * @copyright Copyright (c) 2008, Milksites, LLC., Thomas McKelvey
 * @throws    Mercantile_Exception
 * @license   <>
 * @depends   Zend_Http_Client, SimpleXMLElement
 */

/**
 * @depends Zend_Http_Client
 * @depends SimpleXMLElement
 */
require_once 'Zend/Http/Client.php';

class Mercantile_GCheckoutIntegration
{
    private $_credentials = array(
        'merchant_id' => null,
        'merchant_key' => null
        );

    private $_gcheckout_sandbox_button = 'http://sandbox.google.com/checkout/buttons/checkout.gif';

    private $_gcheckout_live_button = 'http://checkout.google.com/buttons/checkout.gif';

    private $_test = null;

    public function __construct(array $credentials = null)
    {
        if (isset($credentials['merchant_id']) == true) {
            //$this->_credentials['merchant_id'] = 'https://sandbox.google.com/api/checkout/v2/checkout/Merchant/' .
            $this->_credentials['merchant_id'] = $credentials['merchant_id'];
        } else {
            throw new Mercantile_Exception('$credentials[\'merchant_id\'] not set');
        }

        if (isset($credentials['merchant_key']) == true) {
            //$this->_credentials['merchant_key'] = 'https://sandbox.google.com/api/checkout/v2/checkout/Merchant/' .
            $this->_credentials['merchant_key'] = $credentials['merchant_key'];
        } else {
            throw new Mercantile_Exception('$credentials[\'merchant_key\'] not set');
        }

        $this->test(false);
    }

    /**
     * Sets variables for sandbox testing, useful for unit tests
     */
    public function test($testing = null)
    {
        if (is_null($testing) == true or $testing == false) {
            $this->_test = false;
        } else {
            $this->_test = true;
        }
    }
    
    /**
     * Test merchant id and merchant key against Google
     *
     * @param array $credentials Array with 'merchant_id' and 'merchant_key'
     * @return bool
     */
    public function testCredentials(array $credentials = null)
    {
        $str = '<hello xmlns="http://checkout.google.com/schema/2"/>';

        $url = 'https://sandbox.google.com/checkout/api/checkout/v2/request/Merchant/' . $credentials['merchant_id'];

        $client = new Zend_Http_Client($url);

        $client->setAuth($credentials['merchant_id'], $credentials['merchant_key'], Zend_Http_Client::AUTH_BASIC);

        $client->setRawData($str);

        $client->request('POST');

        $response = new SimpleXMLElement($client->getLastResponse()->getBody());
        
        if ($response->getName() == 'bye') {
            return true;
        } else {
            // $this->_lastResponse = whatever

            return false;
        }
    }

    /**
     * Return Google Checkout button path
     *
     * Expects:
     * merchant_id, size = (large|medium|small), 
     * style, variant, loc
     */
    public function getCheckoutButton(array $params = null)
    {
        if (isset($params['merchant_id']) == false)
            $params['merchant_id'] = $this->_credentials['merchant_id'];

        if (!isset($params['size']) or !in_array($params['size'], array('large', 'medium', 'small'))) {
            $params['size'] = 'medium';
        }

        $imageSizes = array(
            'large'  => array(180, 46),
            'medium' => array(168, 44),
            'small'  => array(160, 43));

        $params['w'] = $imageSizes[$params['size']][0];
        $params['h'] = $imageSizes[$params['size']][1];
        unset($params['size']);

        if (!isset($params['style']) or !in_array($params['style'], array('white', 'trans')))
            $params['style'] = 'white';

        if (!isset($params['variant']) or !in_array($params['variant'], array('text', 'disabled')))
            $params['variant'] = 'text';

        if (!isset($params['loc']) or !in_array($params['loc'], array('en_US', 'en_GB')))
            $params['loc'] = 'en_US';

        $imageUrl = ($this->_test) ? $this->_gcheckout_sandbox_button : $this->_gcheckout_live_button;

        foreach ($params as $k => &$v) $v = $k . '=' . $v;
        
        $imageParams = implode('&', $params);

        $imageUrl .= '?' . $imageParams;

        return $imageUrl;
    }

    /**
     * Generate an XML shopping cart
     * @TODO: refactor this, may need to use Adapter pattern in practice
     */
    public function getXmlShoppingCart()
    {
        $xmlStr = '<?xml version="1.0" encoding="utf-8"?>';

        $xmlStr .= '<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2"></checkout-shopping-cart>';

        $xml = new SimpleXMLElement($xmlStr);


        $xml->addChild('shopping-cart');

        $itemsXml = $xml->{'shopping-cart'}->addChild('items');

        $item = $itemsXml->addChild('item');
        $item->addChild('item-name', 'test item');
        $item->addChild('item-description', 'test description');
        $item->addChild('unit-price', 159.99)->addAttribute('currency', 'USD');
        $item->addChild('quantity', 'test quantity');

        $xml->addChild('checkout-flow-support');

        $flatRate = $xml->{'checkout-flow-support'}
            ->addChild('merchant-checkout-flow-support')
            ->addChild('shipping-methods')
            ->addChild('flat-rate-shipping');

        $flatRate->addAttribute('name', 'SuperShip Ground');
        $flatRate->addChild('price', 9.99)->addAttribute('currency', 'USD');

        return $xml;
    }
}
