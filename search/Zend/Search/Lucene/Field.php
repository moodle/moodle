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
 * @subpackage Document
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * A field is a section of a Document.  Each field has two parts,
 * a name and a value. Values may be free text or they may be atomic
 * keywords, which are not further processed. Such keywords may
 * be used to represent dates, urls, etc.  Fields are optionally
 * stored in the index, so that they may be returned with hits
 * on the document.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Document
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Field
{
    public $kind;

    public $name        = 'body';
    public $stringValue = null;
    public $isStored    = false;
    public $isIndexed   = true;
    public $isTokenized = true;
    public $isBinary    = false;

    public $storeTermVector = false;

    public $boost = 1.0;

    public function __construct($name, $stringValue, $isStored, $isIndexed, $isTokenized, $isBinary = false)
    {
        $this->name        = $name;

        if (!$isBinary) {
            /**
             * @todo Correct UTF-8 string should be required in future
             * Until full UTF-8 support is not completed, string should be normalized to ANSII encoding
             */
            $this->stringValue = iconv(mb_detect_encoding($stringValue), 'ASCII//TRANSLIT', $stringValue);
            //$this->stringValue = iconv('', 'ASCII//TRANSLIT', $stringValue);
        } else {
            $this->stringValue = $stringValue;
        }
        $this->isStored    = $isStored;
        $this->isIndexed   = $isIndexed;
        $this->isTokenized = $isTokenized;
        $this->isBinary    = $isBinary;

        $this->storeTermVector = false;
        $this->boost           = 1.0;
    }


    /**
     * Constructs a String-valued Field that is not tokenized, but is indexed
     * and stored.  Useful for non-text fields, e.g. date or url.
     *
     * @param string $name
     * @param string $value
     * @return Zend_Search_Lucene_Field
     */
    static public function Keyword($name, $value)
    {
        return new self($name, $value, true, true, false);
    }


    /**
     * Constructs a String-valued Field that is not tokenized nor indexed,
     * but is stored in the index, for return with hits.
     *
     * @param string $name
     * @param string $value
     * @return Zend_Search_Lucene_Field
     */
    static public function UnIndexed($name, $value)
    {
        return new self($name, $value, true, false, false);
    }


    /**
     * Constructs a Binary String valued Field that is not tokenized nor indexed,
     * but is stored in the index, for return with hits.
     *
     * @param string $name
     * @param string $value
     * @return Zend_Search_Lucene_Field
     */
    static public function Binary($name, $value)
    {
        return new self($name, $value, true, false, false, true);
    }

    /**
     * Constructs a String-valued Field that is tokenized and indexed,
     * and is stored in the index, for return with hits.  Useful for short text
     * fields, like "title" or "subject". Term vector will not be stored for this field.
     *
     * @param string $name
     * @param string $value
     * @return Zend_Search_Lucene_Field
     */
    static public function Text($name, $value)
    {
        return new self($name, $value, true, true, true);
    }


    /**
     * Constructs a String-valued Field that is tokenized and indexed,
     * but that is not stored in the index.
     *
     * @param string $name
     * @param string $value
     * @return Zend_Search_Lucene_Field
     */
    static public function UnStored($name, $value)
    {
        return new self($name, $value, false, true, true);
    }

}

