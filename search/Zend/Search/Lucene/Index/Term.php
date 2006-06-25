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
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * A Term represents a word from text.  This is the unit of search.  It is
 * composed of two elements, the text of the word, as a string, and the name of
 * the field that the text occured in, an interned string.
 *
 * Note that terms may represent more than words from text fields, but also
 * things like dates, email addresses, urls, etc.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_Term
{
    /**
     * Field name or field number (depending from context)
     *
     * @var mixed
     */
    public $field;

    /**
     * Term value
     *
     * @var string
     */
    public $text;


    /**
     * @todo docblock
     */
    public function __construct( $text, $field = 'contents' )
    {
        $this->field = $field;
        $this->text = $text;
    }


    /**
     * @todo docblock
     */
    public function key()
    {
        return $this->field . chr(0) . $this->text;
    }
}

