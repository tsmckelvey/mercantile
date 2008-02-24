<?php
/**
 * @package Mercantile_Integrations
 * @subpackage GCheckout
 */
abstract class Mercantile_Integrations_GCheckout_Shipping extends DOMDocument implements Mercantile_Integrations_GCheckout_Shipping_Interface
{
    static protected $_countryAreas = array(
        'CONTINENTAL_48',
        'FULL_50_STATES',
        'ALL'
        );
}
