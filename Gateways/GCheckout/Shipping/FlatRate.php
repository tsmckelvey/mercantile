<?php
class Mercantile_Gateways_GCheckout_Shipping_FlatRate extends Mercantile_Gateways_GCheckout_Shipping
{
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

    /**
     * Set the shipping restrictions for this shipping method
     *
     * Overwrites existing shipping-restrictions
     *
     * @param array $area A complex-type array of restriction areas
     * @param bool $allowPOBox True/false value of allowPOBox option
     * @throws Mercantile_Exception
     * @return void
     */
    public function setShippingRestrictions(array $restrictions = null, $allowPOBox = false)
    {
        if (!is_array($restrictions))
            throw new Mercantile_Exception('$area not array, is ' . gettype($restrictions));

        foreach ($restrictions as $restriction => $areaData) {
            if (!in_array($restriction, parent::$_restrictions)) {
                throw new Mercantile_Exception($restriction . ' is not allowed shipping-restriction');
            }
        }

        if (!array_key_exists(self::ALLOWED_AREAS, $restrictions))
            throw new Mercantile_Exception(self::ALLOWED_AREAS . ' is required');

        // create root node of shipping restrictions
        if ($this->getElementsByTagName(self::SHIPPING_RESTRICTIONS)->length > 0) {
            $currentRestrictionsNode =& $this->getElementsByTagName(self::SHIPPING_RESTRICTIONS)->item(1);
            unset($currentRestrictionsNode);
        }

        $restrictionsNode = $this->documentElement->appendChild(new DomElement(self::SHIPPING_RESTRICTIONS));

        // insert allow-us-po-box
        $restrictionsNode->appendChild(new DomElement(self::ALLOW_US_PO_BOX, ($allowPOBox) ? 'true' : 'false'));

        foreach ($restrictions as $restriction => $area) {
            $restrictionNode = $restrictionsNode->appendChild(new DomElement($restriction));

            if (!is_array($area))
                throw new Mercantile_Exception($restriction . ' must be array, is ' . gettype($restriction));

            foreach ($area as $key => $value) {
                switch ($key) {
                    case self::STATE:
                        $stateRules = $restrictionNode->getElementsByTagName(self::US_STATE_AREA);

                        if ($stateRules->length < 1) {
                            $restrictionNode->appendChild(new DomElement(self::US_STATE_AREA))
                                        ->appendChild(new DomElement(self::STATE, $value));
                        } else {
                            $stateRules->item(0)->appendChild(new DomElement(self::STATE, $value));
                        }
                    break;
                    case self::ZIP_PATTERN:
                        // @TODO: duplicates???
                        $zipRules = $restrictionNode->getElementsByTagName(self::US_ZIP_AREA);

                        if ($zipRules->length < 1) {
                            $restrictionNode->appendChild(new DomElement(self::US_ZIP_AREA))
                                        ->appendChild(new DomElement(self::ZIP_PATTERN, $value));
                        } else {
                            $zipRules->item(0)->appendChild(new DomElement(self::ZIP_PATTERN, $value));
                        }
                    break;
                    case self::COUNTRY_AREA:
                        $countryRules = $restrictionNode->getElementsByTagName(self::US_COUNTRY_AREA);

                        if (in_array($value, parent::$_countryAreas)) {
                            if ($countryRules->length < 1) {
                                $restrictionNode->appendChild(new DomElement(self::US_COUNTRY_AREA))
                                            ->setAttribute(self::COUNTRY_AREA, $value);
                            } else {
                                $countryRules->item(0)->setAttribute(self::COUNTRY_AREA, $value);
                            }
                        }
                    break;
                    case self::COUNTRY_CODE:
                        // @TODO: add ISO 3166 checking
                        // @TODO: duplicates???
                        $postalRules = $restrictionNode->getElementsByTagName(self::POSTAL_AREA);

                        if ($postalRules->length < 1) {
                            $restrictionNode->appendChild(new DomElement(self::POSTAL_AREA))
                                        ->appendChild(new DomElement(self::COUNTRY_CODE, $value));
                        } else {
                            $postalRules->item(0)->appendChild(new DomElement(self::COUNTRY_CODe, $value));
                        }
                    break;
                    case self::POSTAL_AREA:
                        // @TODO: duplicates???
                        $postalRules = $restrictionNode->getElementsByTagName(self::POSTAL_AREA);

                        if ($postalRules->length < 1) {
                            $restrictionNode->appendChild(new DomElement(self::POSTAL_AREA))
                                        ->appendChild(new DomElement(self::POSTAL_CODE_PATTERN, $value));
                        } else {
                            $postalRules->item(0)->appendChild(new DomElement(self::POSTAL_CODE_PATTERN, $value));
                        }
                    break;
                    case self::WORLD_AREA:
                        if ($rule == self::ALLOWED_AREAS) {
                            $worldRule = $restrictionNode->getElementsByTagName(self::WORLD_AREA);

                            if ($worldRule->length < 1)
                                $restrictionNode->appendChild(new DOMElement(self::WORLD_AREA));
                        }
                    break;
                    default:
                    break;
                }
            }
        }
    }
}
