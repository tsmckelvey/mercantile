<?php
require_once dirname(dirname(dirname(__FILE__))) . '/Exception.php';

class Mercantile_Gateways_AuthNetArb_Subscription extends DomDocument
{
	const NAME = 'name';

	/**
	 * Acceptable optional values
	 */
	protected $_options = array(
		self::NAME,
	);

	/**
	 * The measurement of time, in association with 
	 * the Interval Unit, that is used to define the
	 * frequency of the billing occurrences
	 *
	 * Up to 3 digits
	 */
	protected $_length = null;

	const DAYS = 'days';
	const MONTHS = 'months';

	/**
	 * The unit of time, in association with Interval Length,
	 * between each billing occurrence
	 */
	protected $_unit = null;

	protected $_units = array(
		self::DAYS,
		self::MONTHS
	);

	/**
	 * Payment schedule element, commonly accessed
	 */
	protected $_paymentScheduleElement = null;

	/**
	 * Required, needed for setInterval()
	 */
	protected $_intervalElement = null;

	public function __construct($options = null)
	{
		parent::__construct();

		$this->formatOutput = true;

		$this->appendChild(new DomElement('subscription'));
		
		if (is_array($options)) $this->setOptions($options);

		$this->_paymentScheduleElement = $this->documentElement
											  ->appendChild(new DomElement('paymentSchedule')); /* @required */

		$this->_intervalElement = $this->_paymentScheduleElement
						 			   ->appendChild(new DomElement('interval')); /* @required */
	}

	public function setOptions(array $options)
	{
		if (isset($options[self::NAME])) {
			$this->documentElement->setAttribute('name', $options[self::NAME]);
		}
	}

	/**
	 * Set the subscription Interval
	 * @required
	 * @param int $length
	 * @param string $unit
	 */
	public function setInterval($length, $unit)
	{
		$length = (int)$length;
		if ($unit == self::DAYS) {
			if (($length < 7) || ($length > 365)) {
				throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setInterval(): ' .
					$length . ' must be between 7 and 365 when using "days" as unit');
			}
		} else if ($unit == self::MONTHS) {
			if (($length < 1) || ($length > 12)) {
				throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setInterval(): ' .
					$length . ' must be between 1 and 12 when using "months" as unit');
			}
		} else {
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setInterval(): ' .
				$unit . ' not a valid Interval Unit');
		}

		$this->_intervalElement->appendCHild(new DomElement('length'))->nodeValue = $length;

		$this->_intervalElement->appendChild(new DomElement('unit'))->nodeValue= $unit;

		return $this;
	}
	
	/**
	 * @required
	 * @param string $date YYYY-MM-DD
	 */
	public function setStartDate($date)
	{
		if (!preg_match('/\d{4}\-\d{2}\-\d{2}/', $date)) {
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setStartDate(): ' .
				$date . ' does not match YYY-MM-DD');
		}

		$this->_paymentScheduleElement->appendChild(new DomElement('startDate'))->nodeValue = $date;

		return $this;
	}

	/**
	 * @required
	 * @param int occurrences 9999 for no end-date
	 */
	public function setTotalOccurrences($occurrences = 9999)
	{
		$occurrences = (int)$occurrences;

		if (strlen((string)$occurrences) > 4) {
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setTotalOccurrences(): ' .
				$occurrences . ' must be less than 5 long');
		}

		$this->_paymentScheduleElement->appendChild(new DomElement('totalOccurrences'))->nodeValue = $occurrences;

		return $this;
	}

	/**
	 * @optional
	 * @param int occurrences 
	 */
	public function setTrialOccurrences($occurrences)
	{
		$occurrences = (int)$occurrences;

		if (strlen((string)$occurrences) > 2) {
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setTrialOccurrences(): ' .
				$occurrences . ' must be less than 3 long');
		}

		$this->_paymentSchedulElement->appendChild(new DomElement('trialOccurrences'))->nodeValue = $occurrences;

		return $this;
	}

	/**
	 * @required
	 * @param float|int amount Amount to bill customer for each payment
	 */
	public function setAmount($amount)
	{
		// TODO validate and test

		$this->documentElement->appendChild(new DomElement('amount'))->nodeValue = $amount;

		return $this;
	}

	// TODO setTrialAmount

	public function __toString()
	{
		return $this->saveXML();
	}
}
