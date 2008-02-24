<?php
/**
 * @package Mercantile_Gateways
 * @subpackage AuthNetCim
 */
class Mercantile_Gateways_AuthNetCim_PaymentProfile extends DOMDocument
{
    public function __construct()
    {
        parent::__construct();
        
        $this->appendChild(new DOMElement('paymentProfile'));

        $this->documentElement->appendChild(new DOMElement('customerType', 'individual'));

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

        $creditCard = $payment->appendChild(new DOMElement('creditCard'));

        $creditCard->appendChild(new DOMElement('cardNumber'));
        $creditCard->appendChild(new DOMElement('expirationDate'));

        $bankAccount = $payment->appendChild(new DOMElement('bankAccount'));

        $bankAccount->appendChild(new DOMElement('accountType'));
        $bankAccount->appendChild(new DOMElement('nameOnAccount'));
        $bankAccount->appendChild(new DOMElement('echeckType'));
        $bankAccount->appendChild(new DOMElement('bankName'));
        $bankAccount->appendChild(new DOMElement('routingNumber'));
        $bankAccount->appendChild(new DOMElement('accountNumber'));

        $driversLicense = $this->documentElement->appendChild(new DOMElement('driversLicense'));

        $driversLicense->appendChild(new DOMElement('state'));
        $driversLicense->appendChild(new DOMElement('number'));
        $driversLicense->appendChild(new DOMElement('dateOfBirth'));

        $this->documentElement->appendChild(new DOMElement('taxId'));
    }
}
