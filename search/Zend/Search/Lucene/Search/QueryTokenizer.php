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
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Search_QueryToken */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Search/QueryToken.php';

/** Zend_Search_Lucene_Exception */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Search
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Search_QueryTokenizer implements Iterator
{
    /**
     * inputString tokens.
     *
     * @var array
     */
    protected $_tokens = array();

    /**
     * tokens pointer.
     *
     * @var integer
     */
    protected $_currToken = 0;


    /**
     * QueryTokenize constructor needs query string as a parameter.
     *
     * @param string $inputString
     */
    public function __construct($inputString)
    {
        if (!strlen($inputString)) {
            throw new Zend_Search_Lucene_Exception('Cannot tokenize empty query string.');
        }

        $currentToken = '';
        for ($count = 0; $count < strlen($inputString); $count++) {
            if (ctype_alnum( $inputString{$count} ) ||
                $inputString{$count} == '_') {
                $currentToken .= $inputString{$count};
            } else if ($inputString{$count} == '\\') { // Escaped character
                $count++;

                if ($count == strlen($inputString)) {
                    throw new Zend_Search_Lucene_Exception('Non finished escape sequence.');
                }

                $currentToken .= $inputString{$count};
            } else {
                // Previous token is finished
                if (strlen($currentToken)) {
                    $this->_tokens[] = new Zend_Search_Lucene_Search_QueryToken(Zend_Search_Lucene_Search_QueryToken::TOKTYPE_WORD,
                                                                $currentToken);
                    $currentToken = '';
                }

                if ($inputString{$count} == '+' || $inputString{$count} == '-') {
                    $this->_tokens[] = new Zend_Search_Lucene_Search_QueryToken(Zend_Search_Lucene_Search_QueryToken::TOKTYPE_SIGN,
                                                                $inputString{$count});
                } elseif ($inputString{$count} == '(' || $inputString{$count} == ')') {
                    $this->_tokens[] = new Zend_Search_Lucene_Search_QueryToken(Zend_Search_Lucene_Search_QueryToken::TOKTYPE_BRACKET,
                                                                $inputString{$count});
                } elseif ($inputString{$count} == ':' && $this->count()) {
                    if ($this->_tokens[count($this->_tokens)-1]->type == Zend_Search_Lucene_Search_QueryToken::TOKTYPE_WORD) {
                        $this->_tokens[count($this->_tokens)-1]->type = Zend_Search_Lucene_Search_QueryToken::TOKTYPE_FIELD;
                    }
                }
            }
        }

        if (strlen($currentToken)) {
            $this->_tokens[] = new Zend_Search_Lucene_Search_QueryToken(Zend_Search_Lucene_Search_QueryToken::TOKTYPE_WORD, $currentToken);
        }
    }


    /**
     * Returns number of tokens
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_tokens);
    }


    /**
     * Returns TRUE if a token exists at the current position.
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_currToken < $this->count();
    }


    /**
     * Resets token stream.
     *
     * @return integer
     */
    public function rewind()
    {
        $this->_currToken = 0;
    }


    /**
     * Returns the token at the current position or FALSE if
     * the position does not contain a valid token.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->valid() ? $this->_tokens[$this->_currToken] : false;
    }


    /**
     * Returns next token
     *
     * @return Zend_Search_Lucene_Search_QueryToken
     */
    public function next()
    {
        return ++$this->_currToken;
    }


    /**
     * Return the position of the current token.
     *
     * @return integer
     */
    public function key()
    {
        return $this->_currToken;
    }

}

