<?php
interface Mercantile_Billing_CreditCard_Interface
{
	public function setCardNumber($ccNumber);
	public function getCardNumber();
	public function setExpirationDate($expDate);
	public function getExpirationDate();
}
