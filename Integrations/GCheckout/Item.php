<?php
/**
 * Class for wrapping a DOMElement object with which we
 * can easily custom-tailor our items per the GCheckout API
 */
class Mercantile_Integrations_GCheckout_Item
{
    protected $_itemDocument = null;

    protected $_itemNode = null;

    //protected $_merchItemId = null;

    //protected $_taxTableSelector = null;

    //protected $_digitalContent = null;

    //protected $_merchPrivateItemData = null;

    public function __construct(array $itemInfo = null)
    {
        if (is_null($itemInfo))
            throw new Mercantile_Exception('Item info not array, is ' . gettype($itemInfo));

        // begin assembling item
        $this->_itemDocument = new DOMDocument('1.0', 'utf-8');

        $this->_itemNode = $this->_itemDocument->createElement('item');

        if (!isset($itemInfo['name']) or !is_string($itemInfo['name']))
            throw new Mercantile_Exception('Item name not string, is ' . gettype($itemInfo['name']));

        $this->_itemNode->appendChild(new DOMElement('item-name', $itemInfo['name']));

        if (!isset($itemInfo['description']) or !is_string($itemInfo['description']))
            throw new Mercantile_Exception('Item description not string, is ' . gettype($itemInfo['description']));

        $this->_itemNode->appendChild(new DOMElement('item-description', $itemInfo['description']));

        if (!isset($itemInfo['price']) or !is_float($itemInfo['price']))
            throw new Mercantile_Exception('Item unit-price not float, is ' . gettype($itemInfo['price']));

        $price = $this->_itemNode->appendChild(new DOMElement('unit-price', $itemInfo['price']));
        $price->setAttribute('currency', 'USD');

        if (!isset($itemInfo['quantity']) or !is_int($itemInfo['quantity']))
            throw new Mercantile_Exception('Item quantity not integer, is ' . gettype($itemInfo['quantity']));

        $this->_itemNode->appendChild(new DOMElement('quantity', $itemInfo['quantity']));
    }

    public function __toString()
    {
        return $this->_itemDocument->saveXML($this->_itemNode);
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

    /**
     * Returns the DOMNode of our item
     *
     * @return DOMNode Object of item
     */
    public function getItem()
    {
        return $this->_itemNode;
    }
}
