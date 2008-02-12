<?php
/**
 * Interprets XML responses from Google
 */
class Mercantile_GCheckout_Response
{
    protected $_messages = null;

    public function __construct($xml = null)
    {
        if (is_string($xml) == false or isset($xml) == false)
            throw new Mercantile_Exception('$xml must be string, is ' . gettype($xml));
        
        $xmlObj = new SimpleXMLElement($xml);

        $this->_setMessage('response', $xmlObj->getName());
    }
    protected function _setMessage($key, $value)
    {
        $this->_messages[$key] = $value;
    }
    public function getMessages()
    {
        return $this->_messages;
    }
}
