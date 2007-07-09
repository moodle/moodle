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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Search_Lucene_Index_DictionaryLoader */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/DictionaryLoader.php';


/** Zend_Search_Lucene_Exception */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene_Index_SegmentInfo
{
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
     * Term Dictionary Index
     *
     * Array of arrays (Zend_Search_Lucene_Index_Term objects are represented as arrays because
     * of performance considerations)
     * [0] -> $termValue
     * [1] -> $termFieldNum
     *
     * Corresponding Zend_Search_Lucene_Index_TermInfo object stored in the $_termDictionaryInfos
     *
     * @var array
     */
    private $_termDictionary;

    /**
     * Term Dictionary Index TermInfos
     *
     * Array of arrays (Zend_Search_Lucene_Index_TermInfo objects are represented as arrays because
     * of performance considerations)
     * [0] -> $docFreq
     * [1] -> $freqPointer
     * [2] -> $proxPointer
     * [3] -> $skipOffset
     * [4] -> $indexPointer
     *
     * @var array
     */
    private $_termDictionaryInfos;

    /**
     * Segment fields. Array of Zend_Search_Lucene_Index_FieldInfo objects for this segment
     *
     * @var array
     */
    private $_fields;

    /**
     * Field positions in a dictionary.
     * (Term dictionary contains filelds ordered by names)
     *
     * @var array
     */
    private $_fieldsDicPositions;


    /**
     * Associative array where the key is the file name and the value is data offset
     * in a compound segment file (.csf).
     *
     * @var array
     */
    private $_segFiles;

    /**
     * Associative array where the key is the file name and the value is file size (.csf).
     *
     * @var array
     */
    private $_segFileSizes;


    /**
     * File system adapter.
     *
     * @var Zend_Search_Lucene_Storage_Directory_Filesystem
     */
    private $_directory;

    /**
     * Normalization factors.
     * An array fieldName => normVector
     * normVector is a binary string.
     * Each byte corresponds to an indexed document in a segment and
     * encodes normalization factor (float value, encoded by
     * Zend_Search_Lucene_Search_Similarity::encodeNorm())
     *
     * @var array
     */
    private $_norms = array();

    /**
     * List of deleted documents.
     * bitset if bitset extension is loaded or array otherwise.
     *
     * @var mixed
     */
    private $_deleted;

    /**
     * $this->_deleted update flag
     *
     * @var boolean
     */
    private $_deletedDirty = false;


    /**
     * Zend_Search_Lucene_Index_SegmentInfo constructor needs Segmentname,
     * Documents count and Directory as a parameter.
     *
     * @param string $name
     * @param integer $docCount
     * @param Zend_Search_Lucene_Storage_Directory $directory
     */
    public function __construct($name, $docCount, $directory)
    {
        $this->_name = $name;
        $this->_docCount = $docCount;
        $this->_directory = $directory;
        $this->_termDictionary = null;

        $this->_segFiles = array();
        if ($this->_directory->fileExists($name . '.cfs')) {
            $cfsFile = $this->_directory->getFileObject($name . '.cfs');
            $segFilesCount = $cfsFile->readVInt();

            for ($count = 0; $count < $segFilesCount; $count++) {
                $dataOffset = $cfsFile->readLong();
                if ($count != 0) {
                    $this->_segFileSizes[$fileName] = $dataOffset - end($this->_segFiles);
                }
                $fileName = $cfsFile->readString();
                $this->_segFiles[$fileName] = $dataOffset;
            }
            if ($count != 0) {
                $this->_segFileSizes[$fileName] = $this->_directory->fileLength($name . '.cfs') - $dataOffset;
            }
        }

        $fnmFile = $this->openCompoundFile('.fnm');
        $fieldsCount = $fnmFile->readVInt();
        $fieldNames = array();
        $fieldNums  = array();
        $this->_fields = array();
        for ($count=0; $count < $fieldsCount; $count++) {
            $fieldName = $fnmFile->readString();
            $fieldBits = $fnmFile->readByte();
            $this->_fields[$count] = new Zend_Search_Lucene_Index_FieldInfo($fieldName,
                                                                            $fieldBits & 1,
                                                                            $count,
                                                                            $fieldBits & 2 );
            if ($fieldBits & 0x10) {
                // norms are omitted for the indexed field
                $this->_norms[$count] = str_repeat(chr(Zend_Search_Lucene_Search_Similarity::encodeNorm(1.0)), $docCount);
            }

            $fieldNums[$count]  = $count;
            $fieldNames[$count] = $fieldName;
        }
        array_multisort($fieldNames, SORT_ASC, SORT_REGULAR, $fieldNums);
        $this->_fieldsDicPositions = array_flip($fieldNums);

        try {
            $delFile = $this->openCompoundFile('.del');

            $byteCount = $delFile->readInt();
            $byteCount = ceil($byteCount/8);
            $bitCount  = $delFile->readInt();

            if ($bitCount == 0) {
                $delBytes = '';
            } else {
                $delBytes = $delFile->readBytes($byteCount);
            }

            if (extension_loaded('bitset')) {
                $this->_deleted = $delBytes;
            } else {
                $this->_deleted = array();
                for ($count = 0; $count < $byteCount; $count++) {
                    $byte = ord($delBytes{$count});
                    for ($bit = 0; $bit < 8; $bit++) {
                        if ($byte & (1<<$bit)) {
                            $this->_deleted[$count*8 + $bit] = 1;
                        }
                    }
                }
            }
        } catch(Zend_Search_Exception $e) {
            if (strpos($e->getMessage(), 'compound file doesn\'t contain') !== false ) {
                $this->_deleted = null;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Opens index file stoted within compound index file
     *
     * @param string $extension
     * @param boolean $shareHandler
     * @throws Zend_Search_Lucene_Exception
     * @return Zend_Search_Lucene_Storage_File
     */
    public function openCompoundFile($extension, $shareHandler = true)
    {
        $filename = $this->_name . $extension;

        // Try to open common file first
        if ($this->_directory->fileExists($filename)) {
            return $this->_directory->getFileObject($filename, $shareHandler);
        }

        if( !isset($this->_segFiles[$filename]) ) {
            throw new Zend_Search_Lucene_Exception('Index compound file doesn\'t contain '
                                       . $filename . ' file.' );
        }

        $file = $this->_directory->getFileObject($this->_name . '.cfs', $shareHandler);
        $file->seek($this->_segFiles[$filename]);
        return $file;
    }

    /**
     * Get compound file length
     *
     * @param string $extension
     * @return integer
     */
    public function compoundFileLength($extension)
    {
        $filename = $this->_name . $extension;

        // Try to get common file first
        if ($this->_directory->fileExists($filename)) {
            return $this->_directory->fileLength($filename);
        }

        if( !isset($this->_segFileSizes[$filename]) ) {
            throw new Zend_Search_Lucene_Exception('Index compound file doesn\'t contain '
                                       . $filename . ' file.' );
        }

        return $this->_segFileSizes[$filename];
    }

    /**
     * Returns field index or -1 if field is not found
     *
     * @param string $fieldName
     * @return integer
     */
    public function getFieldNum($fieldName)
    {
        foreach( $this->_fields as $field ) {
            if( $field->name == $fieldName ) {
                return $field->number;
            }
        }

        return -1;
    }

    /**
     * Returns field info for specified field
     *
     * @param integer $fieldNum
     * @return Zend_Search_Lucene_Index_FieldInfo
     */
    public function getField($fieldNum)
    {
        return $this->_fields[$fieldNum];
    }

    /**
     * Returns array of fields.
     * if $indexed parameter is true, then returns only indexed fields.
     *
     * @param boolean $indexed
     * @return array
     */
    public function getFields($indexed = false)
    {
        $result = array();
        foreach( $this->_fields as $field ) {
            if( (!$indexed) || $field->isIndexed ) {
                $result[ $field->name ] = $field->name;
            }
        }
        return $result;
    }

    /**
     * Returns array of FieldInfo objects.
     *
     * @return array
     */
    public function getFieldInfos()
    {
        return $this->_fields;
    }

    /**
     * Returns the total number of documents in this segment (including deleted documents).
     *
     * @return integer
     */
    public function count()
    {
        return $this->_docCount;
    }

    /**
     * Returns number of deleted documents.
     *
     * @return integer
     */
    private function _deletedCount()
    {
        if ($this->_deleted === null) {
            return 0;
        }

        if (extension_loaded('bitset')) {
            return count(bitset_to_array($this->_deleted));
        } else {
            return count($this->_deleted);
        }
    }

    /**
     * Returns the total number of non-deleted documents in this segment.
     *
     * @return integer
     */
    public function numDocs()
    {
        if ($this->hasDeletions()) {
            return $this->_docCount - $this->_deletedCount();
        } else {
            return $this->_docCount;
        }
    }

    /**
     * Get field position in a fields dictionary
     *
     * @param integer $fieldNum
     * @return integer
     */
    private function _getFieldPosition($fieldNum) {
        // Treat values which are not in a translation table as a 'direct value'
        return isset($this->_fieldsDicPositions[$fieldNum]) ?
                           $this->_fieldsDicPositions[$fieldNum] : $fieldNum;
    }

    /**
     * Return segment name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


    /**
     * TermInfo cache
     *
     * Size is 1024.
     * Numbers are used instead of class constants because of performance considerations
     *
     * @var array
     */
    private $_termInfoCache = array();

    private function _cleanUpTermInfoCache()
    {
        // Clean 256 term infos
        foreach ($this->_termInfoCache as $key => $termInfo) {
            unset($this->_termInfoCache[$key]);

            // leave 768 last used term infos
            if (count($this->_termInfoCache) == 768) {
                break;
            }
        }
    }

    /**
     * Scans terms dictionary and returns term info
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return Zend_Search_Lucene_Index_TermInfo
     */
    public function getTermInfo(Zend_Search_Lucene_Index_Term $term)
    {
        $termKey = $term->key();
        if (isset($this->_termInfoCache[$termKey])) {
            $termInfo = $this->_termInfoCache[$termKey];

            // Move termInfo to the end of cache
            unset($this->_termInfoCache[$termKey]);
            $this->_termInfoCache[$termKey] = $termInfo;

            return $termInfo;
        }


        if ($this->_termDictionary === null) {
            // Check, if index is already serialized
            if ($this->_directory->fileExists($this->_name . '.sti')) {
                // Prefetch dictionary index data
                $stiFile = $this->_directory->getFileObject($this->_name . '.sti');
                $stiFileData = $stiFile->readBytes($this->_directory->fileLength($this->_name . '.sti'));

                // Load dictionary index data
                list($this->_termDictionary, $this->_termDictionaryInfos) = unserialize($stiFileData);
            } else {
                // Prefetch dictionary index data
                $tiiFile = $this->openCompoundFile('.tii');
                $tiiFileData = $tiiFile->readBytes($this->compoundFileLength('.tii'));

                // Load dictionary index data
                list($this->_termDictionary, $this->_termDictionaryInfos) =
                            Zend_Search_Lucene_Index_DictionaryLoader::load($tiiFileData);

                $stiFileData = serialize(array($this->_termDictionary, $this->_termDictionaryInfos));
                $stiFile = $this->_directory->createFile($this->_name . '.sti');
                $stiFile->writeBytes($stiFileData);
            }

        }



        $searchField = $this->getFieldNum($term->field);

        if ($searchField == -1) {
            return null;
        }
        $searchDicField = $this->_getFieldPosition($searchField);

        // search for appropriate value in dictionary
        $lowIndex = 0;
        $highIndex = count($this->_termDictionary)-1;
        while ($highIndex >= $lowIndex) {
            // $mid = ($highIndex - $lowIndex)/2;
            $mid = ($highIndex + $lowIndex) >> 1;
            $midTerm = $this->_termDictionary[$mid];

            $fieldNum = $this->_getFieldPosition($midTerm[0] /* field */);
            $delta = $searchDicField - $fieldNum;
            if ($delta == 0) {
                $delta = strcmp($term->text, $midTerm[1] /* text */);
            }

            if ($delta < 0) {
                $highIndex = $mid-1;
            } elseif ($delta > 0) {
                $lowIndex  = $mid+1;
            } else {
                // return $this->_termDictionaryInfos[$mid]; // We got it!
                $a = $this->_termDictionaryInfos[$mid];
                $termInfo = new Zend_Search_Lucene_Index_TermInfo($a[0], $a[1], $a[2], $a[3], $a[4]);

                // Put loaded termInfo into cache
                $this->_termInfoCache[$termKey] = $termInfo;

                return $termInfo;
            }
        }

        if ($highIndex == -1) {
            // Term is out of the dictionary range
            return null;
        }

        $prevPosition = $highIndex;
        $prevTerm = $this->_termDictionary[$prevPosition];
        $prevTermInfo = $this->_termDictionaryInfos[$prevPosition];

        $tisFile = $this->openCompoundFile('.tis');
        $tiVersion = $tisFile->readInt();
        if ($tiVersion != (int)0xFFFFFFFE) {
            throw new Zend_Search_Lucene_Exception('Wrong TermInfoFile file format');
        }

        $termCount     = $tisFile->readLong();
        $indexInterval = $tisFile->readInt();
        $skipInterval  = $tisFile->readInt();

        $tisFile->seek($prevTermInfo[4] /* indexPointer */ - 20 /* header size*/, SEEK_CUR);

        $termValue    = $prevTerm[1] /* text */;
        $termFieldNum = $prevTerm[0] /* field */;
        $freqPointer = $prevTermInfo[1] /* freqPointer */;
        $proxPointer = $prevTermInfo[2] /* proxPointer */;
        for ($count = $prevPosition*$indexInterval + 1;
             $count <= $termCount &&
             ( $this->_getFieldPosition($termFieldNum) < $searchDicField ||
              ($this->_getFieldPosition($termFieldNum) == $searchDicField &&
               strcmp($termValue, $term->text) < 0) );
             $count++) {
            $termPrefixLength = $tisFile->readVInt();
            $termSuffix       = $tisFile->readString();
            $termFieldNum     = $tisFile->readVInt();
            $termValue        = Zend_Search_Lucene_Index_Term::getPrefix($termValue, $termPrefixLength) . $termSuffix;

            $docFreq      = $tisFile->readVInt();
            $freqPointer += $tisFile->readVInt();
            $proxPointer += $tisFile->readVInt();
            if( $docFreq >= $skipInterval ) {
                $skipOffset = $tisFile->readVInt();
            } else {
                $skipOffset = 0;
            }
        }

        if ($termFieldNum == $searchField && $termValue == $term->text) {
            $termInfo = new Zend_Search_Lucene_Index_TermInfo($docFreq, $freqPointer, $proxPointer, $skipOffset);
        } else {
            $termInfo = null;
        }

        // Put loaded termInfo into cache
        $this->_termInfoCache[$termKey] = $termInfo;

        if (count($this->_termInfoCache) == 1024) {
            $this->_cleanUpTermInfoCache();
        }

        return $termInfo;
    }

    /**
     * Returns term freqs array.
     * Result array structure: array(docId => freq, ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param integer $shift
     * @return Zend_Search_Lucene_Index_TermInfo
     */
    public function termFreqs(Zend_Search_Lucene_Index_Term $term, $shift = 0)
    {
        $termInfo = $this->getTermInfo($term);

        if (!$termInfo instanceof Zend_Search_Lucene_Index_TermInfo) {
            return array();
        }

        $frqFile = $this->openCompoundFile('.frq');
        $frqFile->seek($termInfo->freqPointer,SEEK_CUR);
        $result = array();
        $docId = 0;

        for ($count = 0; $count < $termInfo->docFreq; $count++) {
            $docDelta = $frqFile->readVInt();
            if ($docDelta % 2 == 1) {
                $docId += ($docDelta-1)/2;
                $result[$shift + $docId] = 1;
            } else {
                $docId += $docDelta/2;
                $result[$shift + $docId] = $frqFile->readVInt();
            }
        }

        return $result;
    }

    /**
     * Returns term positions array.
     * Result array structure: array(docId => array(pos1, pos2, ...), ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @param integer $shift
     * @return Zend_Search_Lucene_Index_TermInfo
     */
    public function termPositions(Zend_Search_Lucene_Index_Term $term, $shift = 0)
    {
        $termInfo = $this->getTermInfo($term);

        if (!$termInfo instanceof Zend_Search_Lucene_Index_TermInfo) {
            return array();
        }

        $frqFile = $this->openCompoundFile('.frq');
        $frqFile->seek($termInfo->freqPointer,SEEK_CUR);
        $freqs = array();
        $docId = 0;

        for ($count = 0; $count < $termInfo->docFreq; $count++) {
            $docDelta = $frqFile->readVInt();
            if ($docDelta % 2 == 1) {
                $docId += ($docDelta-1)/2;
                $freqs[$docId] = 1;
            } else {
                $docId += $docDelta/2;
                $freqs[$docId] = $frqFile->readVInt();
            }
        }

        $result = array();
        $prxFile = $this->openCompoundFile('.prx');
        $prxFile->seek($termInfo->proxPointer, SEEK_CUR);
        foreach ($freqs as $docId => $freq) {
            $termPosition = 0;
            $positions = array();

            for ($count = 0; $count < $freq; $count++ ) {
                $termPosition += $prxFile->readVInt();
                $positions[] = $termPosition;
            }

            $result[$shift + $docId] = $positions;
        }

        return $result;
    }

    /**
     * Load normalizatin factors from an index file
     *
     * @param integer $fieldNum
     */
    private function _loadNorm($fieldNum)
    {
        $fFile = $this->openCompoundFile('.f' . $fieldNum);
        $this->_norms[$fieldNum] = $fFile->readBytes($this->_docCount);
    }

    /**
     * Returns normalization factor for specified documents
     *
     * @param integer $id
     * @param string $fieldName
     * @return float
     */
    public function norm($id, $fieldName)
    {
        $fieldNum = $this->getFieldNum($fieldName);

        if ( !($this->_fields[$fieldNum]->isIndexed) ) {
            return null;
        }

        if (!isset($this->_norms[$fieldNum])) {
            $this->_loadNorm($fieldNum);
        }

        return Zend_Search_Lucene_Search_Similarity::decodeNorm( ord($this->_norms[$fieldNum]{$id}) );
    }

    /**
     * Returns norm vector, encoded in a byte string
     *
     * @param string $fieldName
     * @return string
     */
    public function normVector($fieldName)
    {
        $fieldNum = $this->getFieldNum($fieldName);

        if ($fieldNum == -1  ||  !($this->_fields[$fieldNum]->isIndexed)) {
            $similarity = Zend_Search_Lucene_Search_Similarity::getDefault();

            return str_repeat(chr($similarity->encodeNorm( $similarity->lengthNorm($fieldName, 0) )),
                              $this->_docCount);
        }

        if (!isset($this->_norms[$fieldNum])) {
            $this->_loadNorm($fieldNum);
        }

        return $this->_norms[$fieldNum];
    }


    /**
     * Returns true if any documents have been deleted from this index segment.
     *
     * @return boolean
     */
    public function hasDeletions()
    {
        return $this->_deleted !== null;
    }


    /**
     * Deletes a document from the index segment.
     * $id is an internal document id
     *
     * @param integer
     */
    public function delete($id)
    {
        $this->_deletedDirty = true;

        if (extension_loaded('bitset')) {
            if ($this->_deleted === null) {
                $this->_deleted = bitset_empty($id);
            }
            bitset_incl($this->_deleted, $id);
        } else {
            if ($this->_deleted === null) {
                $this->_deleted = array();
            }

            $this->_deleted[$id] = 1;
        }
    }

    /**
     * Checks, that document is deleted
     *
     * @param integer
     * @return boolean
     */
    public function isDeleted($id)
    {
        if ($this->_deleted === null) {
            return false;
        }

        if (extension_loaded('bitset')) {
            return bitset_in($this->_deleted, $id);
        } else {
            return isset($this->_deleted[$id]);
        }
    }


    /**
     * Write changes if it's necessary.
     */
    public function writeChanges()
    {
        if (!$this->_deletedDirty) {
            return;
        }

        if (extension_loaded('bitset')) {
            $delBytes = $this->_deleted;
            $bitCount = count(bitset_to_array($delBytes));
        } else {
            $byteCount = floor($this->_docCount/8)+1;
            $delBytes = str_repeat(chr(0), $byteCount);
            for ($count = 0; $count < $byteCount; $count++) {
                $byte = 0;
                for ($bit = 0; $bit < 8; $bit++) {
                    if (isset($this->_deleted[$count*8 + $bit])) {
                        $byte |= (1<<$bit);
                    }
                }
                $delBytes{$count} = chr($byte);
            }
            $bitCount = count($this->_deleted);
        }


        $delFile = $this->_directory->createFile($this->_name . '.del');
        $delFile->writeInt($this->_docCount);
        $delFile->writeInt($bitCount);
        $delFile->writeBytes($delBytes);

        $this->_deletedDirty = false;
    }



    /**
     * Term Dictionary File object for stream like terms reading
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    private $_tisFile = null;

    /**
     * Frequencies File object for stream like terms reading
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    private $_frqFile = null;

    /**
     * Offset of the .frq file in the compound file
     *
     * @var integer
     */
    private $_frqFileOffset;

    /**
     * Positions File object for stream like terms reading
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    private $_prxFile = null;

    /**
     * Offset of the .prx file in the compound file
     *
     * @var integer
     */
    private $_prxFileOffset;


    /**
     * Number of terms in term stream
     *
     * @var integer
     */
    private $_termCount = 0;

    /**
     * Segment skip interval
     *
     * @var integer
     */
    private $_skipInterval;

    /**
     * Last TermInfo in a terms stream
     *
     * @var Zend_Search_Lucene_Index_TermInfo
     */
    private $_lastTermInfo = null;

    /**
     * Last Term in a terms stream
     *
     * @var Zend_Search_Lucene_Index_Term
     */
    private $_lastTerm = null;

    /**
     * Map of the document IDs
     * Used to get new docID after removing deleted documents.
     * It's not very effective from memory usage point of view,
     * but much more faster, then other methods
     *
     * @var array|null
     */
    private $_docMap = null;

    /**
     * An array of all term positions in the documents.
     * Array structure: array( docId => array( pos1, pos2, ...), ...)
     *
     * @var array
     */
    private $_lastTermPositions;

    /**
     * Reset terms stream
     *
     * $startId - id for the fist document
     * $compact - remove deleted documents
     *
     * Returns start document id for the next segment
     *
     * @param integer $startId
     * @param boolean $compact
     * @throws Zend_Search_Lucene_Exception
     * @return integer
     */
    public function reset($startId = 0, $compact = false)
    {
        if ($this->_tisFile !== null) {
            $this->_tisFile = null;
        }

        $this->_tisFile = $this->openCompoundFile('.tis', false);
        $tiVersion = $this->_tisFile->readInt();
        if ($tiVersion != (int)0xFFFFFFFE) {
            throw new Zend_Search_Lucene_Exception('Wrong TermInfoFile file format');
        }

        $this->_termCount    = $this->_tisFile->readLong();
                               $this->_tisFile->readInt();  // Read Index interval
        $this->_skipInterval = $this->_tisFile->readInt();  // Read skip interval

        if ($this->_frqFile !== null) {
            $this->_frqFile = null;
        }
        $this->_frqFile = $this->openCompoundFile('.frq', false);
        $this->_frqFileOffset = $this->_frqFile->tell();

        if ($this->_prxFile !== null) {
            $this->_prxFile = null;
        }
        $this->_prxFile = $this->openCompoundFile('.prx', false);
        $this->_prxFileOffset = $this->_prxFile->tell();

        $this->_lastTerm     = new Zend_Search_Lucene_Index_Term('', -1);
        $this->_lastTermInfo = new Zend_Search_Lucene_Index_TermInfo(0, 0, 0, 0);

        $this->_docMap = array();
        for ($count = 0; $count < $this->_docCount; $count++) {
            if (!$this->isDeleted($count)) {
                $this->_docMap[$count] = $startId + ($compact ? count($this->_docMap) : $count);
            }
        }

        $this->nextTerm();
        return $startId + ($compact ? count($this->_docMap) : $this->_docCount);
    }


    /**
     * Scans terms dictionary and returns next term
     *
     * @return Zend_Search_Lucene_Index_Term|null
     */
    public function nextTerm()
    {
        if ($this->_tisFile === null  ||  $this->_termCount == 0) {
            $this->_lastTerm     = null;
            $this->_lastTermInfo = null;

            // may be necessary for "empty" segment
            $this->_tisFile = null;
            $this->_frqFile = null;
            $this->_prxFile = null;

            return null;
        }

        $termPrefixLength = $this->_tisFile->readVInt();
        $termSuffix       = $this->_tisFile->readString();
        $termFieldNum     = $this->_tisFile->readVInt();
        $termValue        = Zend_Search_Lucene_Index_Term::getPrefix($this->_lastTerm->text, $termPrefixLength) . $termSuffix;

        $this->_lastTerm = new Zend_Search_Lucene_Index_Term($termValue, $this->_fields[$termFieldNum]->name);

        $docFreq     = $this->_tisFile->readVInt();
        $freqPointer = $this->_lastTermInfo->freqPointer + $this->_tisFile->readVInt();
        $proxPointer = $this->_lastTermInfo->proxPointer + $this->_tisFile->readVInt();
        if ($docFreq >= $this->_skipInterval) {
            $skipOffset = $this->_tisFile->readVInt();
        } else {
            $skipOffset = 0;
        }

        $this->_lastTermInfo = new Zend_Search_Lucene_Index_TermInfo($docFreq, $freqPointer, $proxPointer, $skipOffset);


        $this->_lastTermPositions = array();

        $this->_frqFile->seek($this->_lastTermInfo->freqPointer + $this->_frqFileOffset, SEEK_SET);
        $freqs = array();   $docId = 0;
        for( $count = 0; $count < $this->_lastTermInfo->docFreq; $count++ ) {
            $docDelta = $this->_frqFile->readVInt();
            if( $docDelta % 2 == 1 ) {
                $docId += ($docDelta-1)/2;
                $freqs[ $docId ] = 1;
            } else {
                $docId += $docDelta/2;
                $freqs[ $docId ] = $this->_frqFile->readVInt();
            }
        }

        $this->_prxFile->seek($this->_lastTermInfo->proxPointer + $this->_prxFileOffset, SEEK_SET);
        foreach ($freqs as $docId => $freq) {
            $termPosition = 0;  $positions = array();

            for ($count = 0; $count < $freq; $count++ ) {
                $termPosition += $this->_prxFile->readVInt();
                $positions[] = $termPosition;
            }

            if (isset($this->_docMap[$docId])) {
                $this->_lastTermPositions[$this->_docMap[$docId]] = $positions;
            }
        }


        $this->_termCount--;
        if ($this->_termCount == 0) {
            $this->_tisFile = null;
            $this->_frqFile = null;
            $this->_prxFile = null;
        }

        return $this->_lastTerm;
    }


    /**
     * Returns term in current position
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return Zend_Search_Lucene_Index_Term|null
     */
    public function currentTerm()
    {
        return $this->_lastTerm;
    }


    /**
     * Returns an array of all term positions in the documents.
     * Return array structure: array( docId => array( pos1, pos2, ...), ...)
     *
     * @return array
     */
    public function currentTermPositions()
    {
        return $this->_lastTermPositions;
    }
}

