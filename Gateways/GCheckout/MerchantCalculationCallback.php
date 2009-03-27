<?php
class Mercantile_Gateways_GCheckout_MerchantCalculationCallback extends DomDocument
{
	const SERIAL_NUMBER = 'serial-number';

	const SHOPPING_CART = 'shopping-cart';

	const ITEMS = 'items';

	const ITEM = 'item';

	const CALCULATE = 'calculate';

	const ADDRESSES = 'addresses';

	const ANONYMOUS_ADDRESS = 'anonymous-address';

	const ID = 'id';

	const COUNTRY_CODE = 'country-code';

	const POSTAL_CODE = 'postal-code';

	const CITY = 'city';

	const REGION = 'region';

	protected $_responseBody = null;

	protected $_serialNumber = null;

	protected $_shoppingCart = null;

	protected $_countryCode = null;

	protected $_postalCode = null;

	protected $_city = null;

	protected $_region = null;

	public function __construct($responseBody)
	{
		$this->_responseBody = trim($responseBody);

		$this->loadXML($this->_responseBody, LIBXML_NOWARNING);
		$this->formatOutput = true;

		$this->_serialNumber = $this->documentElement->getAttribute(self::SERIAL_NUMBER);
	}

	/**
	 * @return Mercantile_Gateways_GCheckout_ShoppingCart
	 */
	public function getShoppingCart()
	{
		if ($this->_shoppingCart !== null) {
			return $this->_shoppingCart;
		}

		$cart = new Mercantile_Gateways_GCheckout_ShoppingCart();

		$items = $this->documentElement
					  ->getElementsByTagName(self::SHOPPING_CART)
					  ->item(0)
					  ->getElementsByTagName(self::ITEMS)
					  ->item(0)
					  ->getElementsByTagName(self::ITEM);

		foreach ($items as $item) {
			$cart->addItem( Mercantile_Gateways_GCheckout_Item::create($item) );
		}

		$this->_shoppingCart = $cart;

		return $cart;
	}

	public function getCountryCode()
	{
		if ($this->_countryCode !== null) {
			return $this->_countryCode;
		}

		$code = $this->documentElement
					 ->getElementsByTagName(self::CALCULATE)
					 ->item(0)
					 ->getElementsByTagName(self::ADDRESSES)
					 ->item(0)
					 ->getElementsByTagName(self::ANONYMOUS_ADDRESS)
					 ->item(0)
					 ->getElementsByTagName(self::COUNTRY_CODE)
					 ->item(0)
					 ->textContent;

		$this->_countryCode = $code;

		return $code;
	}

	public function getPostalCode()
	{
		if ($this->_postalCode !== null) {
			return $this->_postalCode;
		}

		$code = $this->documentElement
					 ->getElementsByTagName(self::CALCULATE)
					 ->item(0)
					 ->getElementsByTagName(self::ADDRESSES)
					 ->item(0)
					 ->getElementsByTagName(self::ANONYMOUS_ADDRESS)
					 ->item(0)
					 ->getElementsByTagName(self::POSTAL_CODE)
					 ->item(0)
					 ->textContent;

		$this->_postalCode = $code;

		return $code;
	}

	public function getCity()
	{
		if ($this->_city !== null) {
			return $this->_city;
		}

		$city = $this->documentElement
					 ->getElementsByTagName(self::CALCULATE)
					 ->item(0)
					 ->getElementsByTagName(self::ADDRESSES)
					 ->item(0)
					 ->getElementsByTagName(self::ANONYMOUS_ADDRESS)
					 ->item(0)
					 ->getElementsByTagName(self::CITY)
					 ->item(0)
					 ->textContent;

		$this->_city = $city;

		return $city;
	}

	public function getRegion()
	{
		if ($this->_region !== null) {
			return $this->_region;
		}

		$city = $this->documentElement
					 ->getElementsByTagName(self::CALCULATE)
					 ->item(0)
					 ->getElementsByTagName(self::ADDRESSES)
					 ->item(0)
					 ->getElementsByTagName(self::ANONYMOUS_ADDRESS)
					 ->item(0)
					 ->getElementsByTagName(self::REGION)
					 ->item(0)
					 ->textContent;

		$this->_region = $region;

		return $region;
	}

	public function getAnonymousAddressId()
	{
		$id = $this->documentElement
				   ->getElementsByTagName(self::CALCULATE)
				   ->item(0)
				   ->getElementsByTagName(self::ADDRESSES)
				   ->item(0)
				   ->getElementsByTagName(self::ANONYMOUS_ADDRESS)
				   ->getAttribute(self::ID);

		return $id;
	}
}
