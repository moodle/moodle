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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Analysis_Analyzer */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';

/** Zend_Search_Lucene_Index_SegmentWriter */
require_once 'Zend/Search/Lucene/Index/SegmentWriter.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_SegmentWriter_DocumentWriter extends Zend_Search_Lucene_Index_SegmentWriter
{
    /**
     * Term Dictionary
     * Array of the Zend_Search_Lucene_Index_Term objects
     * Corresponding Zend_Search_Lucene_Index_TermInfo object stored in the $_termDictionaryInfos
     *
     * @var array
     */
    protected $_termDictionary;

    /**
     * Documents, which contain the term
     *
     * @var array
     */
    protected $_termDocs;

    /**
     * Object constructor.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param string $name
     */
    public function __construct(Zend_Search_Lucene_Storage_Directory $directory, $name)
    {
        parent::__construct($directory, $name);

        $this->_termDocs       = array();
        $this->_termDictionary = array();
    }


    /**
     * Adds a document to this segment.
     *
     * @param Zend_Search_Lucene_Document $document
     * @throws Zend_Search_Lucene_Exception
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        $storedFields = array();
        $docNorms     = array();
        $similarity   = Zend_Search_Lucene_Search_Similarity::getDefault();

        foreach ($document->getFieldNames() as $fieldName) {
            $field = $document->getField($fieldName);
            $this->addField($field);

            if ($field->storeTermVector) {
                /**
                 * @todo term vector storing support
                 */
                throw new Zend_Search_Lucene_Exception('Store term vector functionality is not supported yet.');
            }

            if ($field->isIndexed) {
                if ($field->isTokenized) {
                    $analyzer = Zend_Search_Lucene_Analysis_Analyzer::getDefault();
                    $analyzer->setInput($field->value, $field->encoding);

                    $position     = 0;
                    $tokenCounter = 0;
                    while (($token = $analyzer->nextToken()) !== null) {
                        $tokenCounter++;

                        $term = new Zend_Search_Lucene_Index_Term($token->getTermText(), $field->name);
                        $termKey = $term->key();

                        if (!isset($this->_termDictionary[$termKey])) {
                            // New term
                            $this->_termDictionary[$termKey] = $term;
                            $this->_termDocs[$termKey] = array();
                            $this->_termDocs[$termKey][$this->_docCount] = array();
                        } else if (!isset($this->_termDocs[$termKey][$this->_docCount])) {
                            // Existing term, but new term entry
                            $this->_termDocs[$termKey][$this->_docCount] = array();
                        }
                        $position += $token->getPositionIncrement();
                        $this->_termDocs[$termKey][$this->_docCount][] = $position;
                    }

                    $docNorms[$field->name] = chr($similarity->encodeNorm( $similarity->lengthNorm($field->name,
                                                                                                   $tokenCounter)*
                                                                           $document->boost*
                                                                           $field->boost ));
                } else {
                    $term = new Zend_Search_Lucene_Index_Term($field->getUtf8Value(), $field->name);
                    $termKey = $term->key();

                    if (!isset($this->_termDictionary[$termKey])) {
                        // New term
                        $this->_termDictionary[$termKey] = $term;
                        $this->_termDocs[$termKey] = array();
                        $this->_termDocs[$termKey][$this->_docCount] = array();
                    } else if (!isset($this->_termDocs[$termKey][$this->_docCount])) {
                        // Existing term, but new term entry
                        $this->_termDocs[$termKey][$this->_docCount] = array();
                    }
                    $this->_termDocs[$termKey][$this->_docCount][] = 0; // position

                    $docNorms[$field->name] = chr($similarity->encodeNorm( $similarity->lengthNorm($field->name, 1)*
                                                                           $document->boost*
                                                                           $field->boost ));
                }
            }

            if ($field->isStored) {
                $storedFields[] = $field;
            }
        }


        foreach ($this->_fields as $fieldName => $field) {
            if (!$field->isIndexed) {
                continue;
            }

            if (!isset($this->_norms[$fieldName])) {
                $this->_norms[$fieldName] = str_repeat(chr($similarity->encodeNorm( $similarity->lengthNorm($fieldName, 0) )),
                                                       $this->_docCount);
            }

            if (isset($docNorms[$fieldName])){
                $this->_norms[$fieldName] .= $docNorms[$fieldName];
            } else {
                $this->_norms[$fieldName] .= chr($similarity->encodeNorm( $similarity->lengthNorm($fieldName, 0) ));
            }
        }

        $this->addStoredFields($storedFields);
    }


    /**
     * Dump Term Dictionary (.tis) and Term Dictionary Index (.tii) segment files
     */
    protected function _dumpDictionary()
    {
        ksort($this->_termDictionary, SORT_STRING);

        $this->initializeDictionaryFiles();

        foreach ($this->_termDictionary as $termId => $term) {
            $this->addTerm($term, $this->_termDocs[$termId]);
        }

        $this->closeDictionaryFiles();
    }


    /**
     * Close segment, write it to disk and return segment info
     *
     * @return Zend_Search_Lucene_Index_SegmentInfo
     */
    public function close()
    {
        if ($this->_docCount == 0) {
            return null;
        }

        $this->_dumpFNM();
        $this->_dumpDictionary();

        $this->_generateCFS();

        return new Zend_Search_Lucene_Index_SegmentInfo($this->_directory,
                                                        $this->_name,
                                                        $this->_docCount,
                                                        -1,
                                                        true,
                                                        true);
    }

}

