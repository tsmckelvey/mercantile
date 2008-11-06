<?php
require_once dirname(dirname(dirname(__FILE__))) . '/Exception.php';
require_once dirname(dirname(dirname(__FILE__))) . '/Billing/CreditCard/Interface.php';

class Mercantile_Gateways_AuthNetArb_CreditCard implements Mercantile_Billing_CreditCard_Interface
{
	protected $_creditCardNumber = null;

	protected $_expirationDate = null;

	public function __construct()
	{

	}

	public function setCardNumber($ccNumber)
	{
		$this->_creditCardNumber = $ccNumber;

		return $this;
	}

	public function getCardNumber()
	{
		return $this->_creditCardNumber;
	}

	/**
	 * YYYY-MM
	 */
	public function setExpirationDate($expDate)
	{
		if (!preg_match('/\d{4}\-\d{2}/', $expDate)) {
			throw new Mercantile_Exception('Mercantile_Gateways_AuthNetArb_CreditCard::setExpirationDate(): ' .
				'must be in the format YYYY-MM');
		}

		$this->_expirationDate = $expDate;

		return $this;
	}

	public function getExpirationDate()
	{
		return $this->_expirationDate;
	}
}
