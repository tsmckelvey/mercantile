<?php
/**
 * GCheckout_Checkout assembles all <checkout-shopping-cart> requests.  
 * A checkout request requires a shopping-cart item at the least.
 *
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
class Mercantile_Gateways_GCheckout_Checkout extends DomDocument
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

    // Shipping methods container element
    const SHIPPING_METHODS = 'shipping-methods';

    // option 
    const EDIT_CART_URL              = 'edit-cart-url';

    // option 
    const CONTINUE_SHOPPING_URL      = 'continue-shopping-url';

    // option 
    const REQUEST_BUYER_PHONE_NUMBER = 'request-buyer-phone-number';

    // option
    const PLATFORM_ID                = 'platform-id';

    // option
    const ANALYTICS_DATA             = 'analytics-data';

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
        self::EDIT_CART_URL => false,
        self::CONTINUE_SHOPPING_URL => false,
        self::REQUEST_BUYER_PHONE_NUMBER => false,
        self::PLATFORM_ID => false,
        self::ANALYTICS_DATA => false,
        // @TODO:, 'parameterized-urls'
        );
    
    /**
     * Instantiate the Checkout object
     *
     * Create the DOMDocument and root element
     */
    public function __construct(array $options = array())
    {
        parent::__construct('1.0', 'utf-8');

        $this->appendChild(new DomElement(self::CHECKOUT_SHOPPING_CART));

        $this->documentElement->setAttribute('xmlns', self::CHECKOUT_XML_SCHEMA);

        foreach ($options as $option => $value) {
            if (array_key_exists($option, $this->_optionalParams) == true) {
                $this->_optionalParams[$option] = $value;

                $this->_setupCheckoutFlowSupport();

                // @TODO: need to add validation
                // edit-cart-url and continue-shopping-url must URL validate
                $this->_flowSupport->appendChild(new DOMElement($option, $value));
            }
        }
    }

    // @TODO: setOption
    // @TODO: setOptions

    public function __toString()
    {
        return $this->saveXML($this->documentElement);
    }
    
    protected function _setupCheckoutFlowSupport()
    {
        if ($this->_flowSupport == null) {
            $checkoutFlow = $this->documentElement->appendChild(new DomElement(self::CHECKOUT_FLOW_SUPPORT));

            $this->_flowSupport = $checkoutFlow->appendChild(new DomElement(self::MERCHANT_CHECKOUT_FLOW_SUPPORT));
        }

        return $this->_flowSupport;
    }

    /**
     * Add a DOMElement cart
     *
     * Only one shopping-cart is accepted, if a second setShoppingCart
     * request is made the first one will be overwritten.
     *
     * @param DOMElement|Mercantile_Gateways_GCheckout_ShoppingCart $cart The shopping-cart wrapper
     */
    public function setShoppingCart($cart = null)
    {
        if (get_class($cart) == 'DOMElement') {
        } else if (get_class($cart) == 'Mercantile_Gateways_GCheckout_ShoppingCart') {
            $cart = $cart->documentElement;
        } else {
            throw new Mercantile_Exception('Cart wrong type, is ' . get_class($cart));
        }

        // import into document 
        $cartElement = $this->importNode($cart, $deep = true);

        if ($cartElement->tagName == self::SHOPPING_CART) {
            // number of items
            $itemCount = $cartElement->getElementsByTagName('items')->item(0)
                                     ->getElementsByTagName('item')->length;

            if ($itemCount < 1)
                throw new Mercantile_Exception('Shopping cart must have atleast one item, has ' . $itemCount);

            // check for existing shpping-cart ... there can only be one ...
            if ($this->documentElement->getElementsByTagName(self::SHOPPING_CART)->length > 0) {
                $existingCart = $this->documentElement->getElementsByTagName(self::SHOPPING_CART)->item(0);
                unset($existingCart);
            }

            $this->documentElement->appendChild($cartElement);

            return true;
        } else {
            throw new Mercantile_Exception('Shopping cart not \'shopping-cart\', is ' . $cartElement->tagName);
        }
    }

    /**
     *
     */
    public function setShippingMethod(Mercantile_Gateways_GCheckout_Shipping $method = null)
    {
        // @TODO: getRoot()? wtf is this?
        $method = $this->importNode($method->getRoot(), $deep = true);

        // if method tag name == 
        $this->_setupCheckoutFlowSupport()
             ->appendChild(new DOMElement(self::SHIPPING_METHODS))
             ->appendChild($method);

        return true;
    }

    public function getCheckout()
    {
        return $this->_checkoutShoppingCart;
    }
}
