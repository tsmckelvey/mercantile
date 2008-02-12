<?php
/**
 * Wrapper class for SimpleXMLElement
 */

class Mercantile_GCheckout_ShoppingCart
{
    // the root checkout-shopping-cart
    private $_root = null;

    // items node inside shopping-cart
    private $_items = null;

    // checkout-flow-support node
    private $_checkout = null;

    public function __construct()
    {
        $base = '<?xml version="1.0" encoding="utf-8"?><checkout-shopping-cart xmlns="http://checkout.google.com/schema/2"/>';

        $this->_root = new SimpleXMLElement($base);

        $this->_items = $this->_root->addChild('shopping-cart')
                                    ->addChild('items');

        $this->_shippingMethods = $this->_root->addChild('checkout-flow-support')
                                              ->addChild('merchant-checkout-flow-support')
                                              ->addChild('shipping-methods');
    }

    /**
     * Returns xml string from SimpleXMLElement, really just
     * a wrapper
     */
    public function asXml()
    {
        return $this->_root->asXml();
    }

    /**
     * Add an item to the shopping cart
     */
    public function addItem(array $properties = array())
    {
        $item = $this->_items->addChild('item');

        if ($properties['name']) 
            $item->{'item-name'} = $properties['name'];

        if ($properties['description'])
            $item->{'item-description'} = $properties['description'];

        if ($properties['unit_price'])
            $item->{'unit-price'} = $properties['unit_price'];

        if ($properties['quantity'])
            $item->{'quantity'} = $properties['quantity'];

        // aka SKU
        if ($properties['value'])
            $item->{'value'} = $properties['value'];

        /* @TODO: this is a whole other bucket
        if ($properties['tax'])
            $item->{'alternate-tax-table'} = $properties['tax'];
        */

        // #@TODO: digital-delivery good flag

        // @TODO: google allows other "proprietary" info, look into it

        return $item;
    }

    /**
     * Customize checkout-flow-support
     */
# @TODO: this is a HUGE IMPLEMENTATION :|
}
