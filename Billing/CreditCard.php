<?php
require_once 'Zend/Validate.php';

/**
 * Mercantile_Billing_CreditCard
 *
 * CC object for storing credentials in
 * please dont serialize or store the CC data
 * unless you're PCI compliant, thx
 *
 * @package Mercantile_Billing
 */
class Mercantile_Billing_CreditCard
{
    private $_type      = null;
    private $_number    = null;
    private $_exp_date  = null;
    private $_card_code = null;
    private $_first_name= null;
    private $_last_name = null;

    /**
     * Instantiate a new CreditCard object
     *
     * $ccInfo is an array of relevant CC values, the following options
     * are required: type, number, month, year
     * Other likely variables are: card_code, first name, last name
     * billing address
     *
     * @param array $ccInfo
     */
    public function __construct(array $ccInfo = null)
    {
        if (isset($ccInfo['type']) && is_string($ccInfo['type'])) {
            $this->_type = $ccInfo['type'];
        } else {
            throw new Mercantile_Exception('CC type invalid, is ' . gettype($ccInfo['type']));
        }

        if (isset($ccInfo['number']) && is_string($ccInfo['number'])) {
            $this->_number = $ccInfo['number'];
        } else {
            throw new Mercantile_Exception('CC number invalid, is ' . gettype($ccInfo['number']));
        }

        if (isset($ccInfo['month']) && is_int($ccInfo['month'])) {
            if (isset($ccInfo['year']) && is_int($ccInfo['year'])) {
                if (strlen($ccInfo['year']) == 4) {
                    $this->_exp_date = $ccInfo['month'] . $ccInfo['year'];
                } else {
                    throw new Mercantile_Exception('CC year not 4 digits, is ' . strlen($ccInfo['year']));
                }
            } else {
                throw new Mercantile_Exception('CC exp date year invalid, is ' . gettype($ccInfo['year']));
            }
        } else {
            throw new Mercantile_Exception('CC exp date month invalid, is ' . gettype($ccInfo['month']));
        }

        if (isset($ccInfo['card_code']) && is_int($ccInfo['card_code'])) {
            $this->_card_code = $ccInfo['card_code'];
        } else {
            throw new Mercantile_Exception('CVC/CVV2/CIV invalid, is ' . gettype($ccInfo['card_code']));
        }

        if (isset($ccInfo['first_name']) && is_string($ccInfo['first_name'])) {
            $this->_first_name = $ccInfo['first_name'];
        } else {
            throw new Mercantile_Exception('CC fname invalid, is ' . gettype($ccInfo['first_name']));
        }

        if (isset($ccInfo['last_name']) && is_string($ccInfo['last_name'])) {
            $this->_last_name = $ccInfo['last_name'];
        } else {
            throw new Mercantile_Exception('CC lname invalid, is ' . gettype($ccInfo['last_name']));
        }
    }

    public function getType()
    {
        return $this->_type;
    }
    public function getNumber()
    {
        return $this->_number;
    }
    public function getExpDate()
    {
        return $this->_exp_date;
    }
    public function getCardCode()
    {
        return $this->_card_code;
    }
    public function getFirstName()
    {
        return $this->_first_name;
    }
    public function getLastName()
    {
        return $this->_last_name;
    }
    /**
     * Find out if the CC passes Luhn algo
     */
    public function isValid()
    {
        $validator = new Zend_Validate_Ccnum();

        if ($validator->isValid($this->getNumber()) == true) {
            return true;
        } else {
            return false;
        }
    }
}
