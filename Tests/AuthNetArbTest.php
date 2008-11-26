<?php
require_once '/opt/Milksites/library/Zend/Loader.php';
Zend_Loader::registerAutoload();
set_include_path(
	'/opt/Milksites/library' . PATH_SEPARATOR .
	get_include_path()
);
require_once dirname(dirname(__FILE__)) . '/Gateways/AuthNetArb.php';
require_once dirname(dirname(__FILE__)) . '/Gateways/AuthNetArb/Subscription.php';
require_once dirname(dirname(__FILE__)) . '/Gateways/AuthNetArb/Response.php';
require_once dirname(dirname(__FILE__)) . '/Gateways/AuthNetArb/CreditCard.php';
require_once dirname(dirname(__FILE__)) . '/Response.php';

class AuthNetArbTest extends PHPUnit_Framework_TestCase
{
	public function setup()
	{
		$this->sub = new Mercantile_Gateways_AuthNetArb_Subscription();	
	}

	public function tearDown()
	{

	}

	public function testSetOptions()
	{
		/*
		// TODO this is broken?
		$this->sub->setOptions(array(
			'name' => 'a test subscription',
		));
		*/

		$creditCard = new Mercantile_Gateways_AuthNetArb_CreditCard();
		$creditCard->setExpirationDate('2011-10')
				   ->setCardNumber(4222222222222222);

		$this->sub->setInterval(1, 'months')
				  ->setStartDate('2009-11-01')
				  ->setAmount('25')
				  ->setPayment($creditCard)
				  ->setTotalOccurrences(9999)
				  ->setBillingAddress(array(
					'firstName' => 'Bob',
					'lastName'  => 'Bobsen',
					'address' => '6947 Coal Creek Pkwy SE #71789023',
					'city' => 'Newcastle',
					'state' => 'WA',
					'zip' => '98069',
					'country' => 'USA'
				  ));

		$arb = new Mercantile_Gateways_AuthNetArb(array(
			'name' => '8wd65QSj',
			'transactionKey' => '8CP6zJ7uD875J6tY'
		));

		$response = $arb->createSubscription($this->sub);
		print_r($response);
	}

	public function testSetIntervalOutOfDaysRange()
	{
		try {
			$this->sub->setInterval(366, 'days');
		} catch (Exception $e) {
			return;		
		}

		// TODO other cases

		$this->fail('Expected exception');
	}

	public function testSetStartDateInvalidFormat()
	{
		try {
			$this->sub->setStartDate('09-09-22');
		} catch (Exception $e) {
			return;
		}

		$this->fail('Expected exception');
	}

	public function testSetTotalOccurrences()
	{
		try {
			$this->sub->setTotalOccurrences(10000);
		} catch (Exception $e) {
			return;
		}

		$this->fail('Expected exception');
	}
}
