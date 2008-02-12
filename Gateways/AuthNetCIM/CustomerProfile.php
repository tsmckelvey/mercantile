<?php
/**
 * CustomerProfile class which acts as a container
 * for a client's information
 */
class Mercantile_Gateways_AuthNetCIM_CustomerProfile
{
    /**
     * Container for SimpleXMLElement profile object
     */
    private $_profile = null;

    private $_merchantCustomerId = null;

    private $_description = null;

    private $_email = null;

    private $_customerProfileId = null;

    /**
     *
     * @param string|SimpleXMLElement $xmlProfile
     */
    public function __construct($xmlProfile = null)
    {
        if (is_string($xmlProfile)){
            $this->_profile = new SimpleXMLElement($xmlProfile);
        } elseif ($xmlProfile instanceof SimpleXMLElement) {
            $this->_profile = $xmlProfile;
        } else {
            throw new Mercantile_Exception('$xmlProfile incorrect type, is ' . gettype($xmlprofile));
        }

        if ($this->_profile->getName() != 'profile')
            throw new Mercantile_Exception('Incorrect root node: ' . $this->_profile->getName());

        if ($this->_profile->merchantCustomerId)
            $this->_merchantCustomerId = (string)$this->_profile->merchantCustomerId;

        if ($this->_profile->description)
            $this->_description = (string)$this->_profile->description;

        if ($this->_profile->email)
            $this->_email = (string)$this->_profile->email;

        if ($this->_profile->customerProfileId)
            $this->_customerProfileId = (string)$this->_profile->customerProfileId;

    }
    
    /**
     * Alias of asXml()
     */
    public function getProfile()
    {
        return $this->asXml();
    }

    /**
     * Returns XML string
     * Strips stupid header xml
     */
    public function asXml()
    {
        $xml = $this->_profile->asXml();

        $xml = str_replace('<?xml version="1.0"?>', '', $xml);

        return $xml;
    }

    public function getMerchantCustomerId()
    {
        return $this->_merchantCustomerId;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function getProfileId()
    {
        return $this->_customerProfileId;
    }

    /**
     * Add payment profile
     */
    public function addPaymentProfile()
    {
        
    }
}
