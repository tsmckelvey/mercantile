<?php
/**
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
abstract class Mercantile_Gateways_GCheckout_Shipping extends DOMDocument implements Mercantile_Gateways_GCheckout_Shipping_Interface
{
    static protected $_countryAreas = array(
        'CONTINENTAL_48',
        'FULL_50_STATES',
        'ALL'
        );
}
