<?php
/**
 * @package Mercantile_Gateways
 * @subpackage AuthNetCim
 */
class Mercantile_Gateways_AuthNetCim_PaymentProfile extends DOMDocument
{
    protected $_payment = null;

    protected $_customerTypes = array('individual', 'business');

    /**
     * @param string $customerType Enum in _customerTypes
     */
    public function __construct($customerType = null)
    {
        parent::__construct();

        $this->appendChild(new DOMElement('paymentProfiles'));
        
        // Cardinality 0..1
        if (isset($customerType)) {
            if (!in_array($customerType, $this->_customerTypes))
                throw new Mercantile_Exception("$customerType not in _customerTypes");

            $this->documentElement->appendChild(new DOMElement('customerType', $customerType));
        }

        /*
        $billTo = $this->documentElement->appendChild(new DOMElement('billTo'));

        $address = $billTo->appendChild(new DOMElement('address'));

        $address->appendChild(new DOMElement('firstName'));
        $address->appendChild(new DOMElement('lastName'));
        $address->appendChild(new DOMElement('company'));
        $address->appendChild(new DOMElement('address'));
        $address->appendChild(new DOMElement('city'));
        $address->appendChild(new DOMElement('state'));
        $address->appendChild(new DOMElement('zip'));
        $address->appendChild(new DOMElement('country'));
        $address->appendChild(new DOMElement('phoneNumber'));
        $address->appendChild(new DOMElement('faxNumber'));

        $payment = $this->documentElement->appendChild(new DOMElement('payment'));

        $driversLicense = $this->documentElement->appendChild(new DOMElement('driversLicense'));

        $driversLicense->appendChild(new DOMElement('state'));
        $driversLicense->appendChild(new DOMElement('number'));
        $driversLicense->appendChild(new DOMElement('dateOfBirth'));

        $this->documentElement->appendChild(new DOMElement('taxId'));
        */
    }

    public function __toString()
    {
        $this->formatOutput = true;

        // pass documentElement so the node is converted, not the doc, thus avoiding the xml version declaration
        return $this->saveXML($this->documentElement);
    }

    /**
     * Set billing object for this payment profile
     *
     * Cardinality 0..1
     *
     * @param Mercantile_Billing_CreditCard|Mercantile_Billing_BankAccount $billingObj Payment type object
     */
    public function setPayment($billingObj = null)
    {
        // paymentProfiles element (misleading name) may only have one "payment" child
        if ($this->_payment !== null) {
            $this->documentElement->removeChild($this->getElementsByTagName('payment')->item(0));
        }

        if ($billingObj instanceof Mercantile_Billing_CreditCard) {
            // @TODO: make interface for cc
            $ccNumber = $billingObj->getNumber();

            $ccLen = strlen($ccNumber);

            // must be between 13-16 characters
            if ($ccLen > 16 or $ccLen < 13) 
                throw new Mercantile_Exception('Credit card number wrong length, is ' . $ccLen);

            $this->_payment = $this->documentElement->appendChild(new DOMElement('payment'));

            $cc = $this->_payment->appendChild(new DOMElement('creditCard'));

            $cc->appendChild(new DOMElement('cardNumber', $ccNumber));

            // YYYY-MM
            $cc->appendChild(new DOMElement('expirationDate', '2008-10'));
        } else if ($billingObj instanceof Mercantile_Billing_BankAccount) {
            // @TODO: implement this someway
            return;
        } else {
            throw new Mercantile_Exception('billingObj wrong type, is ' . get_class($billingObj));
        }
    }

    /**
     * Set billTo element of paymentProfile
     *
     * Cardinality 0..1
     */
    public function setBillTo()
    {
    }

    /**
     * Set driversLicense
     *
     * Cardinality 0..1
     */
    public function setDriversLicense()
    {
    }

    /**
     * Set taxId
     *
     * Cardinality 0..1
     */
    public function setTaxId()
    {

    }
}
