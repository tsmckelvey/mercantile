<?php
class Mercantile_Gateways_GCheckout_Response_NewOrderNotification extends DomDocument
{
	const GOOGLE_ORDER_NUMBER 		= 'google-order-number';
	const BUYER_SHIPPING_ADDRESS 	= 'buyer-shipping-address';
	const CONTACT_NAME = 'contact-name';
	const EMAIL = 'email';
	const ADDRESS1 = 'address1';
	const CITY = 'city';
	const REGION = 'region';
	const POSTAL_CODE = 'postal-code';
	const COUNTRY_CODE = 'country-code';
	const PHONE = 'phone';
	const STRUCTURED_NAME = 'structured-name';
	const FIRST_NAME = 'first-name';
	const LAST_NAME = 'last-name';
	const BUYER_BILLING_ADDRESS = 'buyer-billing-address';
	const BUYER_ID = 'buyer-id';
	const FULFILLMENT_ORDER_STATE = 'fulfillment-order-state';
	const FINANCIAL_ORDER_STATE = 'financial-order-state';
	const SHOPPING_CART = 'shopping-cart';
	const CART_EXPIRATION = 'cart-expiration';
	const GOOD_UNTIL_DATE = 'good-until-date';
	const ITEMS = 'items';
	const ITEM = 'item';
	const MERCHANT_ITEM_ID = 'merchant-item-id';
	const ITEM_NAME = 'item-name';
	const ITEM_DESCRIPTION = 'item-description';
	const QUANTITY = 'quantity';
	const TAX_TABLE_SELECTOR = 'tax-table-selector';
	const UNIT_PRICE = 'unit-price';
	const CURRENCY = 'currency';
	const MERCHANT_PRIVATE_ITEM_DATA = 'merchant-private-item-data';
	const MERCHANT_PRODUCT_ID = 'merchant-product-id';
	const ORDER_ADJUSTMENT = 'order-adjustment';
	const MERCHANT_CALCULATION_SUCCESSFUL = 'merchant-calculation-successful';
	const MERCHANT_CODES = 'merchant-codes';
	const COUPON_ADJUSTMENT = 'coupon-adjustment';
	const APPLIED_AMOUNT = 'applied-amount';
	const CODE = 'code';
	const CALCULATED_AMOUNT = 'calculated-amount';
	const MESSAGE = 'message';
	const GIFT_CERTIFICATE_ADJUSTMENT = 'gift-certificate-adjustment';
	const TOTAL_TAX = 'total-tax';
	const SHIPPING = 'shipping';
	const MERCHANT_CALCULATED_SHIPPING_ADJUSTMENT = 'merchant-calculated-shipping-adjustment';
	const SHIPPING_NAME = 'shipping-name';
	const SHIPPING_COST = 'shipping-cost';
	const ORDER_TOTAL = 'order-total';
	const BUYER_MARKETING_PREFERENCES = 'buyer-marketing-preferences';
	const EMAIL_ALLOWED = 'email-allowed';
	const TIMESTAMP = 'timestamp';

	protected $_shippingLastName = null;
	protected $_shippingFirstName = null;
	protected $_shippingAddress1 = null;
	protected $_shippingCity = null;
	protected $_shippingRegion = null;
	protected $_shippingPostalCode = null;
	protected $_shippingCountryCode = null;

	public function __construct($xmlString)
	{
		$this->loadXml($xmlString);
	}

	public function getShippingFirstName()
	{
		if ($this->_shippingFirstName === null) {
			$firstName = $this->getBuyerShippingAddress()
							  ->getElementsByTagName(self::STRUCTURED_NAME)
							  ->item(0)
							  ->getElementsByTagName(self::FIRST_NAME)
							  ->item(0)
							  ->textContent;

			$this->_shippingFirstName = $firstName;
		}

		return $this->_shippingFirstName;
	}

	public function getShippingLastName()
	{
		if ($this->_shippingLastName === null) {
			$lastName = $this->getBuyerShippingAddress()
							 ->getElementsByTagName(self::STRUCTURED_NAME)
							 ->item(0)
							 ->getElementsByTagName(self::LAST_NAME)
							 ->item(0)
							 ->textContent;

			$this->_shippingLastName = $lastName;
		}

		return $this->_shippingLastName;
	}

	public function getShippingAddress1()
	{
		if ($this->_shippingAddress1 === null) {
			$shippingAddress1 = $this->getBuyerShippingAddress()
									 ->getElementsByTagName(self::ADDRESS1)
									 ->item(0)
									 ->textContent;

			$this->_shippingAddress1 = $shippingAddress1;
		}

		return $this->_shippingAddress1;
	}

	public function getShippingCity()
	{
		if ($this->_shippingCity === null) {
			$shippingCity = $this->getBuyerShippingAddress()
								 ->getElementsByTagName(self::CITY)
								 ->item(0)
								 ->textContent;

			$this->_shippingCity = $shippingCity;
		}

		return $this->_shippingCity;
	}

	public function getShippingRegion()
	{
		if ($this->_shippingRegion === null) {
			$shippingRegion = $this->getBuyerShippingAddress()
								  ->getElementsByTagName(self::REGION)
								  ->item(0)
								  ->textContent;

			$this->_shippingRegion = $shippingRegion;
		}

		return $this->_shippingRegion;
	}

	public function getShippingPostalCode()
	{
		if ($this->_shippingPostalCode === null) {
			$shippingPostalCode = $this->getBuyerShippingAddress()
								   ->getElementsByTagName(self::POSTAL_CODE)
								   ->item(0)
								   ->textContent;

			$this->_shippingPostalCode = $shippingPostalCode;
		}

		return $this->_shippingPostalCode;
	}

	public function getShippingCountryCode()
	{
		if ($this->_shippingCountryCode === null) {
			$shippingCountryCode = $this->getBuyerShippingAddress()
										->getElementsByTagName(self::COUNTRY_CODE)
										->item(0)
										->textContent;

			$this->_shippingCountryCode = $shippingCountryCode;
		}

		return $this->_shippingCountryCode;
	}

	/**
	 * Getting elements
	 */
	public function __call($method, $args)
	{
		if (substr($method, 0, 3) !== 'get') {
			throw new Mercantile_Exception("$method does not exist");
		}

		$method = explode('get', $method);
		$chars = str_split($method[1]);

		$const = '';

		foreach ($chars as $k => $c) {
			if (preg_match('/[A-Z]/', $c)) {
				$c = ($k == 0) ? $c : '_' . $c;
			}

			$const .= strtoupper($c);
		}

		$reflectionClass = new ReflectionClass('Mercantile_Gateways_GCheckout_Response_NewOrderNotification');

		if (array_key_exists($const, $reflectionClass->getConstants())) {
			try {
				$result = $this->getElementsByTagName($reflectionClass->getConstant($const))->item(0);

				return $result;
			} catch (Exception $e) {
				return null;
			}
		}
	}
}
