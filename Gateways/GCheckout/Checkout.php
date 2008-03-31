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
    // http://code.google.com/apis/checkout/checkout_xml.html
    const CHECKOUT_XML_SCHEMA    = 'http://checkout.google.com/schema/2';

    // Checkout request root element
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_checkout-shopping-cart
    const CHECKOUT_SHOPPING_CART = 'checkout-shopping-cart';
    
    // Shopping-cart container element
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_shopping-cart
    const SHOPPING_CART          = 'shopping-cart';

    // Flow support container element
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_checkout-flow-support
    const CHECKOUT_FLOW_SUPPORT  = 'checkout-flow-support';

    // Flow support container element (redundant?)
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_merchant-checkout-flow-support
    const MERCHANT_CHECKOUT_FLOW_SUPPORT  = 'merchant-checkout-flow-support';

    // Shipping methods container element
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_shipping-methods
    const SHIPPING_METHODS = 'shipping-methods';

    // option 
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_edit-cart-url
    const EDIT_CART_URL              = 'edit-cart-url';

    // option 
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_continue-shopping-url
    const CONTINUE_SHOPPING_URL      = 'continue-shopping-url';

    // option 
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_request-buyer-phone-number
    const REQUEST_BUYER_PHONE_NUMBER = 'request-buyer-phone-number';

    // option
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_platform-id
    const PLATFORM_ID                = 'platform-id';

    // option
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_analytics-data
    const ANALYTICS_DATA             = 'analytics-data';

    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_rounding-policy
    const ROUNDING_POLICY = 'rounding-policy';

    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_rule
    const RULE = 'rule';

    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_mode
    const MODE = 'mode';

    // rounding-policy rules
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_rule
    const PER_LINE = 'PER_LINE';

    const TOTAL = 'TOTAL';

    // rounding-policy modes
    // http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_mode
    const UP = 'UP';

    const DOWN = 'DOWN';

    const CEILING = 'CEILING';

    const HALF_UP = 'HALF_UP';

    const HALF_DOWN = 'HALF_DOWN';

    const HALF_EVEN = 'HALF_EVEN';

    private $_roundingPolicyRules = array(
        self::PER_LINE,
        self::TOTAL,
        );

    private $_roundingPolicyModes = array(
        self::UP,
        self::DOWN,
        self::CEILING,
        self::HALF_UP,
        self::HALF_DOWN,
        self::HALF_EVEN,
        );

    /**
     * merchant-checkout-flow-support element
     */
    private $_flowSupportNode = null;

    /**
     * Optional params passed to constructor
     *
     * These all go into merchant-checkout-flow-support anyways
     */
    private $_optionalParams = array(
        self::EDIT_CART_URL,
        self::CONTINUE_SHOPPING_URL,
        self::REQUEST_BUYER_PHONE_NUMBER,
        self::PLATFORM_ID,
        self::ANALYTICS_DATA,
        // @TODO: rounding policy
        );
    
    /**
     * Instantiate the Checkout object
     *
     * Create the DOMDocument and root element
     */
    public function __construct(array $options = array())
    {
        parent::__construct('1.0', 'utf-8');

        $this->formatOutput = true;

        $this->appendChild(new DomElement(self::CHECKOUT_SHOPPING_CART));

        $this->documentElement->setAttribute('xmlns', self::CHECKOUT_XML_SCHEMA);

        // initialize flow support
        $this->_flowSupportNode = $this->documentElement->appendChild(new DomElement(self::CHECKOUT_FLOW_SUPPORT))
                                                    ->appendChild(new DomElement(self::MERCHANT_CHECKOUT_FLOW_SUPPORT));

        // initialize optional params
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    public function __toString()
    {
        return $this->saveXML($this->documentElement);
    }

    /**
     * Set an option value
     *
     * @param string $option A supported option, listed in $this->_optionalParams
     * @param string $value The value for the option, subject to validation rules
     * @return void
     */
    public function setOption($option = null, $value = null)
    {
        if (!in_array($option, $this->_optionalParams))
            throw new Mercantile_Exception($option . ' not optional parameter');

        // @TODO: validation code below VVV
        switch ($option) {
            case self::EDIT_CART_URL:
                // @TODO: dependancy ... ugh
                if (!Zend_Uri::check($value))
                    throw new Mercantile_Exception($option . '=>' . $value . ' is not well-formed URI');
            break;
            case self::CONTINUE_SHOPPING_URL:
                // @TODO: dependancy ... ugh
                if (!Zend_Uri::check($value))
                    throw new Mercantile_Exception($option . ' => ' . $value . ' is not well-formed URI');
            break;
            case self::REQUEST_BUYER_PHONE_NUMBER:
                if (!is_bool($value))
                    throw new Mercantile_Exception($option . ' => ' . $value . ' is not boolean');
            break;
            case self::PLATFORM_ID:
                if (!is_long($value))
                    throw new Mercantile_Exception($option . ' => ' . $value . ' is not long');
            break;
            case self::ANALYTICS_DATA:
                if (!is_string($value))
                    throw new Mercantile_Exception($option . ' => ' . $value . ' is not string');
            break;
            default:
            break;
        }

        // unset if set
        if ($this->_flowSupportNode->getElementsByTagName($option)->length == 1) {
            $existingOption = $this->_flowSupportNode->getElementsByTagName($option)->item(0);
            unset($existingOption);
        }

        $this->_flowSupportNode->appendChild(new DomElement($option, $value));
    }

    /**
     * Add a DOMElement cart
     *
     * Only one shopping-cart is accepted, if a second setShoppingCart
     * request is made the first one will be overwritten.
     *
     * @param DOMElement|Mercantile_Gateways_GCheckout_ShoppingCart $cart The shopping-cart wrapper
     * @throws Mercantile_Exception
     * @return void
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
     * Add a shipping method
     *
     * @param Mercantile_Gateways_GCheckout_Shipping $method The shipping method to add
     */
    public function addShippingMethod(Mercantile_Gateways_GCheckout_Shipping $method = null)
    {
        $method = $this->importNode($method->documentElement, $deep = true);

        // if method tag name == 
        if ($this->_flowSupportNode->getElementsByTagName(self::SHIPPING_METHODS)->length == 0)
            $this->_flowSupportNode->appendChild(new DomElement(self::SHIPPING_METHODS));

        $this->_flowSupportNode->getElementsByTagName(self::SHIPPING_METHODS)
                               ->item(0)
                               ->appendChild($method);
    }

    /**
     * Set the rounding policy of this cart.
     *
     * Overwrites existing rounding policy.
     *
     * http://code.google.com/apis/checkout/developer/Google_Checkout_XML_API_Tag_Reference.html#tag_rounding-policy
     *
     * @param string $rule Rounding policy rule, subject to $this->_roundingPolicyRules
     * @param string $mode Rounding policy mode, subject to $this->_roundingPolicyModes
     * @throws Mercantile_Exception
     * @return void
     */
    public function setRoundingPolicy($rule = self::TOTAL, $mode = self::HALF_UP)
    {
        if (!in_array($rule, $this->_roundingPolicyRules))
            throw new Mercantile_Exception($rule . ' is not an allowed rounding policy rule');

        if (!in_array($mode, $this->_roundingPolicyModes))
            throw new Mercantile_Exception($mode . ' is not an allowed rounding policy mode');

        if ($this->_flowSupportNode->getElementsByTagName(self::ROUNDING_POLICY)->length == 1) {
            $existingRoundingPolicy = $this->_flowSupportNode->getElementsByTagName(self::ROUNDING_POLICY)->item(0);
            unset($existingRoundingPolicy);
        }

        $roundingPolicyNode = $this->_flowSupportNode->appendChild(new DomElement(self::ROUNDING_POLICY));

        $roundingPolicyNode->appendChild(new DomElement(self::RULE, $rule));

        $roundingPolicyNode->appendChild(new DomElement(self::MODE, $mode));
    }
}
