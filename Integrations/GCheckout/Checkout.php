<?php

/**
 * GCheckout_Checkout assembles and posts all <checkout-shopping-cart>
 * requests.  A checkout request requires a shopping-cart item at the least.
 */
class Mercantile_Integrations_GCheckout_Checkout
{
    // Google Checkout API Schema
    const CHECKOUT_XML_SCHEMA    = 'http://checkout.google.com/schema/2';

    // Checkout request root element
    const CHECKOUT_SHOPPING_CART = 'checkout-shopping-cart';
    
    // Shopping-cart container element
    const SHOPPING_CART          = 'shopping-cart';

    // Flow support container element
    const CHECKOUT_FLOW_SUPPORT  = 'checkout-flow-support';

    // Flow support container element (redundant?)
    const MERCHANT_CHECKOUT_FLOW_SUPPORT  = 'merchant-checkout-flow-support';

    /**
     * The checkout DOMDocument we operate on
     */
    private $_checkoutDocument = null;

    /**
     * The root node of the document, 'checkout-shopping-cart' within which
     * every element is held
     */
    private $_checkoutShoppingCart = null;

    /**
     * merchant-checkout-flow-support element
     */
    private $_flowSupport = null;

    /**
     * Optional params passed to constructor
     *
     * These all go into merchant-checkout-flow-support anyways
     */
    private $_optionalParams = array(
        'edit-cart-url' => false,
        'continue-shopping-url' => false,
        'request-buyer-phone-number' => false,
        'platform-id' => false,
        'analytics-data' => false
        // , 'parameterized-urls'
        );
    
    /**
     * Instantiate the Checkout object
     *
     * Create the DOMDocument and root element
     */
    public function __construct(array $options = array())
    {
        $this->_checkoutDocument = new DOMDocument('1.0', 'utf-8');        

        $this->_checkoutShoppingCart = $this->_checkoutDocument
                                            ->createElement( self::CHECKOUT_SHOPPING_CART );

        $this->_checkoutDocument
             ->appendChild($this->_checkoutShoppingCart);

        $this->_checkoutShoppingCart->setAttribute('xmlns', self::CHECKOUT_XML_SCHEMA);

        foreach ($options as $option => $value) {
            if (array_key_exists($option, $this->_optionalParams) == true) {
                $this->_optionalParams[$option] = $value;

                $this->_setupCheckoutFlowSupport();

                // need to add validation
                // edit-cart-url and continue-shopping-url must URL validate
                $this->_flowSupport->appendChild(new DOMElement($option, $value));
            }
        }
    }

    public function __toString()
    {
        $this->_checkoutDocument->formatOutput = true;

        return $this->_checkoutDocument->saveXML();
    }
    
    protected function _setupCheckoutFlowSupport()
    {
        if ($this->_flowSupport == null) {
            $checkoutFlow = $this->_checkoutShoppingCart
                                 ->appendChild(new DOMElement( self::CHECKOUT_FLOW_SUPPORT ));

            $this->_flowSupport = $checkoutFlow->appendChild(new DOMElement( self::MERCHANT_CHECKOUT_FLOW_SUPPORT ));
        }

        return $this->_flowSupport;
    }
    /**
     * Add a DOMElement cart
     *
     * Only one shopping-cart is accepted, if a second setShoppingCart
     * request is made the first one will be overwritten.
     *
     * @param DOMElement|Mercantile_Integrations_GCheckout_ShoppingCart $cart The shopping-cart wrapper
     */
    public function setShoppingCart($cart = null)
    {
        if (get_class($cart) == 'DOMElement') {
        } elseif (get_class($cart) == 'Mercantile_Integrations_GCheckout_ShoppingCart') {
            $cart = $cart->getShoppingCart();
        } else {
            throw new Mercantile_Exception('Cart wrong type, is ' . get_class($cart));
        }

        // import into document, required by spec
        $cartElement = $this->_checkoutDocument->importNode($cart, $deep = true);

        if ($cartElement->tagName == self::SHOPPING_CART) {
            // number of items
            $itemCount = $cartElement->getElementsByTagName('items')->item(0)
                                     ->getElementsByTagName('item')->length;

            if ($itemCount < 1)
                throw new Mercantile_Exception('Shopping cart must have atleast one item, has ' . $itemCount);

            // check for existing shpping-cart ... there can only be one ...
            if ($this->_checkoutShoppingCart->getElementsByTagName( self::SHOPPING_CART )->length > 0) {
                $existingCart = $this->_checkoutShoppingCart->getElementsByTagName( self::SHOPPING_CART )->item(0);
                unset($existingCart);
            }

            $this->_checkoutShoppingCart->appendChild($cartElement);

            return true;
        } else {
            throw new Mercantile_Exception('Shopping cart not \'shopping-cart\', is ' . $cartElement->tagName);
        }
    }

    /**
     *
     */
    public function setShippingMethod(Mercantile_Integrations_GCheckout_Shipping $method = null)
    {
        $method = $this->_checkoutDocument->importNode($method->getRoot(), $deep = true);

        // if method tag name == 
        $this->_setupCheckoutFlowSupport()
             ->appendChild(new DOMElement('shipping-methods'))
             ->appendChild($method);

        return true;
    }

    public function getCheckout()
    {
        return $this->_checkoutShoppingCart;
    }
}
