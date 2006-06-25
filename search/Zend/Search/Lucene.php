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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';

/** Zend_Search_Lucene_Document */
require_once 'Zend/Search/Lucene/Document.php';

/** Zend_Search_Lucene_Storage_Directory */
require_once 'Zend/Search/Lucene/Storage/Directory/Filesystem.php';

/** Zend_Search_Lucene_Index_Term */
require_once 'Zend/Search/Lucene/Index/Term.php';

/** Zend_Search_Lucene_Index_TermInfo */
require_once 'Zend/Search/Lucene/Index/TermInfo.php';

/** Zend_Search_Lucene_Index_SegmentInfo */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';

/** Zend_Search_Lucene_Index_FieldInfo */
require_once 'Zend/Search/Lucene/Index/FieldInfo.php';

/** Zend_Search_Lucene_Index_Writer */
require_once 'Zend/Search/Lucene/Index/Writer.php';

/** Zend_Search_Lucene_Search_QueryParser */
require_once 'Zend/Search/Lucene/Search/QueryParser.php';

/** Zend_Search_Lucene_Search_QueryHit */
require_once 'Zend/Search/Lucene/Search/QueryHit.php';

/** Zend_Search_Lucene_Search_Similarity */
require_once 'Zend/Search/Lucene/Search/Similarity.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Search_Lucene
{
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

        if ($create) {
            $this->_writer = new Zend_Search_Lucene_Index_Writer($this->_directory, true);
        } else {
            $this->_writer = null;
        }

        $this->_segmentInfos = array();

        $segmentsFile = $this->_directory->getFileObject('segments');

        $format = $segmentsFile->readInt();

        if ($format != (int)0xFFFFFFFF) {
            throw new Zend_Search_Lucene_Exception('Wrong segments file format');
        }

        // read version
        $segmentsFile->readLong();

        // read counter
        $segmentsFile->readInt();

        $segments = $segmentsFile->readInt();

        $this->_docCount = 0;

        // read segmentInfos
        for ($count = 0; $count < $segments; $count++) {
            $segName = $segmentsFile->readString();
            $segSize = $segmentsFile->readInt();
            $this->_docCount += $segSize;

            $this->_segmentInfos[$count] =
                                new Zend_Search_Lucene_Index_SegmentInfo($segName,
                                                                         $segSize,
                                                                         $this->_directory);
        }
    }


    /**
     * Object destructor
     */
    public function __destruct()
    {
        $this->commit();

        if ($this->_closeDirOnExit) {
            $this->_directory->close();
        }
    }

    /**
     * Returns an instance of Zend_Search_Lucene_Index_Writer for the index
     *
     * @return Zend_Search_Lucene_Index_Writer
     */
    public function getIndexWriter()
    {
        if (!$this->_writer instanceof Zend_Search_Lucene_Index_Writer) {
            $this->_writer = new Zend_Search_Lucene_Index_Writer($this->_directory);
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
     * Returns the total number of documents in this index.
     *
     * @return integer
     */
    public function count()
    {
        return $this->_docCount;
    }


    /**
     * Performs a query against the index and returns an array
     * of Zend_Search_Lucene_Search_QueryHit objects.
     * Input is a string or Zend_Search_Lucene_Search_Query.
     *
     * @param mixed $query
     * @return array ZSearchHit
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

        $hits = array();
        $scores = array();

        $docNum = $this->count();
        for( $count=0; $count < $docNum; $count++ ) {
            $docScore = $query->score( $count, $this);
            if( $docScore != 0 ) {
                $hit = new Zend_Search_Lucene_Search_QueryHit($this);
                $hit->id = $count;
                $hit->score = $docScore;

                $hits[] = $hit;
                $scores[] = $docScore;
            }
        }
        array_multisort($scores, SORT_DESC, SORT_REGULAR, $hits);

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

        $segCount = 0;
        $nextSegmentStartId = $this->_segmentInfos[ 0 ]->count();
        while( $nextSegmentStartId <= $id ) {
               $segCount++;
               $nextSegmentStartId += $this->_segmentInfos[ $segCount ]->count();
        }
        $segmentStartId = $nextSegmentStartId - $this->_segmentInfos[ $segCount ]->count();

        $fdxFile = $this->_segmentInfos[ $segCount ]->openCompoundFile('.fdx');
        $fdxFile->seek( ($id-$segmentStartId)*8, SEEK_CUR );
        $fieldValuesPosition = $fdxFile->readLong();

        $fdtFile = $this->_segmentInfos[ $segCount ]->openCompoundFile('.fdt');
        $fdtFile->seek( $fieldValuesPosition, SEEK_CUR );
        $fieldCount = $fdtFile->readVInt();

        $doc = new Zend_Search_Lucene_Document();
        for( $count = 0; $count < $fieldCount; $count++ ) {
            $fieldNum = $fdtFile->readVInt();
            $bits = $fdtFile->readByte();

            $fieldInfo = $this->_segmentInfos[ $segCount ]->getField($fieldNum);

            if( !($bits & 2) ) { // Text data
                $field = new Zend_Search_Lucene_Field($fieldInfo->name,
                                                      $fdtFile->readString(),
                                                      true,
                                                      $fieldInfo->isIndexed,
                                                      $bits & 1 );
            } else {
                $field = new Zend_Search_Lucene_Field($fieldInfo->name,
                                                      $fdtFile->readBinary(),
                                                      true,
                                                      $fieldInfo->isIndexed,
                                                      $bits & 1 );
            }

            $doc->addField($field);
        }

        return $doc;
    }


    /**
     * Returns an array of all the documents which contain term.
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
     * Returns an array of all term positions in the documents.
     * Return array structure: array( docId => array( pos1, pos2, ...), ...)
     *
     * @param Zend_Search_Lucene_Index_Term $term
     * @return array
     */
    public function termPositions(Zend_Search_Lucene_Index_Term $term)
    {
        $result = array();
        $segmentStartDocId = 0;
        foreach( $this->_segmentInfos as $segInfo ) {
            $termInfo = $segInfo->getTermInfo($term);

            if (!$termInfo instanceof Zend_Search_Lucene_Index_TermInfo) {
                $segmentStartDocId += $segInfo->count();
                continue;
            }

            $frqFile = $segInfo->openCompoundFile('.frq');
            $frqFile->seek($termInfo->freqPointer,SEEK_CUR);
            $freqs = array();
            $docId = 0;

            for( $count = 0; $count < $termInfo->docFreq; $count++ ) {
                $docDelta = $frqFile->readVInt();
                if( $docDelta % 2 == 1 ) {
                    $docId += ($docDelta-1)/2;
                    $freqs[ $docId ] = 1;
                } else {
                    $docId += $docDelta/2;
                    $freqs[ $docId ] = $frqFile->readVInt();
                }
            }

            $prxFile = $segInfo->openCompoundFile('.prx');
            $prxFile->seek($termInfo->proxPointer,SEEK_CUR);
            foreach ($freqs as $docId => $freq) {
                $termPosition = 0;
                $positions = array();

                for ($count = 0; $count < $freq; $count++ ) {
                    $termPosition += $prxFile->readVInt();
                    $positions[] = $termPosition;
                }

                $result[ $segmentStartDocId + $docId ] = $positions;
            }

            $segmentStartDocId += $segInfo->count();
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
     * @return Zend_Search_Lucene_Document
     */
    public function norm( $id, $fieldName )
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

        $segCount = 0;
        $nextSegmentStartId = $this->_segmentInfos[ 0 ]->count();
        while( $nextSegmentStartId <= $id ) {
               $segCount++;
               $nextSegmentStartId += $this->_segmentInfos[ $segCount ]->count();
        }

        $this->_hasChanges = true;
        $segmentStartId = $nextSegmentStartId - $this->_segmentInfos[ $segCount ]->count();
        $this->_segmentInfos[ $segCount ]->delete($id - $segmentStartId);
    }



    /**
     * Adds a document to this index.
     *
     * @param Zend_Search_Lucene_Document $document
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        if (!$this->_writer instanceof Zend_Search_Lucene_Index_Writer) {
            $this->_writer = new Zend_Search_Lucene_Index_Writer($this->_directory);
        }

        $this->_writer->addDocument($document);
    }


    /**
     * Commit changes resulting from delete() or undeleteAll() operations.
     *
     * @todo delete() and undeleteAll processing.
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
            foreach ($this->_writer->commit() as $segmentName => $segmentInfo) {
                if ($segmentInfo !== null) {
                    $this->_segmentInfos[] = $segmentInfo;
                    $this->_docCount += $segmentInfo->count();
                } else {
                    foreach ($this->_segmentInfos as $segId => $segInfo) {
                        if ($segInfo->getName() == $segmentName) {
                            unset($this->_segmentInfos[$segId]);
                        }
                    }
                }
            }
        }
    }


    /*************************************************************************
    @todo UNIMPLEMENTED
    *************************************************************************/

    /**
     * Returns an array of all terms in this index.
     *
     * @todo Implementation
     * @return array
     */
    public function terms()
    {
        return array();
    }


    /**
     * Undeletes all documents currently marked as deleted in this index.
     *
     * @todo Implementation
     */
    public function undeleteAll()
    {}
}