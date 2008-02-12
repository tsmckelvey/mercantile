<?php
require_once 'Mercantile/Gateway.php';
require_once 'Mercantile/Gateway/Interface.php';
require_once 'Zend/Http/Client.php';

/**
 * Mercantile_Gateways_AuthNetAIM
 *
 * will Supports AIM, ARB, and CIM
 */
class Mercantile_Gateways_AuthNetAIM implements Mercantile_Gateway_Interface
{
    const API_VERSION      = 3.1;
    const API_LIVE_ENDPOINT    = 'https://secure.authorize.net/gateway/transact.dll';
    const API_SANDBOX_ENDPOINT = 'https://test.authorize.net/gateway/transact.dll';

    /**
     * Types of API requests 
     */
    const API_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const API_AUTH_ONLY    = 'AUTH_ONLY';
    const API_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const API_CREDIT       = 'CREDIT';
    const API_VOID         = 'VOID';
    const API_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';

    /**
     * Array indice for response reason
     *
     * Reason string captured as $messages[]
     */
    const RESPONSE_CODE_REASON = 4;

    /**
     * Array indice of Approved response code
     */
    const RESPONSE_CODE = 1;

    /**
     * API endpoint to use
     */
    private $_api_endpoint = null;

    /**
     * Duplicate-transaction window default setting
     */
    const DUPLICATE_TRANS_WINDOW = 2;

    /**
     * Settings for transaction
     *
     */
    private $_options = array(
        'x_version'           => self::API_VERSION,
        'x_delim_data'        => true,
        'x_relay_response'    => false,
        'x_login'             => null,
        'x_tran_key'          => null,
        'x_duplicate_window'  => self::DUPLICATE_TRANS_WINDOW
        );

    /**
     * Response code messages
     */
    private $_responseCodes = array(
        '1' => 'Approved',
        '2' => 'Declined',
        '3' => 'Error'
        );

    /**
     * Response code errors
     */
    private $_responseCodeErrors = array(2, 3);

    /**
     * Array indice of CVV2/CVC2/CID response
     */
    const CARD_CODE_RESPONSE_CODE = 39;

    /**
     * CVV2/CVC2/CID messages
     */
    private $_cardCodeMessages = array(
        'M' => 'Card verification number matched',
        'N' => 'Card verification number does not match',
        'P' => 'Card verification number was not processed',
        'S' => 'Card verification number should be on card but was not indicated',
        'U' => 'Issuer was not certified for card verification'
        );

    /**
     * CVV error codes
     */
    private $_cardCodeErrors = array('N', 'S');

    /**
     * Array indice of AVS response
     */
    const AVS_RESPONSE_CODE = 6;

    /**
     * AVS messages
     */
    private $_avsMessages = array(
        'A' => 'Street address matches billing information, zip/postal code does not',
        'B' => 'Address information not provided for address verification check',
        'E' => 'Address verification service error',
        'G' => 'Non-U.S. card-issuing bank',
        'N' => 'Neither street address nor zip/postal match billing information',
        'P' => 'Address verification not applicable for this transaction',
        'R' => 'Payment gateway was unavailable or timed out',
        'S' => 'Address verification service not supported by issuer',
        'U' => 'Address information is unavailable',
        'W' => '9-digit zip/postal code matches billing information, street address does not',
        'X' => 'Street address and 9-digit zip/postal code matches billing information',
        'Y' => 'Street address and 5-digit zip/postal code matches billing information',
        'Z' => '5-digit zip/postal code matches billing information, street address does not');

    /**
     * AVS error codes
     */
    private $_avsErrors = array('A', 'E', 'N', 'R', 'W', 'Z');

    /**
     * Mercantile_AuthorizeNetGateway::__construct()
     * 
     * Sets up login and tran_key for requests, sets testmode to false
     *
     * @param array $gatewayCreds An array expecting keys 'login' and 'tran_key'
     * @return Mercantile_AuthorizeNetGateway
     */
    public function __construct(array $gatewayCreds = null)
    {
        if (isset($gatewayCreds['login']) && is_string($gatewayCreds['login'])) {
            $this->_options['x_login']     = $gatewayCreds['login'];
        } else {
            throw new Mercantile_Exception('Login not string, is ' . gettype($gatewayCreds['login']));
        }

        if (isset($gatewayCreds['tran_key']) && is_string($gatewayCreds['tran_key'])) {
            $this->_options['x_tran_key']  = $gatewayCreds['tran_key'];
        } else {
            throw new Mercantile_Exception('Transaction key not string, is ' .
                gettype($gatewayCreds['tran_key']));
        }

        $this->setTest(false);
    }

    /**
     * Convenience method for setting request value to test gateway and
     * toggling the active api endpoint
     *
     * @param bool $testing Boolean testing value
     */
    public function setTest($testing = null)
    {
        if ($testing == true) {
            $this->_options['x_test_request'] = true;
            $this->_api_endpoint = self::API_SANDBOX_ENDPOINT;
            $this->_options['x_duplicate_window'] = 0;
        } else {
            $this->_options['x_test_request'] = false;
            $this->_api_endpoint = self::API_LIVE_ENDPOINT;
            $this->_options['x_duplicate_window'] = self::DUPLICATE_TRANS_WINDOW;
        }
    }

    /**
     * Make request and return legible response
     *
     * Put in an array of k/v pairs, get out
     * an array response from ANET
     *
     * @param array $postArray Array to be posted
     */
    protected function _post(array $postArray = null)
    {
        $client = new Zend_Http_Client(self::API_SANDBOX_ENDPOINT);

        $client->setParameterPost($postArray);

        $client->request('POST');

        $responseString = $client->getLastResponse()->getRawBody();

        // pad array to match ANET doc indices
        return array_merge(array(''), explode(',', $responseString));
    }

    /**
     * Parse response string to a Response object
     */
    protected function _parseResponse(array $response = null)
    {
        if (!isset($response) or !is_array($response))
            throw new Mercantile_Exception('Response is not array, is ' . gettype($response));

        $success = true;

        $cardCodeResponseCode = $response[self::CARD_CODE_RESPONSE_CODE];

        if (in_array($cardCodeResponseCode, $this->_cardCodeErrors) == true) {
            $success = false;
        }

        $messages = array($this->_cardCodeMessages[$cardCodeResponseCode]);

        $avsResponseCode = $response[self::AVS_RESPONSE_CODE];

        if (in_array($avsResponseCode, $this->_avsErrors) == true) {
            $success = false;
        }
        
        $messages[] = $this->_avsMessages[$avsResponseCode];

        $responseCode = $response[self::RESPONSE_CODE];

        if (in_array((string)$responseCode, $this->_responseCodeErrors) == true) {
            $success = false;
        }

        $messages[] = $response[self::RESPONSE_CODE_REASON];

        $params = array(
            'approval_code' => $response[5],
            'transaction_id'=> $response[7]
            );

        return new Mercantile_Gateway_Response($success, $messages, $params);
    }

    /**
     * Mercantile_AuthorizeNetGateway::authorize()
     * 
     * @param float $chargeAmt Money amt to charge
     * @param Mercantile_Billing_CreditCard $ccObj 
     * @param array $options Array of arbitrary x_params to include
     * @return Mercantile_Gateway_Response
     */
    public function authorize($chargeAmt = null, Mercantile_Billing_CreditCard $ccObj = null, array $options = array())
    {
        $request = array(
            'x_type'      => self::API_AUTH_ONLY,
            'x_amount'    => $chargeAmt,
            'x_card_num'  => $ccObj->getNumber(),
            'x_exp_date'  => $ccObj->getExpDate(),
            'x_first_name'=> $ccObj->getFirstName(),
            'x_last_name' => $ccObj->getLastName(),
            'x_card_code' => $ccObj->getCardCode()
            );

        foreach ($options as $key => $value) {
            $request[$key] = $value;
        }

        $decodedResponse = $this->_post(array_merge($request, $this->_options));

        $responseObj = $this->_parseResponse($decodedResponse);

        return $responseObj;
    }

    /**
     * Response object must supply a valid transaction id 
     *
     * Must test $responseObj->isValid prior to capture
     *
     * @param float $chargeAmt The money amount to charge
     * @param Mercantile_Gateway_Response $responseObj Response object
     * @param array $options Array of arbitrary x_params to include
     */
    public function capture($chargeAmt = null, Mercantile_Gateway_Response $responseObj = null, array $options = array())
    {
        $params = $responseObj->getParams();

        $request = array(
            'x_type'      => self::API_PRIOR_AUTH_CAPTURE,
            'x_amount'    => $chargeAmt,
            'x_trans_id'  => $params['transaction_id']
            );

        foreach ($options as $key => $value) {
            $request[$key] = $value;
        }

        $decodedResponse = $this->_post(array_merge($request, $this->_options));

        if (in_array($decodedResponse[self::RESPONSE_CODE], $this->_responseCodeErrors) == false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Refunds (credits) an account.  Indicates to gateway that money flows
     * from the merchant to the customer.
     *
     * The following gotchas apply:
     * -Credits require full or first-4 digits of original card
     * -Credit must be issued within 120 days
     * -Transaction must be settled
     * -Credit must not exceed original transaction total
     * @TODO: needs to be passed array of options
     */
    public function credit($chargeAmt = null, array $options = array())
    {
        $request = array(
            'x_type'      => self::API_CREDIT,
            'x_amount'    => $chargeAmt,
            'x_trans_id'  => $options['transaction_id'],
            'x_number'    => $options['number']
            );

        if (isset($options['transaction_id']) == true)
            $request['x_trans_id'] = $options['transaction_id'];
        else
            throw new Mercantile_Exception('Transaction ID required');

        if (isset($options['number']) == true)
            $request['x_number'] = $options['number'];
        else
            throw new Mercantile_Exception('Require valid card number');
        
        $decodedResponse = $this->_post(array_merge($request, $this->_options));

        if (in_array($decodedResponse[self::RESPONSE_CODE], $this->_responseCodeErrors) == false) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Void
     */
    public function void(array $options = array())
    {
        $request = array(
            'x_type'      => self::API_VOID,
            );

        if (isset($options['transaction_id']) == true)
            $request['x_trans_id'] = $options['transaction_id'];
        else
            throw new Mercantile_Exception('Transaction ID required');

        $decodedResponse = $this->_post(array_merge($request, $this->_options));

        if (in_array($decodedResponse[self::RESPONSE_CODE], $this->_responseCodeErrors) == false) {
            return true;
        } else {
            return false;
        }
    }
}
