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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Document */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Document.php';

/** Zend_Search_Lucene_Document_Html */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Document/Html.php';

/** Zend_Search_Lucene_Storage_Directory */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Storage/Directory/Filesystem.php';

/** Zend_Search_Lucene_Storage_File_Memory */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Storage/File/Memory.php';

/** Zend_Search_Lucene_Index_Term */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/Term.php';

/** Zend_Search_Lucene_Index_TermInfo */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/TermInfo.php';

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/SegmentInfo.php';

/** Zend_Search_Lucene_Index_FieldInfo */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/FieldInfo.php';

/** Zend_Search_Lucene_Index_Writer */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/Writer.php';

/** Zend_Search_Lucene_Search_QueryParser */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Search/QueryParser.php';

/** Zend_Search_Lucene_Search_QueryHit */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Search/QueryHit.php';

/** Zend_Search_Lucene_Search_Similarity */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Search/Similarity.php';

/** Zend_Search_Lucene_Index_SegmentInfoPriorityQueue */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Index/SegmentInfoPriorityQueue.php';


/** Zend_Search_Lucene_Interface */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Interface.php';

/** Zend_Search_Lucene_Proxy */
require_once $CFG->dirroot.'/search/Zend/Search/Lucene/Proxy.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene implements Zend_Search_Lucene_Interface
{
    /**
     * Default field name for search
     *
     * Null means search through all fields
     *
     * @var string
     */
    private static $_defaultSearchField = null;

    /**
     * File system adapter.
     *
     * @var Zend_Search_Lucene_Storage_Directory
     */
    private $_directory = null;

    /**
     * File system adapter closing option
     *
     * @var boolean
     */
    private $_closeDirOnExit = true;

    /**
     * Writer for this index, not instantiated unless required.
     *
     * @var Zend_Search_Lucene_Index_Writer
     */
    private $_writer = null;

    /**
     * Array of Zend_Search_Lucene_Index_SegmentInfo objects for this index.
     *
     * @var array Zend_Search_Lucene_Index_SegmentInfo
     */
    private $_segmentInfos = array();

    /**
     * Number of documents in this index.
     *
     * @var integer
     */
    private $_docCount = 0;

    /**
     * Flag for index changes
     *
     * @var boolean
     */
    private $_hasChanges = false;


    /**
     * Index lock object
     *
     * @var Zend_Search_Lucene_Storage_File
     */
    private $_lock;

    /**
     * Signal, that index is already closed, changes are fixed and resources are cleaned up
     *
     * @var boolean
     */
    private $_closed = false;

    /**
     * Number of references to the index object
     *
     * @var integer
     */
    private $_refCount = 0;


    /**
     * Create index
     *
     * @param mixed $directory
     * @return Zend_Search_Lucene_Interface
     */
    public static function create($directory)
    {
        return new Zend_Search_Lucene_Proxy(new Zend_Search_Lucene($directory, true));
    }

    /**
     * Open index
     *
     * @param mixed $directory
     * @return Zend_Search_Lucene_Interface
     */
    public static function open($directory)
    {
        return new Zend_Search_Lucene_Proxy(new Zend_Search_Lucene($directory, false));
    }

    /**
     * Opens the index.
     *
     * IndexReader constructor needs Directory as a parameter. It should be
     * a string with a path to the index folder or a Directory object.
     *
     * @param mixed $directory
     * @throws Zend_Search_Lucene_Exception
     */
    public function __construct($directory = null, $create = false)
    {
        if ($directory === null) {
            throw new Zend_Search_Exception('No index directory specified');
        }

        if ($directory instanceof Zend_Search_Lucene_Storage_Directory_Filesystem) {
            $this->_directory      = $directory;
            $this->_closeDirOnExit = false;
        } else {
            $this->_directory      = new Zend_Search_Lucene_Storage_Directory_Filesystem($directory);
            $this->_closeDirOnExit = true;
        }


        // Get a shared lock to the index
        $this->_lock = $this->_directory->createFile('index.lock');

        $this->_segmentInfos = array();

        if ($create) {
            // Throw an exception if index is under processing now
            if (!$this->_lock->lock(LOCK_EX, true)) {
                throw new Zend_Search_Lucene_Exception('Can\'t create index. It\'s under processing now');
            }

            // Writer will create segments file for empty segments list
            $this->_writer = new Zend_Search_Lucene_Index_Writer($this->_directory, $this->_segmentInfos, true);

            if (!$this->_lock->lock(LOCK_SH)) {
                throw new Zend_Search_Lucene_Exception('Can\'t reduce lock level from Exclusive to Shared');
            }
        } else {
            // Wait if index is under switching from one set of segments to another (Index_Writer::_updateSegments())
            if (!$this->_lock->lock(LOCK_SH)) {
                throw new Zend_Search_Lucene_Exception('Can\'t obtain shared index lock');
            }
            $this->_writer = null;
        }


        $segmentsFile = $this->_directory->getFileObject('segments');

        $format = $segmentsFile->readInt();

        if ($format != (int)0xFFFFFFFF) {
            throw new Zend_Search_Lucene_Exception('Wrong segments file format');
        }

        // read version
        // $segmentsFile->readLong();
        $segmentsFile->readInt(); $segmentsFile->readInt();

        // read segment name counter
        $segmentsFile->readInt();

        $segments = $segmentsFile->readInt();

        $this->_docCount = 0;

        // read segmentInfos
        for ($count = 0; $count < $segments; $count++) {
            $segName = $segmentsFile->readString();
            $segSize = $segmentsFile->readInt();
            $this->_docCount += $segSize;

            $this->_segmentInfos[] =
                                new Zend_Search_Lucene_Index_SegmentInfo($segName,
                                                                         $segSize,
                                                                         $this->_directory);
        }
    }

    /**
     * Close current index and free resources
     */
    private function _close()
    {
        if ($this->_closed) {
            // index is already closed and resources are cleaned up
            return;
        }

        $this->commit();

        // Free shared lock
        $this->_lock->unlock();

        if ($this->_closeDirOnExit) {
            $this->_directory->close();
        }

        $this->_directory    = null;
        $this->_writer       = null;
        $this->_segmentInfos = null;

        $this->_closed = true;
    }

    /**
     * Add reference to the index object
     *
     * @internal
     */
    public function addReference()
    {
        $this->_refCount++;
    }

    /**
     * Remove reference from the index object
     *
     * When reference count becomes zero, index is closed and resources are cleaned up
     *
     * @internal
     */
    public function removeReference()
    {
        $this->_refCount--;

        if ($this->_refCount == 0) {
            $this->_close();
        }
    }

    /**
     * Object destructor
     */
    public function __destruct()
    {
        $this->_close();
    }

    /**
     * Returns an instance of Zend_Search_Lucene_Index_Writer for the index
     *
     * @internal
     * @return Zend_Search_Lucene_Index_Writer
     */
    public function getIndexWriter()
    {
        if (!$this->_writer instanceof Zend_Search_Lucene_Index_Writer) {
            $this->_writer = new Zend_Search_Lucene_Index_Writer($this->_directory, $this->_segmentInfos);
        }

        return $this->_writer;
    }


    /**
     * Returns the Zend_Search_Lucene_Storage_Directory instance for this index.
     *
     * @return Zend_Search_Lucene_Storage_Directory
     */
    public function getDirectory()
    {
        return $this->_directory;
    }


    /**
     * Returns the total number of documents in this index (including deleted documents).
     *
     * @return integer
     */
    public function count()
    {
        return $this->_docCount;
    }

    /**
     * Returns one greater than the largest possible document number.
     * This may be used to, e.g., determine how big to allocate a structure which will have
     * an element for every document number in an index.
     *
     * @return integer
     */
    public function maxDoc()
    {
        return $this->count();
    }

    /**
     * Returns the total number of non-deleted documents in this index.
     *
     * @return integer
     */
    public function numDocs()
    {
        $numDocs = 0;

        foreach ($this->_segmentInfos as $segmentInfo) {
            $numDocs += $segmentInfo->numDocs();
        }

        return $numDocs;
    }

    /**
     * Checks, that document is deleted
     *
     * @param integer $id
     * @return boolean
     * @throws Zend_Search_Lucene_Exception    Exception is thrown if $id is out of the range
     */
    public function isDeleted($id)
    {
        if ($id >= $this->_docCount) {
            throw new Zend_Search_Lucene_Exception('Document id is out of the range.');
        }

        $segmentStartId = 0;
        foreach ($this->_segmentInfos as $segmentInfo) {
            if ($segmentStartId + $segmentInfo->count() > $id) {
                break;
            }

            $segmentStartId += $segmentInfo->count();
        }

        return $segmentInfo->isDeleted($id - $segmentStartId);
    }

    /**
     * Set default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * Default value is null
     *
     * @param string $fieldName
     */
    public static function setDefaultSearchField($fieldName)
    {
        self::$_defaultSearchField = $fieldName;
    }

    /**
     * Get default search field.
     *
     * Null means, that search is performed through all fields by default
     *
     * @return string
     */
    public static function getDefaultSearchField()
    {
        return self::$_defaultSearchField;
    }

    /**
     * Retrieve index maxBufferedDocs option
     *
     * maxBufferedDocs is a minimal number of documents required before
     * the buffered in-memory documents are written into a new Segment
     *
     * Default value is 10
     *
     * @return integer
     */
    public function getMaxBufferedDocs()
    {
        return $this->getIndexWriter()->maxBufferedDocs;
    }

    /**
     * Set index maxBufferedDocs option
     *
     * maxBufferedDocs is a minimal number of documents required before
     * the buffered in-memory documents are written into a new Segment
     *
     * Default value is 10
     *
     * @param integer $maxBufferedDocs
     */
    public function setMaxBufferedDocs($maxBufferedDocs)
    {
        $this->getIndexWriter()->maxBufferedDocs = $maxBufferedDocs;
    }

    /**
     * Retrieve index maxMergeDocs option
     *
     * maxMergeDocs is a largest number of documents ever merged by addDocument().
     * Small values (e.g., less than 10,000) are best for interactive indexing,
     * as this limits the length of pauses while indexing to a few seconds.
     * Larger values are best for batched indexing and speedier searches.
     *
     * Default value is PHP_INT_MAX
     *
     * @return integer
     */
    public function getMaxMergeDocs()
    {
        return $this->getIndexWriter()->maxMergeDocs;
    }

    /**
     * Set index maxMergeDocs option
     *
     * maxMergeDocs is a largest number of documents ever merged by addDocument().
     * Small values (e.g., less than 10,000) are best for interactive indexing,
     * as this limits the length of pauses while indexing to a few seconds.
     * Larger values are best for batched indexing and speedier searches.
     *
     * Default value is PHP_INT_MAX
     *
     * @param integer $maxMergeDocs
     */
    public function setMaxMergeDocs($maxMergeDocs)
    {
        $this->getIndexWriter()->maxMergeDocs = $maxMergeDocs;
    }

    /**
     * Retrieve index mergeFactor option
     *
     * mergeFactor determines how often segment indices are merged by addDocument().
     * With smaller values, less RAM is used while indexing,
     * and searches on unoptimized indices are faster,
     * but indexing speed is slower.
     * With larger values, more RAM is used during indexing,
     * and while searches on unoptimized indices are slower,
     * indexing is faster.
     * Thus larger values (> 10) are best for batch index creation,
     * and smaller values (< 10) for indices that are interactively maintained.
     *
     * Default value is 10
     *
     * @return integer
     */
    public function getMergeFactor()
    {
        return $this->getIndexWriter()->mergeFactor;
    }

    /**
     * Set index mergeFactor option
     *
     * mergeFactor determines how often segment indices are merged by addDocument().
     * With smaller values, less RAM is used while indexing,
     * and searches on unoptimized indices are faster,
     * but indexing speed is slower.
     * With larger values, more RAM is used during indexing,
     * and while searches on unoptimized indices are slower,
     * indexing is faster.
     * Thus larger values (> 10) are best for batch index creation,
     * and smaller values (< 10) for indices that are interactively maintained.
     *
     * Default value is 10
     *
     * @param integer $maxMergeDocs
     */
    public function setMergeFactor($mergeFactor)
    {
        $this->getIndexWriter()->mergeFactor = $mergeFactor;
    }

    /**
     * Performs a query against the index and returns an array
     * of Zend_Search_Lucene_Search_QueryHit objects.
     * Input is a string or Zend_Search_Lucene_Search_Query.
     *
     * @param mixed $query
     * @return array Zend_Search_Lucene_Search_QueryHit
     * @throws Zend_Search_Lucene_Exception
     */
    public function find($query)
    {
        if (is_string($query)) {
            $query = Zend_Search_Lucene_Search_QueryParser::parse($query);
        }

        if (!$query instanceof Zend_Search_Lucene_Search_Query) {
            throw new Zend_Search_Lucene_Exception('Query must be a string or Zend_Search_Lucene_Search_Query object');
        }

        $this->commit();

        $hits   = array();
        $scores = array();
        $ids    = array();

        $query = $query->rewrite($this)->optimize($this);

        $query->execute($this);

        $topScore = 0;

        foreach ($query->matchedDocs() as $id => $num) {
            $docScore = $query->score($id, $this);
            if( $docScore != 0 ) {
                $hit = new Zend_Search_Lucene_Search_QueryHit($this);
                $hit->id = $id;
                $hit->score = $docScore;

                $hits[]   = $hit;
                $ids[]    = $id;
                $scores[] = $docScore;

                if ($docScore > $topScore) {
                    $topScore = $docScore;
                }
            }
        }

        if (count($hits) == 0) {
            // skip sorting, which may cause a error on empty index
        	return array();
        }

        if ($topScore > 1) {
            foreach ($hits as $hit) {
                $hit->score /= $topScore;
            }
        }

        if (func_num_args() == 1) {
            // sort by scores
            array_multisort($scores, SORT_DESC, SORT_NUMERIC,
                            $ids,    SORT_ASC,  SORT_NUMERIC,
                            $hits);
        } else {
            // sort by given field names

            $argList    = func_get_args();
            $fieldNames = $this->getFieldNames();
            $sortArgs   = array();

            for ($count = 1; $count < count($argList); $count++) {
                $fieldName = $argList[$count];

                if (!is_string($fieldName)) {
                    throw new Zend_Search_Lucene_Exception('Field name must be a string.');
                }

                if (!in_array($fieldName, $fieldNames)) {
                    throw new Zend_Search_Lucene_Exception('Wrong field name.');
                }

                $valuesArray = array();
                foreach ($hits as $hit) {
                    try {
                        $value = $hit->getDocument()->getFieldValue($fieldName);
                    } catch (Zend_Search_Lucene_Exception $e) {
                        if (strpos($e->getMessage(), 'not found') === false) {
                            throw $e;
                        } else {
                            $value = null;
                        }
                    }

                    $valuesArray[] = $value;
                }

                $sortArgs[] = $valuesArray;

                if ($count + 1 < count($argList)  &&  is_integer($argList[$count+1])) {
                    $count++;
                    $sortArgs[] = $argList[$count];

                    if ($count + 1 < count($argList)  &&  is_integer($argList[$count+1])) {
                        $count++;
                        $sortArgs[] = $argList[$count];
                    } else {
                        if ($argList[$count] == SORT_ASC  || $argList[$count] == SORT_DESC) {
                            $sortArgs[] = SORT_REGULAR;
                        } else {
                            $sortArgs[] = SORT_ASC;
                        }
                    }
                } else {
                    $sortArgs[] = SORT_ASC;
                    $sortArgs[] = SORT_REGULAR;
                }
            }

            // Sort by id's if values are equal
            $sortArgs[] = $ids;
            $sortArgs[] = SORT_ASC;
            $sortArgs[] = SORT_NUMERIC;

            // Array to be sorted
            $sortArgs[] = &$hits;

            // Do sort
            call_user_func_array('array_multisort', $sortArgs);
        }

        return $hits;
    }


    /**
     * Returns a list of all unique field names that exist in this index.
     *
     * @param boolean $indexed
     * @return array
     */
    public function getFieldNames($indexed = false)
    {
        $result = array();
        foreach( $this->_segmentInfos as $segmentInfo ) {
            $result = array_merge($result, $segmentInfo->getFields($indexed));
        }
        return $result;
    }


    /**
     * Returns a Zend_Search_Lucene_Document object for the document
     * number $id in this index.
     *
     * @param integer|Zend_Search_Lucene_Search_QueryHit $id
     * @return Zend_Search_Lucene_Document
     */
    public function getDocument($id)
    {
        if ($id instanceof Zend_Search_Lucene_Search_QueryHit) {
            /* @var $id Zend_Search_Lucene_Search_QueryHit */
            $id = $id->id;
        }

        if ($id >= $this->_docCount) {
            throw new Zend_Search_Lucene_Exception('Document id is out of the range.');
        }

        $segmentStartId = 0;
        foreach ($this->_segmentInfos as $segmentInfo) {
            if ($segmentStartId + $segmentInfo->count() > $id) {
                break;
            }

            $segmentStartId += $segmentInfo->count();
        }

        $fdxFile = $segmentInfo->openCompoundFile('.fdx');
        $fdxFile->seek( ($id-$segmentStartId)*8, SEEK_CUR );
        $fieldValuesPosition = $fdxFile->readLong();

        $fdtFile = $segmentInfo->openCompoundFile('.fdt');
        $fdtFile->seek($fieldValuesPosition, SEEK_CUR);
        $fieldCount = $fdtFile->readVInt();

        $doc = new Zend_Search_Lucene_Document();
        for ($count = 0; $count < $fieldCount; $count++) {
            $fieldNum = $fdtFile->readVInt();
            $bits = $fdtFile->readByte();

            $fieldInfo = $segmentInfo->getField($fieldNum);

            if (!($bits & 2)) { // Text data
                $field = new Zend_Search_Lucene_Field($fieldInfo->name,
                                                      $fdtFile->readString(),
                                                      'UTF-8',
                                                      true,
                                                      $fieldInfo->isIndexed,
                                                      $bits & 1 );
            } else {            // Binary data
                $field = new Zend_Search_Lucene_Field($fieldInfo->name,
                                                      $fdtFile->readBinary(),
                                                      '',
                                                      true,
                                                      $fieldInfo->isIndexed,
                                                      $bits & 1,
                                                      true );
            }

            $doc->addField($field);
        }

        return $doc;
    }


    /**
     * Returns true if index contain documents with specified term.
     *
     * Is used for query optimization.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return boolean
     */
    public function hasTerm(Zend_Search_Lucene_Index_Term $term)
    {
        foreach ($this->_segmentInfos as $segInfo) {
            if ($segInfo->getTermInfo($term) instanceof Zend_Search_Lucene_Index_TermInfo) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns IDs of all the documents containing term.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return array
     */
    public function termDocs(Zend_Search_Lucene_Index_Term $term)
    {
        $result = array();
        $segmentStartDocId = 0;

        foreach ($this->_segmentInfos as $segInfo) {
            $termInfo = $segInfo->getTermInfo($term);

            if (!$termInfo instanceof Zend_Search_Lucene_Index_TermInfo) {
                $segmentStartDocId += $segInfo->count();
                continue;
            }

            $frqFile = $segInfo->openCompoundFile('.frq');
            $frqFile->seek($termInfo->freqPointer,SEEK_CUR);
            $docId = 0;
            for( $count=0; $count < $termInfo->docFreq; $count++ ) {
                $docDelta = $frqFile->readVInt();
                if( $docDelta % 2 == 1 ) {
                    $docId += ($docDelta-1)/2;
                } else {
                    $docId += $docDelta/2;
                    // read freq
                    $frqFile->readVInt();
                }

                $result[] = $segmentStartDocId + $docId;
            }

            $segmentStartDocId += $segInfo->count();
        }

        return $result;
    }


    /**
     * Returns an array of all term freqs.
     * Result array structure: array(docId => freq, ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return integer
     */
    public function termFreqs(Zend_Search_Lucene_Index_Term $term)
    {
        $result = array();
        $segmentStartDocId = 0;
        foreach ($this->_segmentInfos as $segmentInfo) {
            $result += $segmentInfo->termFreqs($term, $segmentStartDocId);

            $segmentStartDocId += $segmentInfo->count();
        }

        return $result;
    }

    /**
     * Returns an array of all term positions in the documents.
     * Result array structure: array(docId => array(pos1, pos2, ...), ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return array
     */
    public function termPositions(Zend_Search_Lucene_Index_Term $term)
    {
        $result = array();
        $segmentStartDocId = 0;
        foreach ($this->_segmentInfos as $segmentInfo) {
            $result += $segmentInfo->termPositions($term, $segmentStartDocId);

            $segmentStartDocId += $segmentInfo->count();
        }

        return $result;
    }


    /**
     * Returns the number of documents in this index containing the $term.
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return integer
     */
    public function docFreq(Zend_Search_Lucene_Index_Term $term)
    {
        $result = 0;
        foreach ($this->_segmentInfos as $segInfo) {
            $termInfo = $segInfo->getTermInfo($term);
            if ($termInfo !== null) {
                $result += $termInfo->docFreq;
            }
        }

        return $result;
    }


    /**
     * Retrive similarity used by index reader
     *
     * @return Zend_Search_Lucene_Search_Similarity
     */
    public function getSimilarity()
    {
        return Zend_Search_Lucene_Search_Similarity::getDefault();
    }


    /**
     * Returns a normalization factor for "field, document" pair.
     *
     * @param integer $id
     * @param string $fieldName
     * @return float
     */
    public function norm($id, $fieldName)
    {
        if ($id >= $this->_docCount) {
            return null;
        }

        $segmentStartId = 0;
        foreach ($this->_segmentInfos as $segInfo) {
            if ($segmentStartId + $segInfo->count() > $id) {
                break;
            }

            $segmentStartId += $segInfo->count();
        }

        if ($segInfo->isDeleted($id - $segmentStartId)) {
            return 0;
        }

        return $segInfo->norm($id - $segmentStartId, $fieldName);
    }

    /**
     * Returns true if any documents have been deleted from this index.
     *
     * @return boolean
     */
    public function hasDeletions()
    {
        foreach ($this->_segmentInfos as $segmentInfo) {
            if ($segmentInfo->hasDeletions()) {
                return true;
            }
        }

        return false;
    }


    /**
     * Deletes a document from the index.
     * $id is an internal document id
     *
     * @param integer|Zend_Search_Lucene_Search_QueryHit $id
     * @throws Zend_Search_Lucene_Exception
     */
    public function delete($id)
    {
        if ($id instanceof Zend_Search_Lucene_Search_QueryHit) {
            /* @var $id Zend_Search_Lucene_Search_QueryHit */
            $id = $id->id;
        }

        if ($id >= $this->_docCount) {
            throw new Zend_Search_Lucene_Exception('Document id is out of the range.');
        }

        $segmentStartId = 0;
        foreach ($this->_segmentInfos as $segmentInfo) {
            if ($segmentStartId + $segmentInfo->count() > $id) {
                break;
            }

            $segmentStartId += $segmentInfo->count();
        }
        $segmentInfo->delete($id - $segmentStartId);

        $this->_hasChanges = true;
    }



    /**
     * Adds a document to this index.
     *
     * @param Zend_Search_Lucene_Document $document
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        $this->getIndexWriter()->addDocument($document);
        $this->_docCount++;
    }


    /**
     * Update document counter
     */
    private function _updateDocCount()
    {
        $this->_docCount = 0;
        foreach ($this->_segmentInfos as $segInfo) {
            $this->_docCount += $segInfo->count();
        }
    }

    /**
     * Commit changes resulting from delete() or undeleteAll() operations.
     *
     * @todo undeleteAll processing.
     */
    public function commit()
    {
        if ($this->_hasChanges) {
            foreach ($this->_segmentInfos as $segInfo) {
                $segInfo->writeChanges();
            }

            $this->_hasChanges = false;
        }

        if ($this->_writer !== null) {
            $this->_writer->commit();

            $this->_updateDocCount();
        }
    }


    /**
     * Optimize index.
     *
     * Merges all segments into one
     */
    public function optimize()
    {
        // Commit changes if any changes have been made
        $this->commit();

        if (count($this->_segmentInfos) > 1 || $this->hasDeletions()) {
            $this->getIndexWriter()->optimize();
            $this->_updateDocCount();
        }
    }


    /**
     * Returns an array of all terms in this index.
     *
     * @return array
     */
    public function terms()
    {
        $result = array();

        $segmentInfoQueue = new Zend_Search_Lucene_Index_SegmentInfoPriorityQueue();

        foreach ($this->_segmentInfos as $segmentInfo) {
            $segmentInfo->reset();

            // Skip "empty" segments
            if ($segmentInfo->currentTerm() !== null) {
                $segmentInfoQueue->put($segmentInfo);
            }
        }

        while (($segmentInfo = $segmentInfoQueue->pop()) !== null) {
            if ($segmentInfoQueue->top() === null ||
                $segmentInfoQueue->top()->currentTerm()->key() !=
                            $segmentInfo->currentTerm()->key()) {
                // We got new term
                $result[] = $segmentInfo->currentTerm();
            }

            $segmentInfo->nextTerm();
            // check, if segment dictionary is finished
            if ($segmentInfo->currentTerm() !== null) {
                // Put segment back into the priority queue
                $segmentInfoQueue->put($segmentInfo);
            }
        }

        return $result;
    }


    /*************************************************************************
    @todo UNIMPLEMENTED
    *************************************************************************/
    /**
     * Undeletes all documents currently marked as deleted in this index.
     *
     * @todo Implementation
     */
    public function undeleteAll()
    {}
}
