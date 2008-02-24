<?php
/**
 * @copyright Copyright (c) 2007-2008 Thomas McKelvey
 * @license <license> New BSD License
 * @package Mercantile_Gateways
 * @subpackage AuthNetCim
 */
class Mercantile_Gateways_AuthNetCim
{
    const API_XML_SCHEMA       = 'AnetApi/xml/v1/schema/AnetApiSchema.xsd';
    const API_SANDBOX_ENDPOINT = 'https://apitest.authorize.net/xml/v1/request.api';
    const API_LIVE_ENDPOINT    = 'https://api.authorize.net/xml/v1/request.api';

    /**
     * Last HTTP request (string)
     */
    private $_lastRequest = null;

    /**
     * Array of response codes which render the request a failure
     */
    private $_responseCodeErrors = array(
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
        'E00013',
        'E00014',
        'E00015',
        'E00016',
        'E00019',
        'E00027',
        'E00029',
        'E00039',
        'E00040',
        'E00041',
        'E00042',
        'E00043',
        'E00044');

    /**
     * Contains login and transaction key
     */
    protected $_merchantAuth = null;

    public function __construct(array $credentials = null)
    {
        // @TODO: validate data
        $login = $credentials['login'];

        $tranKey = $credentials['tran_key'];

        $doc = new DOMDocument();

        $merchantAuth = $doc->appendChild(new DOMElement('merchantAuthentication'));

        $merchantAuth->appendChild(new DOMElement('name', $login));

        $merchantAuth->appendChild(new DOMElement('transactionKey', $tranKey));

        $this->_merchantAuth = $merchantAuth;
    }

    protected function _parseResponse($response = null)
    {
        // @TODO: validate $response is string
        $responseDoc = new DOMDocument();

        $responseDoc->loadXML($response, LIBXML_NOWARNING);

        $responseDoc->formatOutput = true;

        $responseMessages = $responseDoc->getElementsByTagName('message');

        $messages = array();

        foreach ($responseMessages as $responseMsg) {
            // check error code in messages->resultCode
            $success = (in_array($responseMsg->firstChild->textContent, $this->_responseCodeErrors)) ? false : true;

            $messages[$responseMsg->firstChild->textContent] = $responseMsg->lastChild->textContent;
        }

        $params = array();

        switch ($responseDoc->documentElement->nodeName) {
            case 'ErrorResponse':
                $success = false;
                break;
            case 'createCustomerProfileResponse':
                // if success true, because we CAN have error response codes in this response
                if ($success == true) {
                    $params['customerProfileId'] = $responseDoc->getElementsByTagName('customerProfileId')
                                                               ->item(0)
                                                               ->textContent;
                }
                break;
            default:
                break;
        }

        //return new Mercantile_Gateway_Response($success, $messages, $params);
        return new Mercantile_Gateways_AuthNetCim_Response($success, $messages, $params);
    }

    /**
     * Assembles an XML document from params and posts the data to the server, is wrapped
     * by CIM API methods for convenience
     *
     * Leaves sticky details to wrapper methods
     */
    protected function _request($requestType = null, DOMDocument $payLoad = null, $refId = null)
    {
        // @TODO: implement refId
        // @TODO: validate requestName
        $request = new DOMDocument();

        $request->formatOutput = true;

        $rootElement = $request->appendChild(new DOMElement($requestType));

        $rootElement->setAttribute('xmlns', self::API_XML_SCHEMA);

        $rootElement->appendChild($request->importNode($this->_merchantAuth, $deep = true));

        $rootElement->appendChild($request->importNode($payLoad->documentElement, $deep = true));

        // @TODO: abstract endpoint to dev/prod switch
        $client = new Zend_Http_Client( self::API_SANDBOX_ENDPOINT );

        $client->setRawData( $request->saveXML(), 'application/xml');

        $client->request('POST');

        $this->_lastRequest = $client->getLastRequest();

        $response = $this->_parseResponse($client->getLastResponse()->getRawBody());

        return $response;
    }

    public function getLastRequest()
    {
        return $this->_lastRequest;
    }
    
    public function createCustomerProfile(Mercantile_Gateways_AuthNetCim_CustomerProfile $cusProfile, $refId = null)
    {
        if (!$cusProfile->documentElement->hasChildNodes())
            throw new Mercantile_Exception('Profile must have children');

        $response = $this->_request('createCustomerProfileRequest', $cusProfile, $refId);

        return $response;
    }

    public function getCustomerProfile($cusProfileId = null, $refId = null)
    {
        // @TODO: implement refId
        // @TODO: validation on cusProfileId

        $cusProfileIdDoc = new DOMDocument();

        $cusProfileIdDoc->appendChild(new DOMElement('customerProfileId', $cusProfileId));

        $response = $this->_request('getCustomerProfileRequest', $cusProfileIdDoc, $refId);

        return $response;
    }

    public function updateCustomerProfile($cusProfileId = null, $refId = null)
    {

    }

    public function deleteCustomerProfile($cusProfileId = null, $refId = null)
    {
        // @TODO: implement refId
        // @TODO: validation on cusProfileId

        $cusProfileIdDoc = new DOMDocument();

        $cusProfileIdDoc->appendChild(new DOMElement('customerProfileId', $cusProfileId));

        $response = $this->_request('deleteCustomerProfileRequest', $cusProfileIdDoc, $refId);

        return $response;
    }

    public function createCustomerPaymentProfile($cusProfileId = null, Mercantile_Gateways_AuthNetCim_PaymentProfile $payProfile = null, $refId = null)
    {
        // @TODO: implement validationMode 
        // @TODO: implement refId
        // @TODO: implement validation of cusProfileId

        $response = $this->_request('createCustomerPaymentProfileRequest', $payProfile, $refId);

        return $response;
    }
}
