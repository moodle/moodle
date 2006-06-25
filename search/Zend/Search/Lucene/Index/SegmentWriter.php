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


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Analysis_Analyzer */
require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_SegmentWriter
{
    /**
     * Expert: The fraction of terms in the "dictionary" which should be stored
     * in RAM.  Smaller values use more memory, but make searching slightly
     * faster, while larger values use less memory and make searching slightly
     * slower.  Searching is typically not dominated by dictionary lookup, so
     * tweaking this is rarely useful.
     *
     * @var integer
     */
    static public $indexInterval = 128;

    /** Expert: The fraction of TermDocs entries stored in skip tables.
     * Larger values result in smaller indexes, greater acceleration, but fewer
     * accelerable cases, while smaller values result in bigger indexes,
     * less acceleration and more
     * accelerable cases. More detailed experiments would be useful here.
     *
     * 0x0x7FFFFFFF indicates that we don't use skip data
     * Default value is 16
     *
     * @var integer
     */
    static public $skipInterval = 0x7FFFFFFF;

    /**
     * Number of docs in a segment
     *
     * @var integer
     */
    private $_docCount;

    /**
     * Segment name
     *
     * @var string
     */
    private $_name;

    /**
     * File system adapter.
     *
     * @var Zend_Search_Lucene_Storage_Directory
     */
    private $_directory;

    /**
     * List of the index files.
     * Used for automatic compound file generation
     *
     * @var unknown_type
     */
    private $_files;

    /**
     * Term Dictionary
     * Array of the Zend_Search_Lucene_Index_Term objects
     * Corresponding Zend_Search_Lucene_Index_TermInfo object stored in the $_termDictionaryInfos
     *
     * @var array
     */
    private $_termDictionary;

    /**
     * Documents, which contain the term
     *
     * @var array
     */
    private $_termDocs;

    /**
     * Segment fields. Array of Zend_Search_Lucene_Index_FieldInfo objects for this segment
     *
     * @var array
     */
    private $_fields;

    /**
     * Sizes of the indexed fields.
     * Used for normalization factors calculation.
     *
     * @var array
     */
    private $_fieldLengths;

    /**
     * '.fdx'  file - Stored Fields, the field index.
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    private $_fdxFile;

    /**
     * '.fdt'  file - Stored Fields, the field data.
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    private $_fdtFile;


    /**
     * Object constructor.
     *
     * @param Zend_Search_Lucene_Storage_Directory $directory
     * @param string $name
     */
    public function __construct($directory, $name)
    {
        $this->_directory = $directory;
        $this->_name      = $name;
        $this->_docCount  = 0;

        $this->_fields         = array();
        $this->_termDocs       = array();
        $this->_files          = array();
        $this->_norms          = array();
        $this->_fieldLengths   = array();
        $this->_termDictionary = array();

        $this->_fdxFile = null;
        $this->_fdtFile = null;
    }


    /**
     * Add field to the segment
     *
     * @param Zend_Search_Lucene_Field $field
     */
    private function _addFieldInfo(Zend_Search_Lucene_Field $field)
    {
        if (!isset($this->_fields[$field->name])) {
            $this->_fields[$field->name] =
                                new Zend_Search_Lucene_Index_FieldInfo($field->name,
                                                                       $field->isIndexed,
                                                                       count($this->_fields),
                                                                       $field->storeTermVector);
        } else {
            $this->_fields[$field->name]->isIndexed       |= $field->isIndexed;
            $this->_fields[$field->name]->storeTermVector |= $field->storeTermVector;
        }
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

        foreach ($document->getFieldNames() as $fieldName) {
            $field = $document->getField($fieldName);
            $this->_addFieldInfo($field);

            if ($field->storeTermVector) {
                /**
                 * @todo term vector storing support
                 */
                throw new Zend_Search_Lucene_Exception('Store term vector functionality is not supported yet.');
            }

            if ($field->isIndexed) {
                if ($field->isTokenized) {
                    $tokenList = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($field->stringValue);
                } else {
                    $tokenList = array();
                    $tokenList[] = new Zend_Search_Lucene_Analysis_Token($field->stringValue, 0, strlen($field->stringValue));
                }
                $this->_fieldLengths[$field->name][$this->_docCount] = count($tokenList);

                $position = 0;
                foreach ($tokenList as $token) {
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
            }

            if ($field->isStored) {
                $storedFields[] = $field;
            }
        }

        if (count($storedFields) != 0) {
            if (!isset($this->_fdxFile)) {
                $this->_fdxFile = $this->_directory->createFile($this->_name . '.fdx');
                $this->_fdtFile = $this->_directory->createFile($this->_name . '.fdt');

                $this->_files[] = $this->_name . '.fdx';
                $this->_files[] = $this->_name . '.fdt';
            }

            $this->_fdxFile->writeLong($this->_fdtFile->tell());
            $this->_fdtFile->writeVInt(count($storedFields));
            foreach ($storedFields as $field) {
                $this->_fdtFile->writeVInt($this->_fields[$field->name]->number);
                $fieldBits = ($field->isTokenized ? 0x01 : 0x00) |
                             ($field->isBinary ?    0x02 : 0x00) |
                             0x00; /* 0x04 - third bit, compressed (ZLIB) */
                $this->_fdtFile->writeByte($fieldBits);
                if ($field->isBinary) {
                    $this->_fdtFile->writeVInt(strlen($field->stringValue));
                    $this->_fdtFile->writeBytes($field->stringValue);
                } else {
                    $this->_fdtFile->writeString($field->stringValue);
                }
            }
        }

        $this->_docCount++;
    }


    /**
     * Dump Field Info (.fnm) segment file
     */
    private function _dumpFNM()
    {
        $fnmFile = $this->_directory->createFile($this->_name . '.fnm');
        $fnmFile->writeVInt(count($this->_fields));

        foreach ($this->_fields as $field) {
            $fnmFile->writeString($field->name);
            $fnmFile->writeByte(($field->isIndexed       ? 0x01 : 0x00) |
                                ($field->storeTermVector ? 0x02 : 0x00)
// not supported yet            0x04 /* term positions are stored with the term vectors */ |
// not supported yet            0x08 /* term offsets are stored with the term vectors */   |
                               );

            if ($field->isIndexed) {
                $fieldNum   = $this->_fields[$field->name]->number;
                $fieldName  = $field->name;
                $similarity = Zend_Search_Lucene_Search_Similarity::getDefault();
                $norm       = '';

                for ($count = 0; $count < $this->_docCount; $count++) {
                    $numTokens = isset($this->_fieldLengths[$fieldName][$count]) ?
                                      $this->_fieldLengths[$fieldName][$count] : 0;
                    $norm .= chr($similarity->encodeNorm($similarity->lengthNorm($fieldName, $numTokens)));
                }

                $normFileName = $this->_name . '.f' . $fieldNum;
                $fFile = $this->_directory->createFile($normFileName);
                $fFile->writeBytes($norm);
                $this->_files[] = $normFileName;
            }
        }

        $this->_files[] = $this->_name . '.fnm';
    }


    /**
     * Dump Term Dictionary segment file entry.
     * Used to write entry to .tis or .tii files
     *
     * @param Zend_Search_Lucene_Storage_File $dicFile
     * @param Zend_Search_Lucene_Index_Term $prevTerm
     * @param Zend_Search_Lucene_Index_Term $term
     * @param Zend_Search_Lucene_Index_TermInfo $prevTermInfo
     * @param Zend_Search_Lucene_Index_TermInfo $termInfo
     */
    private function _dumpTermDictEntry(Zend_Search_Lucene_Storage_File $dicFile,
                                        &$prevTerm,     Zend_Search_Lucene_Index_Term     $term,
                                        &$prevTermInfo, Zend_Search_Lucene_Index_TermInfo $termInfo)
    {
        if (isset($prevTerm) && $prevTerm->field == $term->field) {
            $prefixLength = 0;
            while ($prefixLength < strlen($prevTerm->text) &&
                   $prefixLength < strlen($term->text) &&
                   $prevTerm->text{$prefixLength} == $term->text{$prefixLength}
                  ) {
                $prefixLength++;
            }
            // Write preffix length
            $dicFile->writeVInt($prefixLength);
            // Write suffix
            $dicFile->writeString( substr($term->text, $prefixLength) );
        } else {
            // Write preffix length
            $dicFile->writeVInt(0);
            // Write suffix
            $dicFile->writeString($term->text);
        }
        // Write field number
        $dicFile->writeVInt($term->field);
        // DocFreq (the count of documents which contain the term)
        $dicFile->writeVInt($termInfo->docFreq);

        $prevTerm = $term;

        if (!isset($prevTermInfo)) {
            // Write FreqDelta
            $dicFile->writeVInt($termInfo->freqPointer);
            // Write ProxDelta
            $dicFile->writeVInt($termInfo->proxPointer);
        } else {
            // Write FreqDelta
            $dicFile->writeVInt($termInfo->freqPointer - $prevTermInfo->freqPointer);
            // Write ProxDelta
            $dicFile->writeVInt($termInfo->proxPointer - $prevTermInfo->proxPointer);
        }
        // Write SkipOffset - it's not 0 when $termInfo->docFreq > self::$skipInterval
        if ($termInfo->skipOffset != 0) {
            $dicFile->writeVInt($termInfo->skipOffset);
        }

        $prevTermInfo = $termInfo;
    }

    /**
     * Dump Term Dictionary (.tis) and Term Dictionary Index (.tii) segment files
     */
    private function _dumpDictionary()
    {
        $termKeys = array_keys($this->_termDictionary);
        sort($termKeys, SORT_STRING);

        $tisFile = $this->_directory->createFile($this->_name . '.tis');
        $tisFile->writeInt((int)0xFFFFFFFE);
        $tisFile->writeLong(count($termKeys));
        $tisFile->writeInt(self::$indexInterval);
        $tisFile->writeInt(self::$skipInterval);

        $tiiFile = $this->_directory->createFile($this->_name . '.tii');
        $tiiFile->writeInt((int)0xFFFFFFFE);
        $tiiFile->writeLong(ceil((count($termKeys) + 2)/self::$indexInterval));
        $tiiFile->writeInt(self::$indexInterval);
        $tiiFile->writeInt(self::$skipInterval);

        /** Dump dictionary header */
        $tiiFile->writeVInt(0);                    // preffix length
        $tiiFile->writeString('');                 // suffix
        $tiiFile->writeInt((int)0xFFFFFFFF);       // field number
        $tiiFile->writeByte((int)0x0F);
        $tiiFile->writeVInt(0);                    // DocFreq
        $tiiFile->writeVInt(0);                    // FreqDelta
        $tiiFile->writeVInt(0);                    // ProxDelta
        $tiiFile->writeVInt(20);                   // IndexDelta

        $frqFile = $this->_directory->createFile($this->_name . '.frq');
        $prxFile = $this->_directory->createFile($this->_name . '.prx');

        $termCount = 1;

        $prevTerm     = null;
        $prevTermInfo = null;
        $prevIndexTerm     = null;
        $prevIndexTermInfo = null;
        $prevIndexPosition = 20;

        foreach ($termKeys as $termId) {
            $freqPointer = $frqFile->tell();
            $proxPointer = $prxFile->tell();

            $prevDoc = 0;
            foreach ($this->_termDocs[$termId] as $docId => $termPositions) {
                $docDelta = ($docId - $prevDoc)*2;
                $prevDoc = $docId;
                if (count($termPositions) > 1) {
                    $frqFile->writeVInt($docDelta);
                    $frqFile->writeVInt(count($termPositions));
                } else {
                    $frqFile->writeVInt($docDelta + 1);
                }

                $prevPosition = 0;
                foreach ($termPositions as $position) {
                    $prxFile->writeVInt($position - $prevPosition);
                    $prevPosition = $position;
                }
            }

            if (count($this->_termDocs[$termId]) >= self::$skipInterval) {
                /**
                 * @todo Write Skip Data to a freq file.
                 * It's not used now, but make index more optimal
                 */
                $skipOffset = $frqFile->tell() - $freqPointer;
            } else {
                $skipOffset = 0;
            }

            $term = new Zend_Search_Lucene_Index_Term($this->_termDictionary[$termId]->text,
                                                      $this->_fields[$this->_termDictionary[$termId]->field]->number);
            $termInfo = new Zend_Search_Lucene_Index_TermInfo(count($this->_termDocs[$termId]),
                                            $freqPointer, $proxPointer, $skipOffset);

            $this->_dumpTermDictEntry($tisFile, $prevTerm, $term, $prevTermInfo, $termInfo);

            if ($termCount % self::$indexInterval == 0) {
                $this->_dumpTermDictEntry($tiiFile, $prevIndexTerm, $term, $prevIndexTermInfo, $termInfo);

                $indexPosition = $tisFile->tell();
                $tiiFile->writeVInt($indexPosition - $prevIndexPosition);
                $prevIndexPosition = $indexPosition;
            }
            $termCount++;
        }

        $this->_files[] = $this->_name . '.tis';
        $this->_files[] = $this->_name . '.tii';
        $this->_files[] = $this->_name . '.frq';
        $this->_files[] = $this->_name . '.prx';
    }


    /**
     * Generate compound index file
     */
    private function _generateCFS()
    {
        $cfsFile = $this->_directory->createFile($this->_name . '.cfs');
        $cfsFile->writeVInt(count($this->_files));

        $dataOffsetPointers = array();
        foreach ($this->_files as $fileName) {
            $dataOffsetPointers[$fileName] = $cfsFile->tell();
            $cfsFile->writeLong(0); // write dummy data
            $cfsFile->writeString($fileName);
        }

        foreach ($this->_files as $fileName) {
            // Get actual data offset
            $dataOffset = $cfsFile->tell();
            // Seek to the data offset pointer
            $cfsFile->seek($dataOffsetPointers[$fileName]);
            // Write actual data offset value
            $cfsFile->writeLong($dataOffset);
            // Seek back to the end of file
            $cfsFile->seek($dataOffset);

            $dataFile = $this->_directory->getFileObject($fileName);
            $data = $dataFile->readBytes($this->_directory->fileLength($fileName));
            $cfsFile->writeBytes($data);

            $this->_directory->deleteFile($fileName);
        }
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

        return new Zend_Search_Lucene_Index_SegmentInfo($this->_name,
                                                        $this->_docCount,
                                                        $this->_directory);
    }

}

