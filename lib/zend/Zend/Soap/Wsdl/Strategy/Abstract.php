<?php

abstract class Zend_Soap_Wsdl_Strategy_Abstract implements Zend_Soap_Wsdl_Strategy_Interface
{
    protected $_context;

    /**
     * Set the Zend_Soap_Wsdl Context object this strategy resides in.
     *
     * @param Zend_Soap_Wsdl $context
     * @return void
     */
    public function setContext(Zend_Soap_Wsdl $context)
    {
        $this->_context = $context;
    }

    /**
     * Return the current Zend_Soap_Wsdl context object
     *
     * @return Zend_Soap_Wsdl
     */
    public function getContext()
    {
        return $this->_context;
    }
}
