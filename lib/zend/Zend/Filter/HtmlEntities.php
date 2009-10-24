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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_HtmlEntities implements Zend_Filter_Interface
{
    /**
     * Corresponds to the second htmlentities() argument
     *
     * @var integer
     */
    protected $_quoteStyle;

    /**
     * Corresponds to the third htmlentities() argument
     *
     * @var string
     */
    protected $_charSet;

    /**
     * Corresponds to the forth htmlentities() argument
     *
     * @var unknown_type
     */
    protected $_doubleQuote;

    /**
     * Sets filter options
     *
     * @param  integer|array $quoteStyle
     * @param  string  $charSet
     * @return void
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            trigger_error('Support for multiple arguments is deprecated in favor of a single options array', E_USER_NOTICE);
            $options = func_get_args();
            $temp['quotestyle'] = array_shift($options);
            if (!empty($options)) {
                $temp['charset'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!isset($options['quotestyle'])) {
            $options['quotestyle'] = ENT_COMPAT;
        }

        if (!isset($options['charset'])) {
            $options['charset'] = 'ISO-8859-1';
        }

        if (!isset($options['doublequote'])) {
            $options['doublequote'] = true;
        }

        $this->setQuoteStyle($options['quotestyle']);
        $this->setCharSet($options['charset']);
        $this->setDoubleQuote($options['doublequote']);
    }

    /**
     * Returns the quoteStyle option
     *
     * @return integer
     */
    public function getQuoteStyle()
    {
        return $this->_quoteStyle;
    }

    /**
     * Sets the quoteStyle option
     *
     * @param  integer $quoteStyle
     * @return Zend_Filter_HtmlEntities Provides a fluent interface
     */
    public function setQuoteStyle($quoteStyle)
    {
        $this->_quoteStyle = $quoteStyle;
        return $this;
    }

    /**
     * Returns the charSet option
     *
     * @return string
     */
    public function getCharSet()
    {
        return $this->_charSet;
    }

    /**
     * Sets the charSet option
     *
     * @param  string $charSet
     * @return Zend_Filter_HtmlEntities Provides a fluent interface
     */
    public function setCharSet($charSet)
    {
        $this->_charSet = $charSet;
        return $this;
    }

    /**
     * Returns the doubleQuote option
     *
     * @return boolean
     */
    public function getDoubleQuote()
    {
        return $this->_doubleQuote;
    }

    /**
     * Sets the doubleQuote option
     *
     * @param boolean $doubleQuote
     * @return Zend_Filter_HtmlEntities Provides a fluent interface
     */
    public function setDoubleQuote($doubleQuote)
    {
        $this->_doubleQuote = (boolean) $doubleQuote;
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, converting characters to their corresponding HTML entity
     * equivalents where they exist
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        return htmlentities((string) $value, $this->_quoteStyle, $this->_charSet, $this->_doubleQuote);
    }
}
