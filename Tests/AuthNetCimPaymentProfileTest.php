<?php
require_once 'Bootstrap.php';

class AuthNetCimPaymentProfileTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $ccOptions = array('type' => 'visa',
                           'number' => '411111111111111',
                           'month' => 09,
                           'year' => 2009,
                           'card_code' => 388,
                           'first_name' => 'tom',
                           'last_name' => 'yevlekcm');

        $this->billingObj = new Mercantile_Billing_CreditCard($ccOptions);
    }
    public function tearDown()
    {
        $this->billingObj = null;
    }
    public function testPaymentProfile()
    {
        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile('individual');

        $this->assertType('Mercantile_Gateways_AuthNetCim_PaymentProfile', $payProfile);

        $this->assertType('DOMDocument', $payProfile);
    }
    public function testPaymentProfile_invalidCustomerTypeThrowsException()
    {
        try {
            $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile('ewok');
        } catch (Mercantile_Exception $e) {
            return;
        }

        $this->fail('Expected exception for wrong customer type in constructor');
    }
    public function testSetPayment_ccLengthGreaterThan16AndException()
    {
        $ccOptions = array('type' => 'visa',
                           'number' => '12345678901234567',
                           'month' => 09,
                           'year' => 2009,
                           'card_code' => 388,
                           'first_name' => 'tom',
                           'last_name' => 'yevlekcm');

        $billingObj = new Mercantile_Billing_CreditCard($ccOptions);

        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        try {
            $payProfile->setPayment($billingObj);
        } catch (Mercantile_Exception $e) {
            return;
        }

        $this->fail('Expected exception for > 16 cc length');
    }
    public function testSetPayment_ccLengthLesserThan13AndException()
    {
        $ccOptions = array('type' => 'visa',
                           'number' => '123456789012',
                           'month' => 09,
                           'year' => 2009,
                           'card_code' => 388,
                           'first_name' => 'tom',
                           'last_name' => 'yevlekcm');

        $billingObj = new Mercantile_Billing_CreditCard($ccOptions);

        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        try {
            $payProfile->setPayment($billingObj);
        } catch (Mercantile_Exception $e) {
            return;
        }

        $this->fail('Expected exception for < 13 cc length');
    }
    public function testSetPayment_creditCardObjectAndSucceeds()
    {
        $billingObj = $this->billingObj;

        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile('individual');

        $payProfile->setPayment($billingObj);

        $payProfile->formatOutput = true;
        var_dump($payProfile->saveXML());
    }
    public function testSetPayment_bankAccountObjectAndSucceeds()
    {
        $this->markTestSkipped();
    }
    public function testSetPayment_wrongObjectTypeThrowsException()
    {
        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        try {
            $payProfile->setPayment(new stdClass);
        } catch (Mercantile_Exception $e) {
            return;
        }

        $this->fail('Expected exception for wrong payment type');
    }
    public function testSetPayment_overrideExistingPayment()
    {
        $payProfile = new Mercantile_Gateways_AuthNetCim_PaymentProfile();

        $payProfile->setPayment($this->billingObj);

        $ccOptions = array('type' => 'visa',
                           'number' => '1234567890123',
                           'month' => 09,
                           'year' => 2009,
                           'card_code' => 388,
                           'first_name' => 'tom',
                           'last_name' => 'yevlekcm');

        $ccObj = new Mercantile_Billing_CreditCard($ccOptions);

        $payProfile->setPayment($ccObj);
    }
}
