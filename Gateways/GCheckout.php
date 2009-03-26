<?php
/**
 * Class for using the Google Checkout APIs,
 * generating Buy Now buttons, and testing user credentials
 *
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
class Mercantile_Gateways_GCheckout
{
    const API_SANDBOX_ENDPOINT = 'https://sandbox.google.com/checkout/api/checkout/v2/request/Merchant/';

    const API_LIVE_ENDPOINT    = 'https://checkout.google.com/api/checkout/v2/request/Merchant/';

    const API_XML_SCHEMA = 'http://checkout.google.com/schema/2';

    const API_BUTTON_SANDBOX = 'http://sandbox.google.com/checkout/buttons/checkout.gif';

    const API_BUTTON_LIVE    = 'http://checkout.google.com/buttons/checkout.gif';

    const MERCHANT_ID = 'merchant_id';

    const MERCHANT_KEY = 'merchant_key';

    const XML_VERSION = '1.0';

    const XML_ENCODING = 'utf-8';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_xmlns
     */
    const XMLNS = 'xmlns';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_hello
     */
    const HELLO = 'hello';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_bye
     */
    const BYE = 'bye';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_checkout-redirect
     */
    const CHECKOUT_REDIRECT = 'checkout-redirect';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_request-received
     */
    const REQUEST_RECEIVED = 'request-received';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_diagnosis
     */
    const DIAGNOSIS = 'diagnosis';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_error
     */
    const ERROR = 'error';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_serial-number
     */
    const SERIAL_NUMBER = 'serial-number';

    /**
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_redirect-url
     */
    const REDIRECT_URL = 'redirect-url';

    // checkout button constants
    const SIZE = 'size';

    const LARGE = 'large';

    const MEDIUM = 'medium';

    const SMALL = 'small';

    const STYLE = 'style';

    const WHITE = 'white';

    const TRANS = 'trans';

    const VARIANT = 'variant';

    const TEXT = 'text';

    const DISABLED = 'disabled';

    const LOC = 'loc';

    const EN_US = 'en_US';

    const EN_GB = 'en_GB';

    const W = 'w';

    const H = 'h';

	const NEW_ORDER_NOTIFICATION = 'new-order-notification';

	const RISK_INFORMATION_NOTIFICATION = 'risk-information-notification';

	const ORDER_STATE_CHANGE_NOTIFICATION = 'order-state-change-notification';

	const CHARGE_AMOUNT_NOTIFICATION = 'charge-amount-notification';
	
	const REFUND_AMOUNT_NOTIFICATION = 'refund-amount-notification';

	const CHARGEBACK_AMOUNT_NOTIFICATION = 'chargeback-amount-notification';

	const AUTHORIZATION_AMOUNT_NOTIFICATION = 'authorization-amount-notification';

	protected $_notificationCallbacks = array(
		self::NEW_ORDER_NOTIFICATION,
		self::RISK_INFORMATION_NOTIFICATION,
		self::ORDER_STATE_CHANGE_NOTIFICATION,
		self::CHARGE_AMOUNT_NOTIFICATION,
		self::REFUND_AMOUNT_NOTIFICATION,
		self::CHARGEBACK_AMOUNT_NOTIFICATION,
		self::AUTHORIZATION_AMOUNT_NOTIFICATION
	);

	/**
	 * HTTP client
	 */
	protected $_httpClient = null;

    /**
     * The API endpoint to point to, variable between production and sandbox (see:setTestMode);
     */
    protected $_apiEndpoint = null;

    /**
     * k/v pair of API credentials
     */
    protected $_credentials = array();

    /**
     * Raw body of last HTTP request
     */
    protected $_lastRequest = null;

    /**
     * Construct GCheckout base object
     *
     * Valid $credentials format:
     *      merchant_id:v, merchant_key:v
     *
     * @param array $credentials Associative array of credentials
     */
    public function __construct(array $credentials = null, $httpClient = null)
    {
        if (!isset($credentials[self::MERCHANT_ID])) {
            throw new Mercantile_Exception('"merchant_id" not string, is ' . @gettype($credentials[self::MERCHANT_ID]));
        }

		$this->_credentials[self::MERCHANT_ID] = $credentials[self::MERCHANT_ID];

        if (!isset($credentials[self::MERCHANT_KEY])) {
            throw new Mercantile_Exception('"merchant_key" not string, is ' . @gettype($credentials[self::MERCHANT_KEY]));
        }

		$this->_credentials[self::MERCHANT_KEY] = $credentials[self::MERCHANT_KEY];

		if (!isset($httpClient)) {
			throw new Mercantile_Exception('httpClient not set');
		}

		// @todo: make sure this implements interface
		$this->_httpClient = $httpClient;

        $this->setTestMode(true);
    }

    /**
     * Set GCheckout to Sandbox mode
     */
    public function setTestMode($testMode = true)
    {
        $this->_apiEndpoint = ($testMode) ? self::API_SANDBOX_ENDPOINT : self::API_LIVE_ENDPOINT;
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
        $response = new DomDocument(self::XML_VERSION, self::XML_ENCODING);

        $response->loadXML($rawXml);

        $messages = array();

        $params = array();

        $params[self::SERIAL_NUMBER] = $response->documentElement->getAttribute(self::SERIAL_NUMBER);

        foreach ($response->documentElement->childNodes as $messageNode) {
            $messages[$messageNode->localName] = $messageNode->nodeValue;
        }

        switch ($response->documentElement->tagName) {
            /**
             * Test merchant credentials
             */
            case self::BYE:
                $success = true;
                break;

            /**
             * Checkout API 
             */
            case self::CHECKOUT_REDIRECT:
                $params[self::REDIRECT_URL] = $messages[self::REDIRECT_URL];
                unset($messages[self::REDIRECT_URL]);
                $success = true;
                break;

            /**
             * Order Processing API
             */
            case self::REQUEST_RECEIVED:
                $success = true;
                //$params = 
                break;

            /**
             * Diagnostic 
             */
            case self::DIAGNOSIS:
                $success = true;
                break;

            /**
             * Universal error
             */
            case self::ERROR:
                $success = false;
                break;
        }

        return new Mercantile_Gateways_GCheckout_Response($success, $messages, $params);
    }

    public function getLastRequest()
    {
        return $this->_lastRequest;
    }

    /**
     * Test merchant credentials against Google's 'hello' API
     *
     * @param array $credentials Associative array of merchant_id and merchant_key
     * @return Mercantile_Gateway_Response
     */
    public function testCredentials(array $credentials = null)
    {
		if (null == $credentials) {
			$credentials = $this->_credentials;
		}

        $request = new DomDocument(self::XML_VERSION, self::XML_ENCODING);

        $request->appendChild(new DomElement(self::HELLO));

        $request->documentElement->setAttribute(self::XMLNS, self::API_XML_SCHEMA);

        $url = self::API_SANDBOX_ENDPOINT . $credentials[self::MERCHANT_ID];

		$client = $this->_httpClient->setUri($url);

        $client->setAuth($credentials[self::MERCHANT_ID], $credentials[self::MERCHANT_KEY], Zend_Http_Client::AUTH_BASIC);

        $client->setRawData($request->saveXML());

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
        if (!isset($params[self::MERCHANT_ID]))
            throw new Mercantile_Exception('"merchant_id" ' . @gettype($params[self::MERCHANT_ID]));

        if (!isset($params[self::SIZE]) or !in_array($params[self::SIZE], array(self::LARGE, self::MEDIUM, self::SMALL)))
            $params[self::SIZE] = self::MEDIUM;

        $imageSizes = array(
            self::LARGE => array(180, 46),
            self::MEDIUM => array(168, 44),
            self::SMALL => array(160, 43));

        $params[self::W] = $imageSizes[$params[self::SIZE]][0];
        $params[self::H] = $imageSizes[$params[self::SIZE]][1];

        unset($params[self::SIZE]);

        if (!isset($params[self::STYLE]) or !in_array($params[self::STYLE], array(self::WHITE, self::TRANS)))
            $params[self::STYLE] = self::WHITE;

        if (!isset($params[self::VARIANT]) or !in_array($params[self::VARIANT], array(self::TEXT, self::DISABLED)))
            $params[self::VARIANT] = self::TEXT;

        if (!isset($params[self::LOC]) or !in_array($params[self::LOC], array(self::EN_US, self::EN_GB)))
            $params[self::LOC] = self::EN_US;
        
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
        } else if (get_class($checkout) == 'Mercantile_Gateways_GCheckout_Checkout') {
            // type coerce GCheckout_Checkout::__toString()
            $checkout = (string)$checkout;
        } else {
            throw new Mercantile_Exception('Checkout shopping cart wrong type, is ' . get_class($checkout));
        }

        $endpoint = $this->_apiEndpoint . $this->_credentials[self::MERCHANT_ID];

		$client = $this->_httpClient->setUri($endpoint);

        $client->setAuth($this->_credentials[self::MERCHANT_ID], 
                         $this->_credentials[self::MERCHANT_KEY], 
                         Zend_Http_Client::AUTH_BASIC);

        $client->setRawData($checkout);

        $client->request('POST');

        $this->_lastRequest = $client->getLastRequest();

        $response = self::_parseResponse($client->getLastResponse()->getBody());

        return $response;
    }

	/**
	 * Takes in an XML callback and returns it's appropriate Response object,
	 * handshakes
	 *
	 * @param string $rawXml Response XML string
	 * @return Mercantile_Gateways_GCheckout_Response_*
	 */
	public function parseCallback($rawXml)
	{
		if (!is_string($rawXml)) {
			throw new Mercantile_Exception('Mercantile_Gateways_GCheckout::parseCallback(): ' .
				'argument 1 must be a string, is ' . gettype($rawXml));
		}

		$response = new DomDocument(self::XML_VERSION, self::XML_ENCODING);

		$response->loadXML($rawXml);

		$rootTagName = $response->documentElement->tagName;

		if (!in_array($rootTagName, $this->_notificationCallbacks)) {
			throw new Mercantile_Exception($rootTagName . ' not in $this->_notificationCallbacks');
		}

		switch ($rootTagName) {
			case self::NEW_ORDER_NOTIFICATION:
				$className = 'Mercantile_Gateways_GCheckout_Response_NewOrderNotification';
			break;
			case self::RISK_INFORMATION_NOTIFICATION:

			break;
			case self::ORDER_STATE_CHANGE_NOTIFICATION:

			break;
			case self::CHARGE_AMOUNT_NOTIFICATION:

			break;
			case self::REFUND_AMOUNT_NOTIFICATION:

			break;
			case self::CHARGEBACK_AMOUNT_NOTIFICATION:

			break;
			case self::AUTHORIZATION_AMOUNT_NOTIFICATION:

			break;
			default:
			break;
		}

		//return new $className(
	}
}
