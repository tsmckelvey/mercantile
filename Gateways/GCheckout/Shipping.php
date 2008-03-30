<?php
/**
 * @package Mercantile_Gateways
 * @subpackage GCheckout
 */
abstract class Mercantile_Gateways_GCheckout_Shipping extends DOMDocument
{
    const CONTINENTAL_48 = 'CONTINENTAL_48';

    const FULL_50_STATES = 'FULL_50_STATES';
    
    const ALL = 'ALL';

    static protected $_countryAreas = array(
        self::CONTINENTAL_48,
        self::FULL_50_STATES,
        self::ALL,
        );
}
