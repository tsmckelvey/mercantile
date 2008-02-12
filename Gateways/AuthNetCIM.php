<?php
require_once 'Mercantile/Gateways/AuthNetCIM/CustomerProfile.php';
require_once 'Zend/Http/Client.php';

class Mercantile_Gateways_AuthNetCIM
{
    //const API_XML_SCHEMA       = 'https://api.authorize.net/xml/v1/schema/AnetApiSchema.xsd';
    const API_XML_SCHEMA       = 'AnetApi/xml/v1/schema/AnetApiSchema.xsd';
    const API_SANDBOX_ENDPOINT = 'https://apitest.authorize.net/xml/v1/request.api';
    const API_LIVE_ENDPOINT    = 'https://api.authorize.net/xml/v1/request.api';

    private $_options = array();

    private $_merchantAuthXml = null;

    private $_validationModes = array(
        'testMode',
        'liveMode');

    private $_responseCode = array(
        'I00001' => 'The request was processed successfully.',
        'I00003' => 'The record has already been deleted',
        'E00001' => 'Unexpected system error',
        'E00002' => 'Unsupported content-type',
        'E00003' => 'XML parser error',
        'E00004' => 'Root node method invalid',
        'E00005' => 'Transaction key invalid or not present',
        'E00006' => 'Merchant name invalid or not present',
        'E00007' => 'Name/transaction key invalid',
        'E00008' => 'Gateway not active',
        'E00009' => 'API method unavailable while in test mode',
        'E00010' => 'Invalid permissions for API',
        'E00011' => 'Invalid permissions for API method',
        'E00013' => 'Field value invalid',
        'E00014' => 'Required field not present',
        'E00015' => 'Field length invalid',
        'E00016' => 'Field type invalid',
        'E00019' => 'Customer taxId or driversLicense required',
        'E00027' => 'Transaction was unsuccessful',
        'E00029' => 'Payment information required',
        'E00039' => 'Duplicate record already exists',
        'E00040' => 'Record cannot be found',
        'E00041' => 'One or more fields must contain a value',
        'E00042' => 'Maximum number of payment profiles is {0}',
        'E00043' => 'Maximum number of shipping addresses is {0}',
        'E00044' => 'CIM not enabled');

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

    private $_api_endpoint = null;

    /**
     * Mercantile_AuthorizeNetGatewayCIM::__construct()
     *
     * @param array $gatewayCreds An array expecting keys 'login' and 'tran_key'
     * @throws Mercantile_Exception
     * @return Mercantile_AuthorizeNetGatewayCIM
     */
    public function __construct(array $gatewayCreds = null)
    {
        if (isset($gatewayCreds['login']) && is_string($gatewayCreds['login'])) {
            $this->_options['login']     = $gatewayCreds['login'];
        } else {
            throw new Mercantile_Exception('Login not or not string, is ' . gettype($gatewayCreds['login']));
        }

        if (isset($gatewayCreds['tran_key']) && is_string($gatewayCreds['tran_key'])) {
            $this->_options['tran_key']  = $gatewayCreds['tran_key'];
        } else {
            throw new Mercantile_Exception('Transaction key not set or not string, is ' .
                gettype($gatewayCreds['tran_key']));
        }

        $merchantAuth = '<merchantAuthentication>' .
                        '<name>%s</name>' .
                        '<transactionKey>%s</transactionKey>' .
                        '</merchantAuthentication>';
        
        $this->_merchantAuthXml = sprintf($merchantAuth, $this->_options['login'], $this->_options['tran_key']);

        $this->setTest(false);
    }

    /**
     * Sets test variables or modes
     */
    public function setTest($testing = false)
    {
        if ($testing == true) {
            $this->_api_endpoint = self::API_SANDBOX_ENDPOINT;
        } else {
            $this->_api_endpoint = self::API_LIVE_ENDPOINT;
        }
    }

    /**
     * Parse the XML response to a Response object
     *
     * @param string $response The raw XML response string
     */
    protected function _parseResponse($response = null)
    {
        if (!is_string($response))
            throw new Mercantile_Exception('Response not string, is' . gettype($response));

        // suppress relative xmlns warning ... fucking retarded, really
        $xml = @new SimpleXMLElement($response);

        // yank bool sucess code
        $success = ($xml->messages->resultCode == 'Ok') ? true : false;

        unset($xml->messages->resultCode);

        $params = array(
            'responseName' => $xml->getName(),
            'refId' => (string)$xml->refId,
            'customerProfileId' => (string)$xml->customerProfileId
            );

        switch ($params['responseName']){
            case 'getCustomerProfileResponse':
                
            break;
        }

        $messages = array();

        foreach ($xml->messages->message as $message) {
            $messages[] = (string)$message->text;
        }

        return new Mercantile_Gateway_Response($success, $messages, $params);
    }

    /**
     * Create a new customer profile in the customer information manager,
     * will return Response object with customerProfileId parameter and
     * any additional information.
     *
     * $cusProfile should be given as such:
     *   optional refId Merchant-assigned reference id 
     *   optional merchantCustomerId Merchant-assigned customer id
     *   optional description Description of profile
     *   optional email
     *
     * @param array $customerProfile Profile information
     * @return Mercantile_Gateway_Response
     */
    public function createCustomerProfile($refId = null, array $cusProfile = array())
    {
        $xmlHeader = '<?xml version="1.0" encoding="utf-8"?>';
        
        $createCustomerProfile = '<createCustomerProfileRequest>' . 
                                 $this->_merchantAuthXml .
                                 '</createCustomerProfileRequest>';

        $xml = new SimpleXMLElement($createCustomerProfile, null, null, $xmlHeader);

        // GOTCHA: error if given relative xmlns
        $xml->addAttribute('xmlns', self::API_XML_SCHEMA);

        // refId is a child of createCustomerProfileRequest, NOT profile
        if ($cusProfile['refId']) {
            $xml->{'refId'} = $cusProfile['refId'];
            unset($cusProfile['refId']);
        }

        $xml->addChild('profile');

        foreach ($cusProfile as $childName => $childValue) {
            $xml->profile->{$childName} = $childValue;
        }

        $client = new Zend_Http_Client($this->_api_endpoint);

        $client->setRawData($xml->asXml(), 'application/xml');

        $client->request('POST');

        $this->_last_request = $client->getLastRequest();

        $response = $this->_parseResponse($client->getLastResponse()->getRawBody());

        return $response;
    }

    /**
     * Get customer profile by customerProfileId
     */
    public function getCustomerProfile($cusProfileId = null)
    {
        if (!is_int($cusProfileId))
            throw new Mercantile_Exception('customerProfileId not int, is ' . gettype($cusProfileId));

        $xmlHeader = '<?xml version="1.0" encoding="utf-8"?>';

        $getCustomerProfile = '<getCustomerProfileRequest>' .
                              $this->_merchantAuthXml .
                              '</getCustomerProfileRequest>';

        $xml = new SimpleXMLElement($getCustomerProfile, null, null, $xmlHeader);

        $xml->addAttribute('xmlns', self::API_XML_SCHEMA);

        $xml->{'customerProfileId'} = $cusProfileId;

        $client = new Zend_Http_Client($this->_api_endpoint);

        $client->setRawData($xml->asXml(), 'application/xml');

        $client->request('POST');

        $this->_last_request = $client->getLastRequest();

        echo $client->getLastResponse()->getRawBody();

        $response = $this->_parseResponse($client->getLastResponse()->getRawBody());

        return $response;
    }

    public function createCustomerPaymentProfile()
    {
        
    }
}
