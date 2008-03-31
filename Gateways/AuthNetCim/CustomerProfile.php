<?php
/**
 * Class for assembling a CustomerProfile
 * @package Mercantile_Gateways
 * @subpackage AuthNetCim
 */
class Mercantile_Gateways_AuthNetCim_CustomerProfile extends DomDocument
{
    private $_paymentProfiles = array();

    /**
     * Top-level optional nodes
     */
    private $_optionalElements = array(
        'merchantCustomerId',
        'description',
        'email'
        );

    public function __construct(array $options = null)
    {
        parent::__construct();

        $this->formatOutput = true;

        $this->appendChild(new DOMElement('profile'));

        if (gettype($options) !== 'array')
            throw new Mercantile_Exception('options must be array, is ' . gettype($options));

        if (isset($options['merchantCustomerId']) && strlen($options['merchantCustomerId']) > 20) {
            throw new Mercantile_Exception('MerchantCustomerId not 20 or less chars, is ' . strlen($options['merchantCustomerId']));
        } else if (isset($options['merchantCustomerId'])) {
            $this->documentElement->appendChild(new DOMElement('merchantCustomerId', $options['merchantCustomerId']));
        }

        if (isset($options['description']) && strlen($options['description']) > 255) {
            throw new Mercantile_Exception('Description not <= 255 chars, is ' . strlen($options['description']));
        } else if (isset($options['description'])) {
            $this->documentElement->appendChild(new DOMElement('description', $options['description']));
        }

        if (isset($options['email']) && strlen($options['email']) > 255) {
            throw new Mercantile_Exception('Email not <= 255 chars, is ' . strlen($options['email']));
        } else if (isset($options['email'])) {
            $this->documentElement->appendChild(new DOMElement('email', $options['email']));
        }
    }

    public function __toString()
    {
        return $this->saveXML();
    }

    /**
     * Add a payment profile to this customer profile
     *
     * Duplicate payment profiles will silently fail 
     *
     * Cardinality 0..*
     *
     * @param Mercantle_Gateways_AuthNetCim_PaymentProfile $payProfile A payment profile object
     */
    public function addPaymentProfile(Mercantile_Gateways_AuthNetCim_PaymentProfile $payProfile = null)
    {
        // @TODO: add validation code
        // @TODO: add interface for paymentProfiles

        // check for duplicates, else anet returns an ambiguous error message
        foreach ($this->_paymentProfiles as $profile) {
            if ($this->saveXML($profile) == (string)$payProfile) {
                return false;
            }
        }

        $payProfile = $this->importNode($payProfile->documentElement, $deep = true);

        $this->_paymentProfiles[] = $this->documentElement->appendChild($payProfile);

        $this->documentElement->appendChild($payProfile);

        return true;
    }
}
