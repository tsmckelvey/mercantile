<?php
require_once dirname(dirname(__FILE__)) . '/Gateways/AuthNetArb/Subscription.php';

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
		$this->sub->setOptions(array(
			'name' => 'a test subscription'
		));

		$this->sub->setInterval(1, 'months')
				  ->setStartDate('2009-12-01')
				  ->setTotalOccurrences(300)
				  ->setAmount(25);

		echo $this->sub;
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
