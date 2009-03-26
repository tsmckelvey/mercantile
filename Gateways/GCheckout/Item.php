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

    public function __construct(array $itemInfo = null)
    {
        parent::__construct('1.0', 'utf-8');

        $this->formatOutput = true;

        $this->appendChild(new DomElement('item'));

        if (is_null($itemInfo))
            throw new Mercantile_Exception('Item info not array, is ' . gettype($itemInfo));

		if (isset($itemInfo[self::MERCHANT_ITEM_ID])) {
			$this->documentElement->appendChild(new DOMElement(self::MERCHANT_ITEM_ID, $itemInfo[self::MERCHANT_ITEM_ID]));
		}

        if (!isset($itemInfo[self::NAME]) or !is_string($itemInfo[self::NAME]))
            throw new Mercantile_Exception('Item name not string, is ' . gettype($itemInfo[self::NAME]));

        $this->documentElement->appendChild(new DOMElement('item-name', $itemInfo[self::NAME]));

        if (!isset($itemInfo[self::DESCRIPTION]) or !is_string($itemInfo[self::DESCRIPTION]))
            throw new Mercantile_Exception('Item description not string, is ' . gettype($itemInfo[self::DESCRIPTION]));

        $this->documentElement->appendChild(new DOMElement('item-description', $itemInfo[self::DESCRIPTION]));

		// price parsing
		if (!isset($itemInfo[self::PRICE]) ||
			!is_float((float)($itemInfo[self::PRICE]))) {
            throw new Mercantile_Exception('Item unit-price not float, is ' . gettype($itemInfo[self::PRICE]));
		}

        $price = $this->documentElement->appendChild(new DOMElement('unit-price', $itemInfo[self::PRICE]));
        $price->setAttribute('currency', 'USD');

        if (!isset($itemInfo[self::QUANTITY]) or !is_int($itemInfo[self::QUANTITY]))
            throw new Mercantile_Exception('Item quantity not integer, is ' . gettype($itemInfo[self::QUANTITY]));

        $this->documentElement->appendChild(new DOMElement('quantity', $itemInfo[self::QUANTITY]));
    }

    public function __toString()
    {
        return $this->saveXML($this->documentElement);
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
}
