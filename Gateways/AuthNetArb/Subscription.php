<?php
require_once dirname(dirname(dirname(__FILE__))) . '/Exception.php';

class Mercantile_Gateways_AuthNetArb_Subscription extends DomDocument
{
	const NAME = 'name';

	const SUBSCRIPTION = 'subscription';
	const PAYMENT_SCHEDULE = 'paymentSchedule';
	const INTERVAL = 'interval';

	const LENGTH = 'length';
	const UNIT = 'unit';

	const START_DATE_FORMAT = 'Y-M-dd';
	const START_DATE = 'startDate';

	const TOTAL_OCCURRENCES = 'totalOccurrences';
	const TRIAL_OCCURRENCES = 'trialOccurrences';

	const AMOUNT = 'amount';

	const PAYMENT = 'payment';
	const CREDIT_CARD = 'creditCard';
	const CARD_NUMBER = 'cardNumber';
	const EXPIRATION_DATE = 'expirationDate';

	const BILL_TO = 'billTo';
	const FIRST_NAME = 'firstName';
	const LAST_NAME = 'lastName';
	const COMPANY = 'company';
	const ADDRESS = 'address';
	const CITY = 'city';
	const STATE = 'state';
	const ZIP = 'zip';
	const COUNTRY = 'country';

	const ORDER = 'order';
	const INVOICE_NUMBER = 'invoiceNumber';
	const DESCRIPTION = 'description';

	const CUSTOMER = 'customer';
	const ID = 'id';
	const EMAIL = 'email';
	const PHONE_NUMBER = 'phoneNumber';
	const FAX_NUMBER = 'faxNumber';

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

		$this->appendChild(new DomElement(self::SUBSCRIPTION));
		
		if (is_array($options)) $this->setOptions($options);

		$this->_paymentScheduleElement = $this->documentElement
											  ->appendChild(new DomElement(self::PAYMENT_SCHEDULE)); /* @required */

		$this->_intervalElement = $this->_paymentScheduleElement
						 			   ->appendChild(new DomElement(self::INTERVAL)); /* @required */

		$this->init();
	}

	/**
	 * Child method for initialization
	 */
	public function init() {}

	public function setOptions(array $options)
	{
		if (isset($options[self::NAME])) {
			$this->documentElement->appendChild(new DomElement(self::NAME))->nodeValue = $options[self::NAME];
		}
	}

	/**
	 * @optional
	 */
	public function setName($name)
	{
		$this->documentElement->appendChild(new DomElement(self::NAME))->nodeValue = $name;

		return $this;
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

		$this->_intervalElement->appendChild(new DomElement(self::LENGTH))->nodeValue = $length;

		$this->_intervalElement->appendChild(new DomElement(self::UNIT))->nodeValue= $unit;

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
				$date . ' does not match ' . self::START_DATE_FORMAT);
		}

		$this->_paymentScheduleElement->appendChild(new DomElement(self::START_DATE))->nodeValue = $date;

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

		$this->_paymentScheduleElement->appendChild(new DomElement(self::TOTAL_OCCURRENCES))->nodeValue = $occurrences;

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
		$this->_paymentSchedulElement->appendChild(new DomElement(self::TRIAL_OCCURRENCES))->nodeValue = $occurrences;
		return $this;
	}

	/**
	 * @required
	 * @param float|int amount Amount to bill customer for each payment
	 */
	public function setAmount($amount)
	{
		// TODO validate and test
		$this->documentElement->appendChild(new DomElement(self::AMOUNT))->nodeValue = $amount;
		return $this;
	}

	// TODO setTrialAmount

	/**
	 * // TODO this should accept BankAccount object, too
	 * @required
	 * @param Mercantile_Gateways_AuthNetArb_CreditCard $creditCard 
	 */
	public function setPayment(Mercantile_Billing_CreditCard_Interface $creditCard)
	{
		$paymentElement = $this->documentElement->appendChild(new DomElement(self::PAYMENT));
		$creditCardElement = $paymentElement->appendChild(new DomElement(self::CREDIT_CARD));
		$creditCardElement->appendChild(new DomElement(self::CARD_NUMBER))->nodeValue = $creditCard->getCardNumber();
		$creditCardElement->appendChild(new DomElement(self::EXPIRATION_DATE))->nodeValue = $creditCard->getExpirationDate();
		return $this;
	}

	/**
	 * @required
	 */
	public function setBillingAddress(array $billingAddress)
	{
		$billToElement = $this->documentElement->appendChild(new DomElement(self::BILL_TO));
		if (!isset($billingAddress[self::FIRST_NAME]))
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setBillingAddress() ' .
				self::FIRST_NAME . ' not in $billingAddress');
		$billToElement->appendChild(new DomElement(self::FIRST_NAME))->nodeValue = $billingAddress[self::FIRST_NAME];

		if (!isset($billingAddress[self::LAST_NAME]))
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_Subscription::setBillingAddress() ' .
				self::LAST_NAME . ' not in $billingAddress');
		$billToElement->appendChild(new DomElement(self::LAST_NAME))->nodeValue = $billingAddress[self::LAST_NAME];

		if (isset($billingAddress[self::COMPANY]))
			$billToElement->appendChild(new DomElement(self::COMPANY))->nodeValue = $billingAddress[self::COMPANY];

		if (isset($billingAddress[self::ADDRESS]))
			$billToElement->appendChild(new DomElement(self::ADDRESS))->nodeValue = $billingAddress[self::ADDRESS];

		if (isset($billingAddress[self::CITY]))
			$billToElement->appendChild(new DomElement(self::CITY))->nodeValue = $billingAddress[self::CITY];

		if (isset($billingAddress[self::STATE]))
			$billToElement->appendChild(new DomElement(self::STATE))->nodeValue = $billingAddress[self::STATE];

		if (isset($billingAddress[self::ZIP]))
			$billToElement->appendChild(new DomElement(self::ZIP))->nodeValue = $billingAddress[self::ZIP];
		
		if (isset($billingAddress[self::COUNTRY]))
			$billToElement->appendChild(new DomElement(self::COUNTRY))->nodeValue = $billingAddress[self::COUNTRY];

		return $this;
	}

	/**
	 * Contains optional order information
	 * @param int $invoiceNumber Merchant-assigned invoice number for the subscription
	 * @param string $description Description of the subscription
	 * @optional
	 */
	public function setOrder($invoiceNumber = null, $description = null)
	{
		$orderElement = $this->documentElement->appendChild(new DomElement(self::ORDER));	

		if (isset($invoiceNumber))
			$orderElement->appendChild(new DomElement(self::INVOICE_NUMBER))->nodeValue = $invoiceNumber;

		if (isset($description))
			$orderElement->appendChild(new DomElement(self::DESCRIPTION))->nodeValue = $description;

		return $this;
	}

	/**
	 * Contains information about the customer
	 * @param string $id Merchant-assigned identifier for the customer
	 * @param string $email The customer's email address
	 * @param string $phoneNumber The customer's phone number
	 * @param string $faxNumber The customer's fax number
	 * @optional
	 */
	public function setCustomer($id = null, $email = null, $phoneNumber = null, $faxNumber = null)
	{
		$customerElement = $this->documentElement->appendChild(new DomElement(self::CUSTOMER));

		if (isset($id))
			$customerElement->appendChild(new DomElement(self::ID))->nodeValue = $id;

		if (isset($email))
			$customerElement->appendChild(new DomElement(self::EMAIL))->nodeValue = $email;

		if (isset($phoneNumber))
			$customerElement->appendChild(new DomElement(self::PHONE_NUMBER))->nodeValue = $phoneNumber;

		if (isset($faxNumber))
			$customerElement->appendChild(new DomElement(self::FAX_NUMBER))->nodeValue = $faxNumber;

		return $this;
	}

	public function __toString()
	{
		return $this->saveXML();
	}
}
