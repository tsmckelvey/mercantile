<?php
require_once 'Mercantile/Gateway/Response.php';
require_once 'Zend/Http/Client.php';

/**
 * Class for using the Google Checkout APIs,
 * generating Buy Now buttons, and testing user credentials
 */
class Mercantile_Integrations_GCheckout
{
    const API_SANDBOX_ENDPOINT = 'https://sandbox.google.com/checkout/api/checkout/v2/request/Merchant/';
    const API_LIVE_ENDPOINT    = 'https://checkout.google.com/api/checkout/v2/request/Merchant/';

    const API_XML_SCHEMA = 'http://checkout.google.com/schema/2';

    const API_BUTTON_SANDBOX = 'http://sandbox.google.com/checkout/buttons/checkout.gif';
    const API_BUTTON_LIVE    = 'http://checkout.google.com/buttons/checkout.gif';
    
    private $_credentials = array();

    /**
     * Construct GCheckout base object
     *
     * Valid $credentials format:
     *      merchant_id:v, merchant_key:v
     *
     * @param array $credentials Associative array of credentials
     */
    public function __construct(array $credentials = null)
    {
        if (!isset($credentials['merchant_id']))
            throw new Mercantile_Exception('"merchant_id" not string, is ' . gettype($credentials['merchant_id']));

        $this->_credentials['merchant_id'] = $credentials['merchant_id'];

        if (!isset($credentials['merchant_key']))
            throw new Mercantile_Exception('"merchant_key" not string, is ' . gettype($credentials['merchant_key']));

        $this->_credentials['merchant_key'] = $credentials['merchant_key'];
    }

    /**
     * Parse a raw XML string to Mercantile_Gateway_Response object
     *
     * Works by detecting the name of the root element
     * and then determining what to make of the response
     * object.  The only way to do it with an API this large.
     *
     * @param string $rawXml Raw XML response string
     * @return Mercantile_Gateway_Response
     */
    static protected function _parseResponse($rawXml = null)
    {
        $xml = new SimpleXMLElement($rawXml);

        $messages = array();

        $params = array();

        $attr = $xml->attributes();

        $params['serial-number'] = (string)$attr['serial-number'];

        foreach ($xml->children() as $message) {
            $messages[$message->getName()] = (string)$message;
        }

        switch ($xml->getName()) {
            /**
             * Test merchant credentials
             */
            case 'bye':
                $success = true;
                break;

            /**
             * Checkout API 
             */
            case 'checkout-redirect':
                $params['redirect-url'] = $messages['redirect-url'];
                unset($messages['redirect-url']);
                $success = true;
                break;

            /**
             * Order Processing API
             */
            case 'request-received':
                $success = true;
                //$params = 
                break;

            /**
             * Diagnostic 
             */
            case 'diagnosis':
                $success = true;
                break;

            /**
             * Universal erro
             */
           case 'error':
                $success = false;
                break;
        }

        return new Mercantile_Gateway_Response($success, $messages, $params);        
    }

    /**
     * Test merchant credentials against Google's 'hello' API
     *
     * @param array $credentials Associative array of merchant_id and merchant_key
     * @return Mercantile_Gateway_Response
     */
    static public function testCredentials(array $credentials = null)
    {
        $xml = sprintf('<hello xmlns="%s"/>', self::API_XML_SCHEMA);

        $xmlObj = new SimpleXMLElement($xml);

        $url = self::API_SANDBOX_ENDPOINT . $credentials['merchant_id'];

        $client = new Zend_Http_Client($url);

        $client->setAuth($credentials['merchant_id'], $credentials['merchant_key'], Zend_Http_Client::AUTH_BASIC);

        $client->setRawData($xmlObj->asXml());

        $client->request('POST');

        $response = self::_parseResponse($client->getLastResponse()->getBody());

        return $response;
    }

    /**
     * Generate a checkout button URL
     *
     * @param array $params Array in form of
     * @param bool $sandbox Whether or not to use the sandbox or live image URL
     * @return string URL w/ query string for GCheckout button
     */
    static public function generateCheckoutButton(array $params = null, $sandbox = true)
    {
        if (!isset($params['merchant_id']))
            throw new Mercantile_Exception('"merchant_id" ' . gettype($params['merchant_id']));

        if (!isset($params['size']) or !in_array($params['size'], array('large', 'medium', 'small')))
            $params['size'] = 'medium';

        $imageSizes = array(
            'large' => array(180, 46),
            'medium'=> array(168, 44),
            'small' => array(160, 43));

        $params['w'] = $imageSizes[$params['size']][0];
        $params['h'] = $imageSizes[$params['size']][1];

        unset($params['size']);

        if (!isset($params['style']) or !in_array($params['style'], array('white', 'trans')))
            $params['style'] = 'white';

        if (!isset($params['variant']) or !in_array($params['variant'], array('text', 'disabled')))
            $params['variant'] = 'text';

        if (!isset($params['loc']) or !in_array($params['loc'], array('en_US', 'en_GB')))
            $params['loc'] = 'en_US';
        
        $imageUrl = ($sandbox) ? self::API_BUTTON_SANDBOX : self::API_BUTTON_LIVE;

        foreach ($params as $k => &$v) $v = $k . '=' . $v;
        
        $imageParams = implode('&', $params);

        $imageUrl .= '?' . $imageParams;

        return $imageUrl;
    }

    /**
     * Sends the checkout-shopping-cart request
     *
     * @param DOMElement|GCheckout_Checkout $checkout The XML container which will be converted to string
     * @return Mercantile_Gateway_Response The response object
     */
    public function sendCheckoutRequest($checkout = null)
    {
        if (get_class($checkout) == 'DOMElement') {
            $checkout = $checkout->saveXML();
        } elseif (get_class($checkout) == 'Mercantile_Integrations_GCheckout_Checkout') {
            $checkout = (string)$checkout;
        } else {
            throw new Mercantile_Exception('Checkout shopping cart wrong type, is ' . get_class($checkout));
        }

        $url = self::API_SANDBOX_ENDPOINT . $this->_credentials['merchant_id'];

        $client = new Zend_Http_Client($url);

        $client->setAuth($this->_credentials['merchant_id'], 
                         $this->_credentials['merchant_key'], 
                         Zend_Http_Client::AUTH_BASIC);

        $client->setRawData($checkout);

        $client->request('POST');

        $response = self::_parseResponse($client->getLastResponse()->getBody());

        return $response;
    }
}
