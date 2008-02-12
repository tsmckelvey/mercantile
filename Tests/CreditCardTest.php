<?php
require 'Bootstrap.php';

class CreditCardTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }
    public function tearDown()
    {
    }
    public function testCreditCard()
    {
        $cc = array(
            'type'      => 'visa',
            'number'    => '4007000000027',
            'month'     => 10,
            'year'      => 2008,
            'card_code' => 388,
            'first_name' => 'Bob',
            'last_name'  => 'Bobsen'
            );
        
        $cc = new Mercantile_Billing_CreditCard($cc);

        $this->assertType('Mercantile_Billing_CreditCard', $cc);
        $this->assertEquals('visa', $cc->getType());
        $this->assertEquals('4007000000027', $cc->getNumber());
        $this->assertEquals('102008', $cc->getExpDate());
        $this->assertEquals('388', $cc->getCardCode());
        $this->assertEquals('Bob', $cc->getFirstName());
        $this->assertEquals('Bobsen', $cc->getLastName());

        $this->assertTrue($cc->isValid());
    }
    public function testCreditCard_InvalidCcnum()
    {
        $cc = array(
            'type'      => 'visa',
            'number'    => '12345',
            'month'     => 10,
            'year'      => 2008,
            'card_code' => 388,
            'first_name' => 'Bob',
            'last_name'  => 'Bobsen'
            );
        
        $cc = new Mercantile_Billing_CreditCard($cc);

        $this->assertType('Mercantile_Billing_CreditCard', $cc);
        $this->assertFalse($cc->isValid());
    }
}
