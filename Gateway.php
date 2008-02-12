<?php
abstract class Mercantile_Gateway 
{
    /**
     * Factory method for instantiating an unknown gateway
     *
     * @param string $gateway the name of the gateway
     * @param array $gatewayCreds a user's relative gateway API credentials
     * @throws Mercantile_Exception
     * @return Mercantile_Gateway
     */
    static public function factory($gateway = null, array $gatewayCreds = null)
    {
        if (substr($gateway, 0, 11) != 'Mercantile_') 
            $gateway = 'Mercantile_' . $gateway; 

        if (class_exists($gateway) == true) {
            return new $gateway($gatewayCreds);
        } else {
            throw new Mercantile_Exception("$gateway class not found");
        }
    }
}
