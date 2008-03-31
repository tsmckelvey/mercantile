<?php
/**
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
abstract class Mercantile_Gateways_GCheckout_Shipping extends DOMDocument
{
    /**
     * name attribute of shipping methods
     */
    const NAME = 'name';

    // carrier calculated shipping method
    const CARRIER_CALCULATED_SHIPPING = 'carrier-calculated-shipping';

    const SHIPPING_PACKAGES = 'shipping-packages';

    const SHIPPING_PACKAGE = 'shipping-package';

    // L/W/H attr
    const VALUE = 'value';

    // L/W/H attr
    const UNIT = 'unit';

    const LENGTH = 'length';

    const WIDTH = 'width';

    const HEIGHT = 'height';

    // ship-from attr
    const ID = 'id';

    const SHIP_FROM = 'ship-from';

    const REGION = 'region';

    const CITY = 'city';

    const POSTAL_CODE = 'postal-code';

    const DELIVERY_ADDRESS_CATEGORY = 'delivery-address-category';

    const CARRIER_CALCULATED_SHIPPING_OPTIONS = 'carrier-calculated-shipping-options';

    const SHIPPING_COMPANY = 'shipping-company';

    const SHIPPING_TYPE = 'shipping-type';

    const CARRIER_PICKUP = 'carrier-pickup';

    const CURRENCY = 'currency';

    const ADDITIONAL_FIXED_CHARGE = 'additional-fixed-charge';

    const ADDITIONAL_VARIABLE_CHARGE_PERCENT = 'additional-variable-charge-percent';

    // pickup shipping method
    const PICKUP = 'pickup';
    
    // merchant calculated shipping method
    const MERCHANT_CALCULATED_SHIPPING = 'merchant-calculated-shipping';

    // address filters, child of mechant-calculated-shipping
    const ADDRESS_FILTERS = 'address-filters';

    // flat rate shipping method
    const FLAT_RATE_SHIPPING = 'flat-rate-shipping';

    const PRICE = 'price';

    const SHIPPING_RESTRICTIONS = 'shipping-restrictions';

    const EXCLUDED_AREAS = 'excluded-areas';

    const ALLOWED_AREAS = 'allowed-areas';

    // state area, state pair
    const US_STATE_AREA = 'us-state-area';

    const STATE = 'state';

    // zip area, pattern pair
    const US_ZIP_AREA = 'us-zip-area';

    const ZIP_PATTERN = 'zip-pattern';

    // us country area
    const US_COUNTRY_AREA = 'us-country-area';

    // us country area attrib
    const COUNTRY_AREA = 'country-area';

    /* country area attribute constants */
    const CONTINENTAL_48 = 'CONTINENTAL_48';

    const FULL_50_STATES = 'FULL_50_STATES';
    
    const ALL = 'ALL';

    // postal area and children
    const POSTAL_AREA = 'postal-area';

    const COUNTRY_CODE = 'country-code';

    const POSTAL_CODE_PATTERN = 'postal-code-pattern';

    // world area
    const WORLD_AREA = 'world-area';

    // allow-us-po box flag
    const ALLOW_US_PO_BOX = 'allow-us-po-box';

    // @TODO: from here down to the country areas array DOES NOT BELONG IN SHIPPING!
    // parameterized urls
    const PARAMETERIZED_URLS = 'parameterized-urls';

    const PARAMETERIZED_URL = 'parameterized-url';

    // parameterized url attr
    const URL = 'url';

    const PARAMETERS = 'parameters';

    const URL_PARAMETER = 'url-parameter';

    // url-parameter attr
    const TYPE = 'type';

    const MERCHANT_CALCULATIONS = 'merchant-calculations';

    const MERCHANT_CALCULATIONS_URL = 'merchant-calculations-url';

    const ACCEPT_MERCHANT_COUPONS = 'accept-merchant-coupons';
    
    const ACCEPT_GIFT_CERTIFICATESs = 'accept-gift-certificates';

    const REQUEST_BUYER_PHONE_NUMBER = 'request-buyer-phone-number';

    const ANALYTICS_DATA = 'analytics-data';

    const PLATFORM_ID = 'platform-id';

    const ROUNDING_POLICY = 'rounding-policy';

    const MODE = 'mode';

    const RULE = 'rule';

    static protected $_countryAreas = array(
        self::CONTINENTAL_48,
        self::FULL_50_STATES,
        self::ALL,
        );

    static protected $_restrictions = array(
        self::ALLOWED_AREAS,
        self::EXCLUDED_AREAS,
        );
}
