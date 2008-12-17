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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Field
{
    /**
     * Field name
     *
     * @var string
     */
    public $name;


    public $value;
    public $isStored    = false;
    public $isIndexed   = true;
    public $isTokenized = true;
    public $isBinary    = false;

    public $storeTermVector = false;

    /**
     * Field boos factor
     * It's not stored directly in the index, but affects on normalizetion factor
     *
     * @var float
     */
    public $boost = 1.0;

    /**
     * Field value encoding.
     *
     * @var string
     */
    public $encoding;

    /**
     * Object constructor
     *
     * @param string $name
     * @param string $value
     * @param string $encoding
     * @param boolean $isStored
     * @param boolean $isIndexed
     * @param boolean $isTokenized
     * @param boolean $isBinary
     */
    public function __construct($name, $value, $encoding, $isStored, $isIndexed, $isTokenized, $isBinary = false)
    {
        $this->name  = $name;
        $this->value = $value;

        if (!$isBinary) {
            $this->encoding    = $encoding;
            $this->isTokenized = $isTokenized;
        } else {
            $this->encoding    = '';
            $this->isTokenized = false;
        }

        $this->isStored  = $isStored;
        $this->isIndexed = $isIndexed;
        $this->isBinary  = $isBinary;

        $this->storeTermVector = false;
        $this->boost           = 1.0;
    }


    /**
     * Constructs a String-valued Field that is not tokenized, but is indexed
     * and stored.  Useful for non-text fields, e.g. date or url.
     *
     * @param string $name
     * @param string $value
     * @param string $encoding
     * @return Zend_Search_Lucene_Field
     */
    public static function Keyword($name, $value, $encoding = '')
    {
        return new self($name, $value, $encoding, true, true, false);
    }


    /**
     * Constructs a String-valued Field that is not tokenized nor indexed,
     * but is stored in the index, for return with hits.
     *
     * @param string $name
     * @param string $value
     * @param string $encoding
     * @return Zend_Search_Lucene_Field
     */
    public static function UnIndexed($name, $value, $encoding = '')
    {
        return new self($name, $value, $encoding, true, false, false);
    }


    /**
     * Constructs a Binary String valued Field that is not tokenized nor indexed,
     * but is stored in the index, for return with hits.
     *
     * @param string $name
     * @param string $value
     * @param string $encoding
     * @return Zend_Search_Lucene_Field
     */
    public static function Binary($name, $value)
    {
        return new self($name, $value, '', true, false, false, true);
    }

    /**
     * Constructs a String-valued Field that is tokenized and indexed,
     * and is stored in the index, for return with hits.  Useful for short text
     * fields, like "title" or "subject". Term vector will not be stored for this field.
     *
     * @param string $name
     * @param string $value
     * @param string $encoding
     * @return Zend_Search_Lucene_Field
     */
    public static function Text($name, $value, $encoding = '')
    {
        return new self($name, $value, $encoding, true, true, true);
    }


    /**
     * Constructs a String-valued Field that is tokenized and indexed,
     * but that is not stored in the index.
     *
     * @param string $name
     * @param string $value
     * @param string $encoding
     * @return Zend_Search_Lucene_Field
     */
    public static function UnStored($name, $value, $encoding = '')
    {
        return new self($name, $value, $encoding, false, true, true);
    }

    /**
     * Get field value in UTF-8 encoding
     *
     * @return string
     */
    public function getUtf8Value()
    {
        if (strcasecmp($this->encoding, 'utf8' ) == 0  ||
            strcasecmp($this->encoding, 'utf-8') == 0 ) {
                return $this->value;
        } else {
            return iconv($this->encoding, 'UTF-8', $this->value);
        }
    }
}

