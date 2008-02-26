<?php
class Mercantile_Gateways_GCheckout_Shipping_FlatRate extends Mercantile_Gateways_GCheckout_Shipping
{
    const FLAT_RATE_SHIPPING = 'flat-rate-shipping';

    private $_rootElement = null;

    private $_restrictions = array(
        'allowed-areas',
        'excluded-areas'
        );

    // @TODO: add validation
    public function __construct($name = null, $price = null)
    {
        if (!is_string($name))
            throw new Mercantile_Exception('Name must be string, is ' . gettype($name));

        // @TODO: deprecate this with Mercantile_Money
        if (!is_int($price))
            throw new Mercantile_Exception('Price not int, is ' . gettype($price));

        parent::__construct();

        $this->_rootElement = $this->appendChild(new DOMElement( self::FLAT_RATE_SHIPPING ));

        $this->_rootElement->setAttribute('name', $name);

        $this->getElementsByTagName( self::FLAT_RATE_SHIPPING )->item(0)
             ->appendChild(new DOMElement('price', $price))->setAttribute('currency', 'USD');
    }

    public function __toString()
    {
        $this->formatOutput = true;

        return $this->saveXML();
    }

    public function getRoot()
    {
        return $this->_rootElement;
    }

    public function saveXML()
    {
        return parent::saveXML($this->_rootElement);
    }

    protected function _setupShippingRestrictions()
    {
        if ($this->getElementsByTagName('shipping-restrictions')->length < 1)
            $this->firstChild->appendChild(new DOMElement('shipping-restrictions'));

        return $this->getElementsByTagName('shipping-restrictions')->item(0);
    }
    
    /**
     * Add a shipping restriction
     *
     * @param array $area An array
     */
    public function addShippingRestriction($areas = null, $allowPOBox = false)
    {
        if (!is_array($areas))
            throw new Mercantile_Exception('Area not array, is ' . gettype($areas));

        foreach ($areas as $key => $areaData) {
            $rule = substr($key, 0, 3);
                
            if ($rule == 'exc') {
                $rule = 'excluded-areas';
            } elseif ($rule == 'all') {
                $rule = 'allowed-areas';
            } else {
                throw new Mercantile_Exception('Unavailable area ' . $rule);
            }
        }

        // insert allow-us-po-box
        $allowPOBox = ($allowPOBox) ? 'true' : 'false';


        // initialize $restriction as the allowed/excluded area in question
        if ($this->getElementsByTagName($rule)->length < 1) {
            $restriction = $this->_setupShippingRestrictions()
                                ->appendChild(new DOMElement($rule));
        } else {
            $restriction = $this->getElementsByTagName($rule)->item(0);
        }

        foreach ($areas[$key] as $key => $value) {
            switch ($key) {
                case 'state':
                    // @TODO: duplicates???
                    $stateRules = $restriction->getElementsByTagName('us-state-area');

                    if ($stateRules->length < 1) {
                        $restriction->appendChild(new DOMElement('us-state-area'))
                                    ->appendChild(new DOMElement('state', $value));
                    } else {
                        $stateRules->item(0)->appendChild(new DOMElement('state', $value));
                    }
                break;
                case 'zip':
                    // @TODO: duplicates???
                    $zipRules = $restriction->getElementsByTagName('us-zip-area');

                    if ($zipRules->length < 1) {
                        $restriction->appendChild(new DOMElement('us-zip-area'))
                                    ->appendChild(new DOMElement('zip-pattern', $value));
                    } else {
                        $zipRules->item(0)->appendChild(new DOMElement('zip-pattern', $value));
                    }
                break;
                case 'country-area':
                    $countryRules = $restriction->getElementsByTagName('us-country-area');

                    if (in_array($value, parent::$_countryAreas)) {
                        if ($countryRules->length < 1) {
                            $restriction->appendChild(new DOMElement('us-country-area'))->setAttribute('country-area', $value);
                        } else {
                            $countryRules->item(0)->setAttribute('country-area', $value);
                        }
                    }
                break;
                case 'country-code':
                    // @TODO: add ISO 3166 checking
                    // @TODO: duplicates???
                    $postalRules = $restriction->getElementsByTagName('postal-area');

                    if ($postalRules->length < 1) {
                        $restriction->appendChild(new DOMElement('postal-area'))
                                    ->appendChild(new DOMElement('country-code', $value));
                    } else {
                        $postalRules->item(0)->appendChild(new DOMElement('country-code', $value));
                    }
                break;
                case 'postal':
                    // @TODO: duplicates???
                    $postalRules = $restriction->getElementsByTagName('postal-area');

                    if ($postalRules->length < 1) {
                        $restriction->appendChild(new DOMElement('postal-area'))
                                    ->appendChild(new DOMElement('postal-code-pattern', $value));
                    } else {
                        $postalRules->item(0)->appendChild(new DOMElement('postal-code-pattern', $value));
                    }
                break;
                case 'world':
                    if ($rule == 'allowed-areas') {
                        $worldRule = $restriction->getElementsByTagName('world-area');

                        if ($worldRule->length < 1)
                            $restriction->appendChild(new DOMElement('world-area'));
                    }
                break;
            }
        }
    }
}
