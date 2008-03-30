<?php
/**
 * Create, manage, and manipulate a Google Checkout
 * shopping-cart DOM node.  Wrapper class for DOMDocument
 * implementation of Google's shopping-cart API element.
 *
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
class Mercantile_Gateways_GCheckout_ShoppingCart extends DomDocument
{
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
        parent::__construct('1.0', 'utf-8');

        $this->appendChild(new DomElement('shopping-cart'));

        $this->_itemsNode = $this->documentElement->appendChild(new DomElement('items'));
    }

    public function __toString()
    {
        return $this->saveXML($this->documentElement);
    }

    /**
     * Add a GCheckout_Item to the items
     *
     */
    public function addItem(DomDocument $item = null)
    {
        // import node and children
        $itemElement = $this->importNode($item->documentElement, $deep = true);
         
        if ($itemElement->tagName == 'item') {
            $this->_itemsNode->appendChild($itemElement);
            return true;
        } else {
            throw new Mercantile_Exception('Item tag name not \'item\', is ' . $itemElement->tagName);
        }
    }
}
