<?php
class Mercantile_Gateways_GCheckout_Shipping_MerchantCalculated extends Mercantile_Gateways_GCheckout_Shipping_Abstract
{
	public function __construct($methodName = null, $price = null)
	{
        parent::__construct($methodName, $price);

		$this->appendChild(new DomElement(self::MERCHANT_CALCULATED_SHIPPING));

		$this->documentElement->setAttribute('name', $methodName);

		$this->documentElement->appendChild(new DomElement(self::PRICE, number_format($price, 2)))
							  ->setAttribute(self::CURRENCY, 'USD');
	}

	public function __toString()
	{
		return $this->saveXML($this->documentElement);
	}
}
