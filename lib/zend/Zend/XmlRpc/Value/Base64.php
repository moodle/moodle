<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_XmlRpc_Value_Scalar
 */
require_once 'Zend/XmlRpc/Value/Scalar.php';


/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_XmlRpc_Value_Base64 extends Zend_XmlRpc_Value_Scalar
{

    /**
     * Set the value of a base64 native type
     * We keep this value in base64 encoding
     *
     * @param string $value
     * @param bool $already_encoded If set, it means that the given string is already base64 encoded
     */
    public function __construct($value, $already_encoded=false)
    {
        $this->_type = self::XMLRPC_TYPE_BASE64;

        $value = (string)$value;    // Make sure this value is string
        if (!$already_encoded) {
            $value = base64_encode($value);     // We encode it in base64
        }
        $this->_value = $value;
    }

    /**
     * Return the value of this object, convert the XML-RPC native base64 value into a PHP string
     * We return this value decoded (a normal string)
     *
     * @return string
     */
    public function getValue()
    {
        return base64_decode($this->_value);
    }

    /**
     * Return the XML code representing the base64-encoded value
     * 
     * @return string
     */
    public function saveXML()
    {
        if (! $this->_as_xml) {   // The XML was not generated yet
            $dom   = new DOMDocument('1.0', 'UTF-8');
            $value = $dom->appendChild($dom->createElement('value'));
            $type  = $value->appendChild($dom->createElement($this->_type));
            $type->appendChild($dom->createTextNode($this->_value));

            $this->_as_dom = $value;
            $this->_as_xml = $this->_stripXmlDeclaration($dom);
        }

        return $this->_as_xml;
    }
}

