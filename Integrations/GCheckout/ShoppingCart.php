<?php
/**
 * Create, manage, and manipulate a Google Checkout
 * shopping-cart DOM node.  Wrapper class for DOMDocument
 * implementation of Google's shopping-cart API element.
 *
 * @package Mercantile_Integrations
 * @subpackage GCheckout
 */
class Mercantile_Integrations_GCheckout_ShoppingCart
{
    /**
     * The DOMDocument container of shopping-cart,
     * needed to append nodes
     */
    protected $_cartDocument = null;

    /**
     * shopping-cart node object
     */
    protected $_cartElement = null;

    /**
     * Root items node
     */
    protected $_itemsNode = null;

    /**
     * Create a GCheckout_ShoppingCart object
     *
     */
    public function __construct()
    {
        $this->_cartDocument = new DOMDocument('1.0', 'utf-8');

        $this->_cartElement = $this->_cartDocument->createElement('shopping-cart');

        $this->_itemsNode = $this->_cartElement->appendChild(new DOMElement('items'));
    }

    public function __toString()
    {
        return $this->_cartDocument->saveXML($this->_cartElement);
    }

    /**
     * Add a GCheckout_Item to the items
     *
     */
    public function addItem(DOMElement $item = null)
    {
        // import node and children
        $itemElement = $this->_cartDocument->importNode($item, $deep = true);
         
        if ($itemElement->tagName == 'item') {
            $this->_itemsNode->appendChild($itemElement);
            return true;
        } else {
            throw new Mercantile_Exception('Item tag name not \'item\', is ' . $itemElement->tagName);
        }
    }

    public function getShoppingCart()
    {
        return $this->_cartElement;
    }
}
