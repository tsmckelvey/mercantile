<?php
require_once '../Exception.php';

class Mercantile_Gateway_Response
{
    private $_success = null;

    private $_messages = null;
    
    /**
     * Make a Response object
     * 
     * @param bool $success T/F indicating transaction success
     * @param array $messages Array of return messages
     */
    public function __construct($success = null, array $messages = null, array $params = null)
    {
        $this->_setSuccess($success);

        $this->_setMessages($messages);

        if (is_array($params) == true) {
            $this->_params = $params;
        }
    }

    public function __toString()
    {
        $output = <<<OUT
Success: %s
Messages:
%s
Params:
%s
OUT;
                return sprintf($output, (int)print_r($this->isSuccess(), true),
                                        print_r($this->getMessages(), true),
                                        print_r($this->getParams(), true));
    }

    /**
     * Set success attribute
     */
    private function _setSuccess($success = null)
    {
        if (is_bool($success) == true)
            $this->_success = $success;
        else
            throw new Mercantile_Exception('$success not array, is ' . gettype($success));
    }

    /**
     * Set messages array with relevant error messages
     */
    private function _setMessages(array $messages = null)
    {
        if (is_array($messages) == true) {
            $this->_messages = $messages;

            return;
        } else {
            throw new Mercantile_Exception('$messages not array, is ' . gettype($messages));
        }
    }

    /**
     * Return bool transaction success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->_success;
    }

    /**
     * Optional callback params which may
     * be required for capturing a transaction
     *
     * @return array $_params array of callback params
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Get a specific param
     */
    public function getParam($param = null)
    {
        if (!is_string($param))
            throw new Mercantile_Exception('Param not string, is ' . gettype($param));

        if ($this->_params[$param]) {
            return $this->_params[$param];
        } else {
            return false;
        }
    }

    /**
     * Return messages
     *
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
