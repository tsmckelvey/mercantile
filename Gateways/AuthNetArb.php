<?php
/**
 * @copyright Copyright (c) 2007-2008 Thomas McKelvey
 * @license <license> New BSD License
 * @package Mercantile_Gateways
 * @subpackage AuthNetArb
 */
class Mercantile_Gateways_AuthNetArb
{
	const API_XML_SCHEMA = 'AnetApi/xml/v1/schema/AnetApiSchema.xsd';
	const API_SANDBOX_ENDPOINT = 'https://apitest.authorize.net/xml/v1/request.api';
	const API_LIVE_ENDPOINT = 'https://api.authorize.net/xml/v1/request.api';

	const TEST = 'test';
	const LIVE = 'live';

	const ERROR_RESPONSE = 'ErrorResponse';

	const ARB_CREATE_SUBSCRIPTION_REQUEST = 'ARBCreateSubscriptionRequest';
	const ARB_CREATE_SUBSCRIPTION_RESPONSE = 'ARBCreateSubscriptionResponse';

	const ARB_UPDATE_SUBSCRIPTION_REQUEST = 'ARBUpdateSubscriptionRequest';
	const ARB_UPDATE_SUBSCRIPTION_RESPONSE = 'ARBUpdateSubscriptionResponse';

	const ARB_CANCEL_SUBSCRIPTION_REQUEST = 'ARBCancelSubscriptionRequest';
	const ARB_CANCEL_SUBSCRIPTION_RESPONSE = 'ARBCancelSubscriptionRequest';

	protected $_requestTypes = array(
		self::ARB_CREATE_SUBSCRIPTION_REQUEST,
		self::ARB_UPDATE_SUBSCRIPTION_REQUEST,
		self::ARB_CANCEL_SUBSCRIPTION_REQUEST
	);

	private $_lastRequest = null;

	protected $_merchantAuth = null;

	protected $_mode = self::TEST;

	protected $_responseCodeErrors = array(
		'E00001',
		'E00002',
		'E00003',
		'E00004',
		'E00005',
		'E00006',
		'E00007',
		'E00008',
		'E00009',
		'E00010',
		'E00011',
		'E00012',
		'E00013',
		'E00014',
		'E00015',
		'E00016',
		'E00017',
		'E00018',
		'E00019',
		'E00020',
		'E00021',
		'E00022',
		'E00023',
		'E00024',
		'E00025',
		'E00026',
		'E00027',
		'E00028',
		'E00029',
		'E00030',
		'E00031',
		'E00032',
		'E00033',
		'E00034',
		'E00035',
		'E00036',
		'E00037',
		'E00038',
	);

	public function __construct(array $credentials = null)
	{
		$login = $credentials['login'];
		$tranKey = $credentials['tran_key'];

		$doc = new DOMDocument();

		$merchantAuth = $doc->appendChild(new DOMElement('merchantAuthentication'));
		$merchantAuth->appendChild(new DOMElement('name', $login));
		$merchantAuth->appendChild(new DOMElement('transactionKey', $tranKey));

		$this->_merchantAuth = $merchantAuth;
	}

	public function setTestMode($testMode = true)
	{
		if (true === $testMode) {
			$this->_mode = self::TEST;
		} else {
			$this->_mode = self::LIVE;
		}

		return $this;
	}

	protected function _parseResponse($response = null)
	{
		$responseDoc = new DomDocument();
		$responseDoc->loadXML($response, LIBXML_NOWARNING);
		$responseDoc->formatOutput = true;

		$responseMessages = $responseDoc->getElementsByTagName('message');

		$messages = array();

		foreach ($responseMessages as $message) {
			$success = (in_array($message->firstChild->textContent, $this->_responseCodeErrors)) ? false : true;

			$messages[$message->firstChild->textContent] = $message->lastChild->textContent;
		}

		$params = array();

		switch($responseDoc->documentElement->nodeName) {
			case self::ERROR_RESPONSE:
				$success = false;
				break;
			case self::ARB_CREATE_SUBSCRIPTION_RESPONSE:

				break;
			case self::ARB_UPDATE_SUBSCRIPTION_RESPONSE:
				break;
			case self::ARB_CANCEL_SUBSCRIPTION_RESPONSE:
				break;
			default:
				break;
		}

		return new Mercantile_Gateways_AuthNetArb_Response($success, $messages, $params);
	}

	protected function _request($requestType = null, DOMDocument $payLoad = null, $refId = null)
	{
		$request = new DOMDocument('1.0', 'utf-8');
		$request->formatOutput = true;

		$rootElement = $request->appendChild(new DOMElement($requestType));
		$rootElement->setAttribute('xmlns', self::API_XML_SCHEMA);
		$rootElement->appendChild($request->importNode($this->_merchantAuth, $deep = true));
		// TODO validate refId
		if (isset($refId)) $payLoad->documentElement->appendChild(new DomElement('refId'))->nodeValue = $refId;
		$rootElement->appendChild($request->importNode($payLoad->documentElement, $deep = true));

		$client = new Zend_Http_Client( ($this->_mode === self::TEST) ? self::API_SANDBOX_ENDPOINT :
																		self::API_LIVE_ENDPOINT );
		$client->setRawData( $request->saveXML(), 'application/xml' );
		$client->request('POST');

		$this->_lastRequest = $client->getLastRequest();

		echo $request->saveXML();
		$response = $this->_parseResponse($client->getLastResponse()->getRawBody());

		return $response;
	}

	public function getLastRequest()
	{
		return $this->_lastRequest;
	}

	public function createSubscription(Mercantile_Gateways_AuthNetArb_Subscription $subscription, $refId = null)
	{
		$response = $this->_request(self::ARB_CREATE_SUBSCRIPTION_REQUEST, $subscription, $refId);

		return $response;
	}

	protected function _appendSubscriptionId(Mercantile_Gateways_AuthNetArb_Subscription $subscription, $subscriptionId)
	{
		// TODO validate id
		$subscription->documentElement->appendChild(new DomElement('subscriptionId'))->nodeValue = $subscriptionId;

		return $subscription;
	}

	public function updateSubscription(Mercantile_Gateways_AuthNetArb_Subscription $subscription, $subscriptionId, $refId = null)
	{
		$subscription = $this->_appendSubscriptionId($subscription, $subscriptionId);

		$response = $this->_request(self::ARB_UPDATE_SUBSCRIPTION_REQUEST, $subscription, $refId);

		return $response;
	}

	public function cancelSubscription($subscriptionId, $refId = null)
	{
		$subscription = $this->_appendSubscriptionId($subscription, $subscriptionId);

		$response = $this->_request(self::ARB_CANCEL_SUBSCRIPTION_REQUEST, $refId);

		return $response;
	}
}
