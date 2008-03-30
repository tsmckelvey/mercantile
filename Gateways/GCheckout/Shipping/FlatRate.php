<?php
class Mercantile_Gateways_GCheckout_Shipping_FlatRate extends Mercantile_Gateways_GCheckout_Shipping
{
    const FLAT_RATE_SHIPPING = 'flat-rate-shipping';

    const PRICE = 'price';

    const CURRENCY = 'currency';

    const SHIPPING_RESTRICTIONS = 'shipping-restrictions';

    // option
    const US_STATE_AREA = 'us-state-area';

    // option
    const US_ZIP_AREA = 'us-zip-area';

    // option
    const US_COUNTRY_AREA = 'us-country-area';

    // option
    const STATE = 'state';

    // option 
    const ZIP = 'zip';

    // option
    const ZIP_PATTERN = 'zip-pattern';

    // option
    const COUNTRY_AREA = 'country-area';

    // option
    const COUNTRY_CODE = 'country-code';

    // option
    const POSTAL = 'code';

    // option
    const POSTAL_AREA = 'postal-area';

    // option
    const POSTAL_CODE_PATTERN = 'postal-code-pattern';

    // option
    const WORLD = 'world';

    // option
    const WORLD_AREA = 'world-area';

    const ALLOWED_AREAS = 'allowed-areas';

    const EXCLUDED_AREAS = 'excluded-areas';

    private $_restrictions = array(
        self::ALLOWED_AREAS,
        self::EXCLUDED_AREAS,
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

        $this->formatOutput = true;

        $this->appendChild(new DomElement(self::FLAT_RATE_SHIPPING));

        $this->documentElement->setAttribute('name', $name);

        // @TODO: currency USD, does it BELONG HERE? to be continued ...
        $this->documentElement->appendChild(new DomElement(self::PRICE, $price))
                              ->setAttribute(self::CURRENCY, 'USD');
    }

    public function __toString()
    {
        return $this->saveXML($this->documentElement);
    }

    protected function _setupShippingRestrictions()
    {
        if ($this->getElementsByTagName(self::SHIPPING_RESTRICTIONS)->length < 1)
            $this->firstChild->appendChild(new DomElement(self::SHIPPING_RESTRICTIONS));

        return $this->getElementsByTagName(self::SHIPPING_RESTRICTIONS)->item(0);
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
                
            // @TODO: change to constants or something
            if ($rule == 'exc') {
                $rule = self::EXCLUDED_AREAS;
            } elseif ($rule == 'all') {
                $rule = self::ALLOWED_AREAS;
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
                case self::STATE:
                    // @TODO: duplicates???
                    $stateRules = $restriction->getElementsByTagName(self::US_STATE_AREA);

                    if ($stateRules->length < 1) {
                        $restriction->appendChild(new DomElement(self::US_STATE_AREA))
                                    ->appendChild(new DomElement(self::STATE, $value));
                    } else {
                        $stateRules->item(0)->appendChild(new DomElement(self::STATE, $value));
                    }
                break;
                case self::ZIP:
                    // @TODO: duplicates???
                    $zipRules = $restriction->getElementsByTagName(self::US_ZIP_AREA);

                    if ($zipRules->length < 1) {
                        $restriction->appendChild(new DomElement(self::US_ZIP_AREA))
                                    ->appendChild(new DomElement(self::ZIP_PATTERN));
                    } else {
                        $zipRules->item(0)->appendChild(new DomElement(self::ZIP_PATTERN, $value));
                    }
                break;
                case self::COUNTRY_AREA:
                    $countryRules = $restriction->getElementsByTagName(self::US_COUNTRY_AREA);

                    if (in_array($value, parent::$_countryAreas)) {
                        if ($countryRules->length < 1) {
                            $restriction->appendChild(new DomElement(self::US_COUNTRY_AREA))
                                        ->setAttribute(self::COUNTRY_AREA, $value);
                        } else {
                            $countryRules->item(0)->setAttribute(self::COUNTRY_AREA, $value);
                        }
                    }
                break;
                case self::COUNTRY_CODE:
                    // @TODO: add ISO 3166 checking
                    // @TODO: duplicates???
                    $postalRules = $restriction->getElementsByTagName(self::POSTAL_AREA);

                    if ($postalRules->length < 1) {
                        $restriction->appendChild(new DomElement(self::POSTAL_AREA))
                                    ->appendChild(new DomElement(self::COUNTRY_CODE, $value));
                    } else {
                        $postalRules->item(0)->appendChild(new DomElement(self::COUNTRY_CODe, $value));
                    }
                break;
                case self::POSTAL:
                    // @TODO: duplicates???
                    $postalRules = $restriction->getElementsByTagName(self::POSTAL_AREA);

                    if ($postalRules->length < 1) {
                        $restriction->appendChild(new DomElement(self::POSTAL_AREA))
                                    ->appendChild(new DomElement(self::POSTAL_CODE_PATTERN, $value));
                    } else {
                        $postalRules->item(0)->appendChild(new DomElement(self::POSTAL_CODE_PATTERN, $value));
                    }
                break;
                case self::WORLD:
                    if ($rule == self::ALLOWED_AREAS) {
                        $worldRule = $restriction->getElementsByTagName(self::WORLD_AREA);

                        if ($worldRule->length < 1)
                            $restriction->appendChild(new DOMElement(self::WORLD_AREA));
                    }
                break;
            }
        }
    }
}
