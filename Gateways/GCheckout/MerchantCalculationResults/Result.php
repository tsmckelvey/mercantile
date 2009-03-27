<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/Exception.php';

/** 
 * TODO: taxes
 * TODO: gift certificates / coupons
 */
class Mercantile_Gateways_GCheckout_MerchantCalculationResults_Result extends DomDocument
{
	const RESULT = 'result';

	const SHIPPING_NAME = 'shipping-name';

	const ADDRESS_ID = 'address-id';

	const SHIPPING_RATE = 'shipping-rate';

	const SHIPPABLE = 'shippable';

	const CURRENCY = 'currency';

	protected $_shippingRate = null;

	protected $_shippable = null;

	public function __construct($shippingName = null, $addressId = null)
	{
		parent::__construct();

		$this->appendChild(new DomElement(self::RESULT));

		$this->setShippingName($shippingName)
			 ->setAddressId($id);

		$this->_shippingRate = $this->documentElement->appendChild(new DomElement(self::SHIPPING_RATE));

		$this->_shippable = $this->documentElement->appendChild(new DomElement(self::SHIPPABLE));
	}

	public function setShippingName($name)
	{
		$this->documentElement->setAttribute(self::SHIPPING_NAME, $shippingName);
		return $this;
	}

	public function setAddressId($id)
	{
		$this->documentElement->setAddressId(self::ADDRESS_ID, $id);
		return $this;
	}

	public function setShippingRate($rate)
	{
		if (!is_float($rate)) {
			throw new Mercantile_Exception(get_class($this) . '::setShippingRoute(): ' .
				'first arg must be float');
		}

		$this->_shippingRate->textContent = number_format((float) $rate, 2);

		return $this;
	}

	public function setShippable($shippable = true)
	{
		if (!is_bool($shippable)) {
			throw new Mercantile_Exception(get_class($this) . '::setShippable(): ' .
				'first arg must be bool');
		}

		$this->_shippable->textContent = (bool) $shippable;

		return $this;
	}
}
