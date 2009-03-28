<?php
/**
 * Class for wrapping a DOMElement object with which we
 * can easily custom-tailor our items per the GCheckout API
 *
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
class Mercantile_Gateways_GCheckout_Item extends DomDocument
{
	const MERCHANT_ITEM_ID = 'merchant-item-id';

    const NAME = 'name';

    const DESCRIPTION = 'description';

    const PRICE = 'price';

    const QUANTITY = 'quantity';

    //protected $_merchItemId = null;

    //protected $_taxTableSelector = null;

    //protected $_digitalContent = null;

    //protected $_merchPrivateItemData = null;

	protected $_data = array(
		self::MERCHANT_ITEM_ID => null,
		self::NAME => null,
		self::DESCRIPTION => null,
		self::PRICE => null,
		self::QUANTITY => null,
	);

    public function __construct(array $itemInfo = null)
    {
        parent::__construct('1.0', 'utf-8');

        $this->formatOutput = true;

        $this->appendChild(new DomElement('item'));

        if (is_null($itemInfo))
            throw new Mercantile_Exception('Item info not array, is ' . gettype($itemInfo));

		/**
		 * Optional
		 */
		if (isset($itemInfo[self::MERCHANT_ITEM_ID])) {
			$this->documentElement->appendChild(new DOMElement(self::MERCHANT_ITEM_ID, $itemInfo[self::MERCHANT_ITEM_ID]));
			$this->_data[self::MERCHANT_ITEM_ID] = $itemInfo[self::MERCHANT_ITEM_ID];
		}

        if (!isset($itemInfo[self::NAME]) or !is_string($itemInfo[self::NAME]))
            throw new Mercantile_Exception('Item name not string, is ' . gettype($itemInfo[self::NAME]));

        $this->documentElement->appendChild(new DOMElement('item-name', $itemInfo[self::NAME]));
		$this->_data[self::NAME] = $itemInfo[self::NAME];

        if (!isset($itemInfo[self::DESCRIPTION]) or !is_string($itemInfo[self::DESCRIPTION]))
            throw new Mercantile_Exception('Item description not string, is ' . gettype($itemInfo[self::DESCRIPTION]));

        $this->documentElement->appendChild(new DOMElement('item-description', $itemInfo[self::DESCRIPTION]));
		$this->_data[self::DESCRIPTION] = $itemInfo[self::DESCRIPTION];

		// price parsing
		if (!isset($itemInfo[self::PRICE]) ||
			!is_float((float)($itemInfo[self::PRICE]))) {
            throw new Mercantile_Exception('Item unit-price not float, is ' . gettype($itemInfo[self::PRICE]));
		}

        $price = $this->documentElement->appendChild(new DOMElement('unit-price', $itemInfo[self::PRICE]));
		$this->_data[self::PRICE] = $itemInfo[self::PRICE];
        $price->setAttribute('currency', 'USD');

        if (!isset($itemInfo[self::QUANTITY]) or !is_int($itemInfo[self::QUANTITY]))
            throw new Mercantile_Exception('Item quantity not integer, is ' . gettype($itemInfo[self::QUANTITY]));

        $this->documentElement->appendChild(new DOMElement('quantity', $itemInfo[self::QUANTITY]));
		$this->_data[self::QUANTITY] = $itemInfo[self::QUANTITY];
    }

    public function __toString()
    {
        return $this->saveXML($this->documentElement);
    }

	public function toArray()
	{
		return $this->_data;
	}

    /**
     * Set unit-price and currency
     */
    public function setPrice($price = null, $currency = 'USD')
    {
        // @TODO: make this check for valid currency per ISO-4217 (see doc)
        if (!is_float($price))
            throw new Mercantile_Exception('Item price is not float, is ' . gettype($price));
    }

	public function getMerchantItemId()
	{
		return $this->_data[self::MERCHANT_ITEM_ID];
	}

	public function getName()
	{
		return $this->_data[self::NAME];
	}

	public function getDescription()
	{
		return $this->_data[self::DESCRIPTION];
	}

	public function getPrice()
	{
		return $this->_data[self::PRICE];
	}

	public function getQuantity()
	{
		return $this->_data[self::QUANTITY];
	}

	static public function create(DomElement $element)
	{
		$new = get_class($this);

		$merchantItemId = $element->getElementsByTagName(self::MERCHANT_ITEM_ID);
		
		if ($merchantItemId->length) {
			$merchantItemId = $merchantItemId->item(0)->textContent;
		} else {
			$merchantItemId = null;
		}

		$name = $element->getElementsByTagName('item-name')
						->item(0)
						->textContent;

		$description = $element->getElementsByTagName('item-description')
							   ->item(0)
							   ->textContent;

		$quantity = $element->getElementsByTagName('quantity')
							->item(0)
							->textContent;

		$price = $element->getElementsByTagName('unit-price')
						 ->item(0)
						 ->textContent;

		$data = array(
			self::MERCHANT_ITEM_ID => $merchantItemId,
			self::NAME => $name,
			self::DESCRIPTION => $description,
			self::PRICE => $price,
			self::QUANTITY => (int) $quantity,
		);

		return new Mercantile_Gateways_GCheckout_Item($data);
	}
}
