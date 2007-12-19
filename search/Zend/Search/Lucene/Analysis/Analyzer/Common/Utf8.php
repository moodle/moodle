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
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Analysis_Analyzer_Common */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Analysis/Analyzer/Common.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8 extends Zend_Search_Lucene_Analysis_Analyzer_Common
{
    /**
     * Current char position in an UTF-8 stream
     *
     * @var integer
     */
    private $_position;

    /**
     * Current binary position in an UTF-8 stream
     *
     * @var integer
     */
    private $_bytePosition;

    /**
     * Stream length
     *
     * @var integer
     */
    private $_streamLength;

    /**
     * Reset token stream
     */
    public function reset()
    {
        $this->_position     = 0;
        $this->_bytePosition = 0;

        // convert input into UTF-8
        if (strcasecmp($this->_encoding, 'utf8' ) != 0  &&
            strcasecmp($this->_encoding, 'utf-8') != 0 ) {
                $this->_input = iconv($this->_encoding, 'UTF-8', $this->_input);
                $this->_encoding = 'UTF-8';
        }

        // Get UTF-8 string length.
        // It also checks if it's a correct utf-8 string
        $this->_streamLength = iconv_strlen($this->_input, 'UTF-8');
    }

    /**
     * Check, that character is a letter
     *
     * @param string $char
     * @return boolean
     */
    private static function _isAlpha($char)
    {
        if (strlen($char) > 1) {
            // It's an UTF-8 character
            return true;
        }

        return ctype_alpha($char);
    }

    /**
     * Get next UTF-8 char
     *
     * @param string $char
     * @return boolean
     */
    private function _nextChar()
    {
        $char = $this->_input[$this->_bytePosition++];

        if (( ord($char) & 0xC0 ) == 0xC0) {
            $addBytes = 1;
            if (ord($char) & 0x20 ) {
                $addBytes++;
                if (ord($char) & 0x10 ) {
                    $addBytes++;
                }
            }
            $char .= substr($this->_input, $this->_bytePosition, $addBytes);
            $this->_bytePosition += $addBytes;
        }

        $this->_position++;

        return $char;
    }

    /**
     * Tokenization stream API
     * Get next token
     * Returns null at the end of stream
     *
     * @return Zend_Search_Lucene_Analysis_Token|null
     */
    public function nextToken()
    {
        if ($this->_input === null) {
            return null;
        }

        while ($this->_position < $this->_streamLength) {
            // skip white space
            while ($this->_position < $this->_streamLength &&
                   !self::_isAlpha($char = $this->_nextChar())) {
                $char = '';
            }

            $termStartPosition = $this->_position - 1;
            $termText = $char;

            // read token
            while ($this->_position < $this->_streamLength &&
                   self::_isAlpha($char = $this->_nextChar())) {
                $termText .= $char;
            }

            // Empty token, end of stream.
            if ($termText == '') {
                return null;
            }

            $token = new Zend_Search_Lucene_Analysis_Token(
                                      $termText,
                                      $termStartPosition,
                                      $this->_position - 1);
            $token = $this->normalize($token);
            if ($token !== null) {
                return $token;
            }
            // Continue if token is skipped
        }

        return null;
    }
}

